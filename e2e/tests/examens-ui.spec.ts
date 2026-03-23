import { expect, test, type Page } from '@playwright/test';
import { ensurePhpFixture } from '../helpers/fixtures';

let examenFixtureId: number;

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

test.describe('Validation UI examens', () => {
  test.beforeAll(() => {
    examenFixtureId = ensurePhpFixture<{ examen_id: number }>('ensure_e2e_examens.php').examen_id;
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
        await page.goto('/examens');

        await expect(page.locator('h1').filter({ hasText: /Liste des examens/i })).toBeVisible();
        await expect(page.locator('body')).toContainText('Actions rapides');
        await expect(page.locator('.exam-action-shell a[href$="/dashboard"]').first()).toBeVisible();
        await expect(page.locator('.exam-action-shell a[href*="/examens/export"]').first()).toBeVisible();
        await expect(page.locator('.exam-action-shell a[href$="/examens/create"]').first()).toBeVisible();
        await expect(page.locator('input[name="search"]')).toBeVisible();
        await expect(page.locator('select[name="patient"]')).toBeVisible();
        await expect(page.locator('select[name="statut"]')).toBeVisible();
        await expect(page.locator('select[name="type"]')).toBeVisible();
        await expect(page.locator('body')).toContainText('Total affiche');

        const tableExists = await page.locator('table').count();
        if (tableExists > 0) {
          await expect(page.locator('table').first()).toBeVisible();
        } else {
          await expect(page.locator('body')).toContainText('Aucun examen enregistre pour le moment');
        }

        await expectNoPageOverflow(page);
      });
    }
  });

  test('la page create reste coherente et sans debordement', async ({ page }) => {
    await loginWithFallback(page);
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/examens/create');

    await expect(page.locator('.exam-record-eyebrow').filter({ hasText: /Creation examen/i })).toBeVisible();
    await expect(page.locator('#examCreateForm')).toBeVisible();
    await expect(page.locator('select[name="patient_id"]')).toBeVisible();
    await expect(page.locator('select[name="type_examen"]')).toBeVisible();
    await expect(page.locator('input[name="date_examen"]')).toBeVisible();
    await expect(page.locator('textarea[name="description"]')).toBeVisible();
    await expect(page.locator('body')).toContainText('Creation guidee');
    await expect(page.locator('body')).toContainText('Actions rapides');
    await expectNoPageOverflow(page);
  });

  test('les pages show et edit restent lisibles quand un examen existe', async ({ page }) => {
    await loginWithFallback(page);
    await page.setViewportSize({ width: 1440, height: 900 });

    await page.goto(`/examens/${examenFixtureId}`);
    await page.waitForLoadState('networkidle').catch(() => undefined);

    await expect(page.locator('.exam-show-eyebrow').filter({ hasText: /Fiche examen/i })).toBeVisible();
    await expect(page.locator('body')).toContainText('Synthese clinique et technique');
    await expect(page.locator('body')).toContainText('Analyse parametrique');
    await expect(page.locator(`a[href$="/examens/${examenFixtureId}/edit"]`).first()).toBeVisible();
    await expectNoPageOverflow(page);

    await page.goto(`/examens/${examenFixtureId}/edit`);
    await page.waitForLoadState('networkidle').catch(() => undefined);

    await expect(page.locator('.exam-record-eyebrow').filter({ hasText: /Edition examen/i })).toBeVisible();
    await expect(page.locator('#examEditForm')).toBeVisible();
    await expect(page.locator('select[name="patient_id"]')).toBeVisible();
    await expect(page.locator('select[name="type_examen"]')).toBeVisible();
    await expect(page.locator('textarea[name="resultats"]')).toBeVisible();
    await expect(page.locator('body')).toContainText('Formulaire d edition structure');
    await expectNoPageOverflow(page);
  });
});