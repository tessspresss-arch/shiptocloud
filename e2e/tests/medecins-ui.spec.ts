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

let medecinId = '0';

async function loginWithFallback(page: Page) {
  for (const credentials of credentialCandidates) {
    await page.goto('/login');
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await page.fill('input[name="email"]', credentials.email);
    await page.fill('input[name="password"]', credentials.password);
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle').catch(() => undefined);

    if (!page.url().includes('/login')) {
      return credentials;
    }
  }

  throw new Error('Aucune combinaison d’identifiants E2E n’a permis de se connecter.');
}

async function expectNoPageOverflow(page: Page) {
  const hasOverflow = await page.evaluate(() => {
    const allowance = 8;

    return document.documentElement.scrollWidth - window.innerWidth > allowance;
  });

  expect(hasOverflow).toBeFalsy();
}

test.describe('Validation UI module médecins', () => {
  test.beforeAll(() => {
    const fixture = ensurePhpFixture<{ medecin_id: number }>('ensure_e2e_medecins.php');
    medecinId = String(fixture.medecin_id);
  });

  test('index, create, show et edit restent lisibles sur plusieurs viewports', async ({ page }) => {
    await loginWithFallback(page);

    const viewports = [
      { label: 'desktop', width: 1440, height: 900 },
      { label: 'tablet', width: 1024, height: 768 },
      { label: 'mobile', width: 390, height: 844 },
    ];

    for (const viewport of viewports) {
      await test.step(`Validation ${viewport.label}`, async () => {
        await page.setViewportSize({ width: viewport.width, height: viewport.height });

        await page.goto('/medecins');
        await expect(page.getByRole('link', { name: /Nouveau medecin/i })).toBeVisible();
        await expect(page.locator('body')).toContainText('Actions rapides');
        await expectNoPageOverflow(page);

        await page.goto('/medecins/create');
        await expect(page.locator('input[name="nom"]')).toBeVisible();
        await expect(page.locator('input[name="nom"]')).toBeVisible();
        await expect(page.locator('select[name="statut"]')).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto(`/medecins/${medecinId}`);
        await expect(page.locator('body')).toContainText('Informations professionnelles');
        await expect(page.getByRole('link', { name: /Modifier la fiche/i })).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto(`/medecins/${medecinId}/edit`);
        await expect(page.locator('input[name="nom"]')).toBeVisible();
        await expect(page.getByRole('button', { name: /Mettre à jour le médecin/i })).toBeVisible();
        await expectNoPageOverflow(page);
      });
    }
  });
});
