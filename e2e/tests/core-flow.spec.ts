import { expect, test, type Locator, type Page } from '@playwright/test';
import { ensureLoggedIn, login } from '../helpers/auth';
import { selectFirstNonEmptyOption } from '../helpers/forms';

const receptionEmail = process.env.E2E_RECEPTION_EMAIL ?? 'reception@medisys.test';
const receptionPassword = process.env.E2E_RECEPTION_PASSWORD ?? 'password';

test.describe('Flux metier principal', () => {
  test('patient -> rendez-vous -> salle attente -> facture', async ({ page }) => {
    test.slow();

    await login(page, receptionEmail, receptionPassword);
    await ensureLoggedIn(page);

    // 1) Creation patient
    await page.goto('/patients/create');
    const unique = Date.now().toString().slice(-6);
    await page.fill('input[name="nom"]', `Test${unique}`);
    await page.fill('input[name="prenom"]', 'Patient');
    await page.fill('input[name="date_naissance"]', '1992-04-15');
    await page.check('input[name="genre"][value="M"]');
    await page.fill('input[name="telephone"]', `6${unique.padStart(7, '0')}`);
    await page.fill('input[name="cin"]', `TT${unique}`);
    await page.fill('input[name="adresse"]', 'Adresse de test');
    await page.selectOption('select[name="ville_selection"]', 'Casablanca').catch(async () => {
      await page.fill('input[name="ville"]', 'Casablanca');
    });
    await page.selectOption('select[name="assurance_medicale"]', { label: /CNSS|Aucune|CNOPS/i }).catch(() => null);
    await page.click('form[action*="/patients"] button[type="submit"]');
    await expect(page).toHaveURL(/\/patients/);

    // 2) Creation rendez-vous
    await page.goto('/rendezvous/create');
    const selectedPatient = await selectOptionContainingText(page.locator('select[name="patient_id"]'), unique);
    expect(selectedPatient).not.toBeNull();
    await page.check('input[name="medecin_id"]');
    await page.check('input[name="motif"]');

    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const y = tomorrow.getFullYear();
    const m = String(tomorrow.getMonth() + 1).padStart(2, '0');
    const d = String(tomorrow.getDate()).padStart(2, '0');
    await setInputValue(page, '#inputDate', `${y}-${m}-${d}`);
    await setInputValue(page, '#inputTime', '09:00');

    await page.click('#submitBtn');
    await expect(page).toHaveURL(/\/rendezvous/);

    // 3) Affichage salle d'attente
    await page.goto('/salle-attente');
    await expect(page.locator('#waiting-room-app')).toBeVisible();
    await page.click('#wr-refresh');
    await expect(page.locator('.wr-column')).toHaveCount(5);

    // 4) Generation facture simple
    await page.goto('/factures/create');
    await selectOptionContainingText(page.locator('select[name="patient_id"]'), unique);
    await selectFirstNonEmptyOption(page.locator('select[name="medecin_id"]'));
    await page.fill('input[name="prestations[0][description]"]', 'Consultation test automatisee');
    await page.fill('input[name="prestations[0][quantite]"]', '1');
    await page.fill('input[name="prestations[0][prix_unitaire]"]', '250');
    await page.click('button[type="submit"][name="action"][value="en_attente"]');
    await expect(page).toHaveURL(/\/factures/);
  });
});

async function setInputValue(page: Page, selector: string, value: string) {
  await page.evaluate(
    ({ currentSelector, currentValue }) => {
      const input = document.querySelector<HTMLInputElement>(currentSelector);
      if (!input) {
        throw new Error(`Input ${currentSelector} introuvable`);
      }

      input.value = currentValue;
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    },
    { currentSelector: selector, currentValue: value },
  );
}

async function selectOptionContainingText(select: Locator, text: string) {
  const options = select.locator('option');
  const count = await options.count();

  for (let index = 0; index < count; index += 1) {
    const option = options.nth(index);
    const value = (await option.getAttribute('value')) ?? '';
    const label = (await option.textContent()) ?? '';

    if (value.trim() !== '' && label.includes(text)) {
      await select.selectOption(value);
      return value;
    }
  }

  return null;
}
