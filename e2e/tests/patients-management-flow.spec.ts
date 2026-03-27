import { expect, test } from '@playwright/test';
import { ensureLoggedIn, login } from '../helpers/auth';

const receptionEmail = process.env.E2E_RECEPTION_EMAIL ?? 'reception@medisys.test';
const receptionPassword = process.env.E2E_RECEPTION_PASSWORD ?? 'password';

test.describe('Gestion des patients', () => {
  test('flux metier principal desktop', async ({ page }) => {
    test.slow();

    const unique = Date.now().toString().slice(-6);
    const phoneToken = unique.padStart(7, '0');
    const patientNom = `Qa${unique}`;
    const patientPrenom = 'Scenario';
    const patientTelephone = `6${phoneToken}`;
    const patientEmail = `scenario.${unique}@medisys.test`;

    await login(page, receptionEmail, receptionPassword);
    await ensureLoggedIn(page);

    await page.goto('/patients');
    await expect(page.getByText('Gestion des Patients')).toBeVisible();
    await expect(page.getByText('Liste des patients')).toBeVisible();
    await expect(page.getByRole('link', { name: /Nouveau Patient/i })).toBeVisible();

    await page.getByRole('link', { name: /Nouveau Patient/i }).click();
    await expect(page).toHaveURL(/\/patients\/create$/);

    await page.fill('input[name="nom"]', patientNom);
    await page.fill('input[name="prenom"]', patientPrenom);
    await page.fill('input[name="date_naissance"]', '1992-06-14');
    await page.check('input[name="genre"][value="M"]');
    await page.fill('input[name="cin"]', `QA${unique}`);
    await page.fill('input[name="telephone"]', patientTelephone);
    await page.fill('input[name="email"]', patientEmail);
    await page.fill('input[name="adresse"]', '12 Rue Hassan II');
    await page.selectOption('select[name="ville_selection"]', 'Casablanca');
    await page.selectOption('select[name="assurance_medicale"]', 'CNSS').catch(() => null);

    await page.getByRole('button', { name: /^Enregistrer$/i }).click();
    await expect(page).toHaveURL(/\/patients$/);

    await searchPatient(page, patientNom);
    await expect(page.getByText(new RegExp(patientNom, 'i'))).toBeVisible();

    await page.getByRole('link', { name: new RegExp(`Voir le dossier de ${patientPrenom} ${patientNom}`, 'i') }).click();
    await expect(page).toHaveURL(/\/patients\/\d+$/);
    await expect(page.getByText('Dossier patient')).toBeVisible();
    await expect(page.getByText('Informations principales')).toBeVisible();
    await expect(page.getByText('Actions rapides')).toBeVisible();

    await page.getByRole('link', { name: /^Modifier$/i }).click();
    await expect(page).toHaveURL(/\/patients\/\d+\/edit$/);

    await page.fill('input[name="prenom"]', `${patientPrenom} Update`);
    await page.fill('input[name="email"]', `updated.${unique}@medisys.test`);
    await page.selectOption('select[name="ville_selection"]', 'Agadir');
    await page.fill('textarea[name="notes"]', 'Scenario Playwright patient');
    await page.getByRole('button', { name: /^Enregistrer$/i }).click();

    await expect(page).toHaveURL(/\/patients\/\d+$/);
    await expect(page.getByText(`${patientPrenom} Update`, { exact: false })).toBeVisible();
    await expect(page.getByText('Agadir')).toBeVisible();
    await expect(page.getByText('Scenario Playwright patient')).toBeVisible();

    await page.goto('/patients');
    await searchPatient(page, patientNom);

    page.once('dialog', async (dialog) => {
      expect(dialog.message()).toContain('Voulez-vous vraiment archiver ce patient ?');
      await dialog.accept();
    });

    await page.getByRole('button', { name: /Archiver le dossier de/i }).click();
    await expect(page.getByText(new RegExp(patientNom, 'i'))).not.toBeVisible();
  });

  test.describe('responsive mobile', () => {
    test.use({ viewport: { width: 390, height: 844 } });

    test('la liste patients reste exploitable en affichage reduit', async ({ page }) => {
      await login(page, receptionEmail, receptionPassword);
      await ensureLoggedIn(page);

      await page.goto('/patients');

      await expect(page.locator('#mobileMenuBtn')).toBeVisible();
      await expect(page.getByText('Gestion des Patients')).toBeVisible();
      await expect(page.locator('input[name="search"]')).toBeVisible();
      await expect(page.getByText('Liste des patients')).toBeVisible();
      await expect(page.getByText('Mode tableau')).toBeVisible();
    });
  });
});

async function searchPatient(page: import('@playwright/test').Page, value: string) {
  const searchInput = page.locator('input[name="search"]');
  await searchInput.fill(value);
  await searchInput.press('Enter');
  await page.waitForLoadState('networkidle');
}
