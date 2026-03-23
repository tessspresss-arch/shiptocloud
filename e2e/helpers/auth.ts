import { expect, Page } from '@playwright/test';

export async function login(page: Page, email: string, password: string) {
  await page.goto('/login');
  await expect(page.locator('input[name="email"]')).toBeVisible();
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', password);
  await page.click('button[type="submit"]');
}

export async function ensureLoggedIn(page: Page) {
  await expect(page).toHaveURL(/(dashboard|admin\/dashboard|patients|agenda|rendezvous)/);
}
