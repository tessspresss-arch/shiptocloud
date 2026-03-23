import { expect, test, type Page } from '@playwright/test';
import { ensurePhpFixture } from '../helpers/fixtures';

const credentialCandidates = [
  {
    email: process.env.E2E_ADMIN_EMAIL ?? 'admin@medisys.test',
    password: process.env.E2E_ADMIN_PASSWORD ?? 'password',
  },
  {
    email: 'admin@cabinet.com',
    password: '1234',
  },
  {
    email: 'admin@example.com',
    password: 'password123',
  },
];

let consultationId = '0';

async function loginWithFallback(page: Page) {
  for (const credentials of credentialCandidates) {
    await page.goto('/login');
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await page.fill('input[name="email"]', credentials.email);
    await page.fill('input[name="password"]', credentials.password);
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle').catch(() => undefined);

    if (!page.url().includes('/login')) {
      return;
    }
  }

  throw new Error('Aucune combinaison d identifiants E2E n a permis de se connecter.');
}

async function expectNoPageOverflow(page: Page) {
  const hasOverflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth > 8);
  expect(hasOverflow).toBeFalsy();
}

test.describe('Validation UI consultations', () => {
  test.beforeAll(() => {
    const fixture = ensurePhpFixture<{ consultation_id: number }>('ensure_e2e_consultations.php');
    consultationId = String(fixture.consultation_id);
  });

  test('index, create, show et edit restent lisibles avec une fixture consultation', async ({ page }) => {
    await loginWithFallback(page);

    const viewports = [
      { label: 'desktop', width: 1440, height: 900 },
      { label: 'tablet', width: 1024, height: 768 },
      { label: 'mobile', width: 390, height: 844 },
    ];

    for (const viewport of viewports) {
      await test.step(`Validation ${viewport.label}`, async () => {
        await page.setViewportSize({ width: viewport.width, height: viewport.height });

        await page.goto('/consultations');
        await expect(page.locator('body')).toContainText('Gestion des Consultations');
        await expect(page.locator('input[name="search"]')).toBeVisible();
        await expect(page.locator('a[href$="/consultations/create"]').first()).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto('/consultations/create');
        await expect(page.locator('.cc-eyebrow').filter({ hasText: /Nouveau parcours/i })).toBeVisible();
        await expect(page.locator('#consultationCreateForm')).toBeVisible();
        await expect(page.locator('select[name="patient_id"]')).toBeVisible();
        await expect(page.locator('select[name="medecin_id"]')).toBeVisible();
        await expect(page.locator('input[name="date_consultation"]')).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto(`/consultations/${consultationId}`);
        await expect(page.locator('.cs-eyebrow').filter({ hasText: /Dossier de consultation/i })).toBeVisible();
        await expect(page.locator('body')).toContainText('Fiche complete de suivi medical');
        await expectNoPageOverflow(page);

        await page.goto(`/consultations/${consultationId}/edit`);
        await expect(page.locator('.ce-eyebrow').filter({ hasText: /Edition clinique/i })).toBeVisible();
        await expect(page.locator('#consultationForm')).toBeVisible();
        await expect(page.locator('select[name="patient_id"]')).toBeVisible();
        await expect(page.locator('textarea[name="diagnostic"]')).toBeVisible();
        await expectNoPageOverflow(page);
      });
    }
  });
});
