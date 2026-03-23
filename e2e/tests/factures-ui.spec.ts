import { expect, test, type Page } from '@playwright/test';
import { ensurePhpFixture } from '../helpers/fixtures';

let factureFixtureId: number;

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

test.describe('Validation UI factures', () => {
  test.beforeAll(() => {
    factureFixtureId = ensurePhpFixture<{ facture_id: number }>('ensure_e2e_factures.php').facture_id;
  });

  test('la page index reste lisible sur plusieurs viewports', async ({ page }) => {
    await loginWithFallback(page);

    const viewports = [
      { label: 'desktop', width: 1440, height: 900 },
      { label: 'tablet', width: 1024, height: 768 },
      { label: 'mobile', width: 390, height: 844 },
    ];

    for (const viewport of viewports) {
      await test.step(`Validation ${viewport.label}`, async () => {
        await page.setViewportSize({ width: viewport.width, height: viewport.height });
        await page.goto('/factures');

        await expect(page.getByRole('heading', { name: /Gestion des factures/i })).toBeVisible();
        await expect(page.locator('body')).toContainText('Actions rapides');
        await expect(page.getByRole('link', { name: /Nouvelle facture/i })).toBeVisible();
        await expect(page.locator('#exportBtn')).toBeVisible();
        await expect(page.locator('input[name="search"]')).toBeVisible();
        await expect(page.locator('select[name="status"]')).toBeVisible();
        await expect(page.locator('select[name="period"]')).toBeVisible();
        await expect(page.locator('select[name="per_page"]')).toBeVisible();
        await expect(page.locator('body')).toContainText('Montant total');
        await expect(page.locator('table').first()).toBeVisible();

        await expectNoPageOverflow(page);
      });
    }
  });

  test('les pages create, show et edit restent lisibles avec une facture fixture', async ({ page }) => {
    await loginWithFallback(page);
    await page.setViewportSize({ width: 1440, height: 900 });

    await page.goto('/factures/create');
    await expect(page.locator('.facture-head-title').filter({ hasText: /Nouvelle facture/i })).toBeVisible();
    await expect(page.locator('#factureForm')).toBeVisible();
    await expect(page.locator('select[name="patient_id"]')).toBeVisible();
    await expect(page.locator('input[name="prestations[0][description]"]')).toBeVisible();
    await expect(page.locator('input[name="date_facture"]')).toBeVisible();
    await expectNoPageOverflow(page);

    await page.goto(`/factures/${factureFixtureId}`);
    await expect(page.locator('.facture-status')).toBeVisible();
    await expect(page.locator('body')).toContainText('Récapitulatif');
    await expect(page.locator('body')).toContainText('Consultation premium E2E');
    await expectNoPageOverflow(page);

    await page.goto(`/factures/${factureFixtureId}/edit`);
    await expect(page.locator('#factureForm')).toBeVisible();
    await expect(page.locator('body')).toContainText('Informations Patient');
    await expect(page.locator('select[name="patient_id"]')).toBeVisible();
    await expect(page.locator('textarea[name="notes"]')).toBeVisible();
    await expect(page.locator('input[name="date_facture"]')).toBeVisible();
    await expectNoPageOverflow(page);
  });
});