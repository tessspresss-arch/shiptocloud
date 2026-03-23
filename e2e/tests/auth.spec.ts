import { expect, test } from '@playwright/test';
import { ensureLoggedIn, login } from '../helpers/auth';

const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@medisys.test';
const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password';

test.describe('Authentification Medisys Pro', () => {
  test('connexion admin fonctionne et redirige vers le dashboard', async ({ page }) => {
    await login(page, adminEmail, adminPassword);
    await ensureLoggedIn(page);
    await expect(page.locator('body')).toContainText(/Dashboard|Tableau de Bord|Medisys/i);
  });
});
