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

let activeDossierId = '0';
let archivedDossierId = '0';

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

  throw new Error('Aucune combinaison d’identifiants E2E n’a permis de se connecter.');
}

async function expectNoPageOverflow(page: Page) {
  const hasOverflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth > 8);
  expect(hasOverflow).toBeFalsy();
}

test.describe('Validation UI dossiers médicaux', () => {
  test.beforeAll(() => {
    const fixture = ensurePhpFixture<{ active_dossier_id: number; archived_dossier_id: number }>('ensure_e2e_dossiers.php');
    activeDossierId = String(fixture.active_dossier_id);
    archivedDossierId = String(fixture.archived_dossier_id);
  });

  test('index, create, archives, detail et edit restent lisibles sur plusieurs viewports', async ({ page }) => {
    await loginWithFallback(page);

    const viewports = [
      { label: 'desktop', width: 1440, height: 900 },
      { label: 'tablet', width: 1024, height: 768 },
      { label: 'mobile', width: 390, height: 844 },
    ];

    for (const viewport of viewports) {
      await test.step(`Validation ${viewport.label}`, async () => {
        await page.setViewportSize({ width: viewport.width, height: viewport.height });
        await page.goto('/dossiers');

        await expect(page.getByRole('heading', { name: /Gestion des Dossiers Médicaux/i })).toBeVisible();
        await expect(page.locator('a[href$="/dossiers/create"]').first()).toBeVisible();
        await expect(page.locator('a[href$="/dossiers/archives"]').first()).toBeVisible();
        await expect(page.locator('body')).toContainText('Dossiers actifs');
        await expect(page.locator('input[name="search"]')).toBeVisible();
        await expect(page.locator('select[name="type"]')).toBeVisible();
        await expect(page.locator('table')).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto('/dossiers/create');
        await expect(page.getByRole('heading', { name: /Créer un dossier médical/i })).toBeVisible();
        await expect(page.locator('body')).toContainText('Actions rapides');
        await expect(page.locator('select[name="patient_id"]')).toBeVisible();
        await expect(page.locator('input[name="numero_dossier"]')).toBeVisible();
        await expect(page.locator('button[type="submit"]').first()).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto('/dossiers/archives');
        await expect(page.getByRole('heading', { name: /Archives des Dossiers Médicaux/i })).toBeVisible();
        await expect(page.getByRole('link', { name: /Retour aux dossiers actifs/i })).toBeVisible();
        await expect(page.locator('input[name="search"]')).toBeVisible();
        await expect(page.locator('table')).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto(`/dossiers/${activeDossierId}`);
        await expect(page.locator('.dossier-title')).toContainText(/Dossier médical/i);
        await expect(page.locator('body')).toContainText('Archiver');
        await expectNoPageOverflow(page);

        await page.goto(`/dossiers/${activeDossierId}/edit`);
        await expect(page.getByRole('heading', { name: /Modifier le dossier médical/i })).toBeVisible();
        await expect(page.locator('body')).toContainText('Actions rapides');
        await expect(page.locator('textarea[name="observations"]')).toBeVisible();
        await expect(page.locator('button[type="submit"]').first()).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto(`/dossiers/${archivedDossierId}`);
        await expect(page.locator('.dossier-title')).toContainText(/Dossier médical/i);
        await expect(page.locator('body')).toContainText('Voir les archives');
        await expectNoPageOverflow(page);
      });
    }
  });
});