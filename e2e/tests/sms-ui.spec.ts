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

let reminderId = '0';

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

test.describe('Validation UI sms', () => {
  test.beforeAll(() => {
    const fixture = ensurePhpFixture<{ reminder_id: number }>('ensure_e2e_sms.php');
    reminderId = String(fixture.reminder_id);
  });

  test('index, create, logs, show et edit restent lisibles avec une fixture SMS', async ({ page }) => {
    await loginWithFallback(page);

    const viewports = [
      { label: 'desktop', width: 1440, height: 900 },
      { label: 'tablet', width: 1024, height: 768 },
      { label: 'mobile', width: 390, height: 844 },
    ];

    for (const viewport of viewports) {
      await test.step(`Validation ${viewport.label}`, async () => {
        await page.setViewportSize({ width: viewport.width, height: viewport.height });

        await page.goto('/sms');
        await expect(page.locator('body')).toContainText('Rappels SMS');
        await expect(page.locator('a[href$="/sms/create"]').first()).toBeVisible();
        await expect(page.locator('a[href$="/sms/logs"]').first()).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto('/sms/create');
        await expect(page.locator('.sms-create-title-row')).toContainText(/Nouveau Rappel SMS/i);
        await expect(page.locator('select[name="rendezvous_id"]')).toBeVisible();
        await expect(page.locator('input[name="telephone"]')).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto('/sms/logs');
        await expect(page.locator('body')).toContainText('Historique');
        await expectNoPageOverflow(page);

        await page.goto(`/sms/${reminderId}`);
        await expect(page.locator('body')).toContainText('Detail du rappel SMS');
        await expect(page.locator(`a[href$="/sms/${reminderId}/edit"]`).first()).toBeVisible();
        await expectNoPageOverflow(page);

        await page.goto(`/sms/${reminderId}/edit`);
        await expect(page.locator('.sms-edit-form')).toBeVisible();
        await expect(page.locator('select[name="rendezvous_id"]')).toBeVisible();
        await expect(page.locator('textarea[name="message_template"]')).toBeVisible();
        await expectNoPageOverflow(page);
      });
    }
  });
});