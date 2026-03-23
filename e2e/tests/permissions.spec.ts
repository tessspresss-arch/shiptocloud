import { expect, test } from '@playwright/test';
import { login } from '../helpers/auth';

const medecinEmail = process.env.E2E_MEDECIN_EMAIL ?? 'medecin@medisys.test';
const medecinPassword = process.env.E2E_MEDECIN_PASSWORD ?? 'password';

test.describe('Permissions par role', () => {
  test('un medecin sans droit facturation ne peut pas ouvrir le module factures', async ({ page }) => {
    await login(page, medecinEmail, medecinPassword);
    await page.goto('/factures');

    await expect(page.locator('body')).toContainText(/403|Acces refuse|forbidden/i);
  });
});
