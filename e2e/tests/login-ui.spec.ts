import { expect, test } from '@playwright/test';

test.describe('Login UI', () => {
  for (const viewport of [
    { name: 'desktop', width: 1440, height: 960 },
    { name: 'mobile', width: 390, height: 844 },
  ]) {
    test(`renders cleanly on ${viewport.name}`, async ({ browser }) => {
      const page = await browser.newPage({ viewport });

      await page.goto('/login');

      await expect(page.getByRole('heading', { name: 'Connexion à votre espace' })).toBeVisible();
      await expect(page.locator('#email')).toBeVisible();
      await expect(page.locator('#password')).toBeVisible();
      await expect(page.locator('#remember')).toBeVisible();
      await expect(page.getByRole('button', { name: 'Se connecter' })).toBeVisible();
      await expect(page.getByRole('link', { name: 'Mot de passe oublié ?' })).toBeVisible();

      const metrics = await page.evaluate(() => ({
        scrollWidth: document.documentElement.scrollWidth,
        innerWidth: window.innerWidth,
        scrollHeight: document.documentElement.scrollHeight,
        innerHeight: window.innerHeight,
      }));

      expect(metrics.scrollWidth).toBeLessThanOrEqual(metrics.innerWidth);
      expect(metrics.scrollHeight).toBeLessThanOrEqual(metrics.innerHeight + 32);

      await page.close();
    });
  }
});