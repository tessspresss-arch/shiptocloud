import { expect, test, type Locator, type Page } from '@playwright/test';
import { runPhpJsonScript } from '../helpers/fixtures';
import { ensureLoggedIn, login } from '../helpers/auth';
import { selectFirstNonEmptyOption } from '../helpers/forms';

type FlowSnapshot = {
  token: string;
  patient_count: number;
  patient_id: number | null;
  consultation_id: number | null;
  consultation_patient_id: number | null;
  consultation_status: string | null;
  facture_id: number | null;
  facture_patient_id: number | null;
  facture_consultation_id: number | null;
  facture_statut: string | null;
  facture_date_paiement: string | null;
  rendezvous_id: number | null;
  rendezvous_patient_id: number | null;
  rendezvous_statut: string | null;
};

const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@medisys.test';
const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password';

test.describe('Parcours critique patient -> consultation -> facturation -> rendez-vous', () => {
  test('valide le flux metier complet sans erreur fonctionnelle ni visuelle', async ({ page }) => {
    test.slow();
    test.setTimeout(8 * 60_000);

    const token = Date.now().toString().slice(-8);
    const patientNom = `Critical${token}`;
    const patientPrenom = 'Flow';
    const patientLabel = `${patientNom} ${patientPrenom}`;
    const patientEmail = `critical.${token}@medisys.test`;
    const patientCin = `CRIT-${token}`;
    const patientPhone = `6${token.slice(-7)}`;
    const consultationDiagnostic = `Diagnostic critique ${token}`;
    const factureNote = `Flux critique ${token}`;
    const rendezVousNote = `Rendez-vous critique ${token}`;
    const runtimeIssues = createRuntimeIssueCollector(page);
    const consultationDate = toDateTimeLocal(new Date(Date.now() - 60 * 60 * 1000));
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const rendezVousDate = toDate(tomorrow);

    await login(page, adminEmail, adminPassword);
    await ensureLoggedIn(page);
    await waitForStable(page);

    await test.step('Créer un patient complet et unique', async () => {
      await page.setViewportSize({ width: 1440, height: 900 });
      await page.goto('/patients/create', { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await assertNoHorizontalOverflow(page, 'Patients create desktop');

      await page.fill('input[name="nom"]', patientNom);
      await page.fill('input[name="prenom"]', patientPrenom);
      await page.fill('input[name="date_naissance"]', '1991-07-18');
      const genderField = page.locator('input[name="genre"][value="M"]');
      if (await genderField.count()) {
        await genderField.check();
      }
      await page.fill('input[name="telephone"]', patientPhone);
      await page.fill('input[name="cin"]', patientCin);
      await fillIfVisible(page.locator('input[name="email"]'), patientEmail);
      await fillIfVisible(page.locator('textarea[name="adresse"], input[name="adresse"]'), '12 rue QA critique');
      const citySelect = page.locator('select[name="ville_selection"]');
      if (await citySelect.count()) {
        await citySelect.selectOption('Casablanca');
      } else {
        await fillIfVisible(page.locator('input[name="ville"]'), 'Casablanca');
      }
      await fillIfVisible(page.locator('input[name="profession"]'), 'Analyste QA');
      await fillIfVisible(page.locator('textarea[name="notes"]'), `Patient de test critique ${token}`);

      await page.getByRole('button', { name: /Enregistrer/i }).first().click();
      await expect(page).toHaveURL(/\/patients/);
      await expect(page).not.toHaveURL(/\/login/);

      await page.goto(`/patients?search=${patientNom}`, { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await expect(page.locator('body')).toContainText(new RegExp(token));
    });

    let consultationId = 0;

    await test.step('Créer une consultation liée au patient', async () => {
      await page.goto('/consultations/create', { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await assertNoHorizontalOverflow(page, 'Consultations create desktop');

      const selectedPatientValue = await selectOptionContainingText(page.locator('#patient_id'), patientNom);
      expect(selectedPatientValue, 'Le patient doit etre selectable dans le formulaire consultation.').not.toBeNull();
      await expectSelectedOptionToContain(page.locator('#patient_id'), patientNom);
      await selectFirstNonEmptyOption(page.locator('#medecin_id'));
      await setInputValue(page, '#date_consultation', consultationDate);
      await page.locator('textarea[name="symptomes"], input[name="symptomes"]').first().fill(`Symptomes critiques ${token}`);
      await page.locator('textarea[name="diagnostic"], input[name="diagnostic"]').first().fill(consultationDiagnostic);
      await fillIfVisible(page.locator('textarea[name="examen_clinique"]'), 'Examen stable, aucun signe de gravite.');
      await fillIfVisible(page.locator('textarea[name="traitement_prescrit"]'), 'Hydratation et surveillance.');
      await fillIfVisible(page.locator('textarea[name="recommandations"]'), 'Reevaluation sous 24h si aggravation.');

      await expect(page.locator('#patient_id')).toHaveValue(String(selectedPatientValue));
      await expect(page.locator('#medecin_id')).not.toHaveValue('');
      await expect(page.locator('#date_consultation')).toHaveValue(consultationDate);

      const consultationResponsePromise = page.waitForResponse(
        (response) => response.request().method() === 'POST' && /\/consultations$/.test(response.url()),
      );

      await page.locator('#consultationCreateForm').evaluate((form: HTMLFormElement) => {
        form.requestSubmit();
      });

      const consultationResponse = await consultationResponsePromise;
      expect(consultationResponse.status(), 'La soumission du formulaire consultation doit repondre par une redirection.').toBe(302);
      await expect(page).not.toHaveURL(/\/consultations\/create$/);
      await expect(page).not.toHaveURL(/\/login/);

      const consultationSnapshot = await runPhpJsonScript<FlowSnapshot>('check_critical_patient_flow.php', [token]);
      consultationId = consultationSnapshot.consultation_id ?? 0;
      expect(consultationId).toBeGreaterThan(0);

      await page.goto(`/consultations/${consultationId}`, { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await expect(page).toHaveURL(new RegExp(`/consultations/${consultationId}$`));
      await expect(page.locator('body')).toContainText(new RegExp(token));
      await expect(page.getByRole('link', { name: /Facturer/i })).toBeVisible();
    });

    let factureId = 0;

    await test.step('Générer puis régler une facture liée à la consultation', async () => {
      await page.getByRole('link', { name: /Facturer/i }).click();
      await expect(page).toHaveURL(new RegExp(`/factures/create\\?consultation_id=${consultationId}`));
      await waitForStable(page);
      await assertNoHorizontalOverflow(page, 'Factures create desktop');
      await expect(page.locator('input[name="consultation_id"]')).toHaveValue(String(consultationId));
      await expect(page.locator('body')).toContainText(`consultation #${consultationId}`);
      await expectSelectedOptionToContain(page.locator('#selectPatient'), token);

      await page.fill('input[name="prestations[0][description]"]', `Consultation critique ${token}`);
      await page.fill('input[name="prestations[0][quantite]"]', '1');
      await page.fill('input[name="prestations[0][prix_unitaire]"]', '420');
      await page.fill('textarea[name="notes"]', factureNote);

      await page.getByRole('button', { name: /Cr[eé]er la facture/i }).click();
      await expect(page).toHaveURL(/\/factures/);
      await expect(page).not.toHaveURL(/\/login/);

      await page.goto(`/factures?search=${patientNom}`, { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      const row = page.locator('tr').filter({ hasText: token }).first();
      await expect(row).toBeVisible();
      await expect(row).toContainText(/Impay[eé]e|En attente/i);
      await row.locator('a.billing-icon-btn.view').first().click();
      await expect(page).toHaveURL(/\/factures\/\d+$/);
      factureId = Number(page.url().match(/factures\/(\d+)$/)?.[1] ?? '0');
      expect(factureId).toBeGreaterThan(0);
      await expect(page.locator('body')).toContainText(`Consultation liée`);
      await expect(page.locator('body')).toContainText(`#${consultationId}`);

      await page.getByRole('button', { name: /Marquer comme pay[eé]e/i }).click();
      await waitForStable(page);
      await expect(page.locator('.facture-status')).toContainText(/Pay[eé]e/i);
    });

    await test.step('Créer un rendez-vous pour le même patient', async () => {
      await page.goto('/rendezvous/create', { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await assertNoHorizontalOverflow(page, 'Rendez-vous create desktop');

      const patientValue = await selectOptionContainingText(page.locator('#patientSelect'), patientNom);
      expect(patientValue, 'Le patient doit etre disponible dans le formulaire rendez-vous.').not.toBeNull();
      await page.locator('input[name="medecin_id"]').first().check();
      await page.locator('input[name="motif"]').first().check();
      await setInputValue(page, '#inputDate', rendezVousDate);
      await setInputValue(page, '#inputTime', '10:30');
      await fillIfVisible(page.locator('#notes'), rendezVousNote);

      await expect(page.locator('#submitBtn')).toBeEnabled();
      await page.locator('#submitBtn').click();
      await waitForStable(page);
      await expect(page).not.toHaveURL(/\/rendezvous\/create/);
      await expect(page).not.toHaveURL(/\/login/);
    });

    await test.step('Valider la persistance et les relations en base', async () => {
      const snapshot = runPhpJsonScript<FlowSnapshot>('check_critical_patient_flow.php', [token]);

      expect(snapshot.patient_count).toBe(1);
      expect(snapshot.patient_id).not.toBeNull();
      expect(snapshot.consultation_id).toBe(consultationId);
      expect(snapshot.consultation_patient_id).toBe(snapshot.patient_id);
      expect(snapshot.consultation_status).toBe('terminee');
      expect(snapshot.facture_id).toBe(factureId);
      expect(snapshot.facture_patient_id).toBe(snapshot.patient_id);
      expect(snapshot.facture_consultation_id).toBe(snapshot.consultation_id);
      expect(snapshot.facture_statut).toMatch(/pay[eé]e/i);
      expect(snapshot.facture_date_paiement).toBeTruthy();
      expect(snapshot.rendezvous_id).not.toBeNull();
      expect(snapshot.rendezvous_patient_id).toBe(snapshot.patient_id);
    });

    await test.step('Contrôler la cohérence responsive sur les modules critiques', async () => {
      const mobilePages = [
        { route: `/consultations/${consultationId}`, selector: 'main h1' },
        { route: `/factures/${factureId}`, selector: '.facture-status' },
        { route: '/rendezvous/create', selector: '#rdvCreateForm' },
        { route: `/patients?search=${patientNom}`, selector: 'body' },
      ];

      for (const currentPage of mobilePages) {
        await page.setViewportSize({ width: 390, height: 844 });
        await page.goto(currentPage.route, { waitUntil: 'domcontentloaded' });
        await waitForStable(page);
        await expect(page).not.toHaveURL(/\/login/);
        await expect(page.locator(currentPage.selector).first()).toBeVisible();
        await assertNoHorizontalOverflow(page, `Mobile ${currentPage.route}`);
      }
    });

    expect(runtimeIssues.pageErrors, `Erreurs JS detectees: ${runtimeIssues.pageErrors.join(' | ')}`).toEqual([]);
    expect(runtimeIssues.consoleErrors, `Console errors detectees: ${runtimeIssues.consoleErrors.join(' | ')}`).toEqual([]);
    expect(runtimeIssues.networkErrors, `Erreurs reseau/backend detectees: ${runtimeIssues.networkErrors.join(' | ')}`).toEqual([]);
  });
});

function createRuntimeIssueCollector(page: Page) {
  const pageErrors: string[] = [];
  const consoleErrors: string[] = [];
  const networkErrors: string[] = [];

  page.on('pageerror', (error) => {
    pageErrors.push(error.message);
  });

  page.on('console', (message) => {
    if (message.type() === 'error') {
      consoleErrors.push(message.text());
    }
  });

  page.on('response', (response) => {
    const request = response.request();
    if (response.status() >= 400 && (request.isNavigationRequest() || ['fetch', 'xhr'].includes(request.resourceType()))) {
      networkErrors.push(`${response.status()} ${request.method()} ${response.url()}`);
    }
  });

  page.on('requestfailed', (request) => {
    networkErrors.push(`FAILED ${request.method()} ${request.url()} ${request.failure()?.errorText ?? 'unknown'}`);
  });

  return { pageErrors, consoleErrors, networkErrors };
}

async function fillIfVisible(locator: Locator, value: string) {
  const target = locator.first();
  if (await target.count()) {
    await target.fill(value);
  }
}

async function waitForStable(page: Page): Promise<void> {
  await page.waitForLoadState('networkidle', { timeout: 20_000 }).catch(() => undefined);
  await page.waitForTimeout(300);
}

async function setInputValue(page: Page, selector: string, value: string) {
  await page.evaluate(
    ({ currentSelector, currentValue }) => {
      const input = document.querySelector<HTMLInputElement>(currentSelector);
      if (!input) {
        throw new Error(`Input ${currentSelector} introuvable.`);
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
    const label = ((await option.textContent()) ?? '').trim();
    if (value && label.includes(text)) {
      await select.selectOption(value);
      return value;
    }
  }

  return null;
}

async function expectSelectedOptionToContain(select: Locator, text: string) {
  const selectedText = await select.locator('option:checked').textContent();
  expect(selectedText ?? '').toContain(text);
}

async function assertNoHorizontalOverflow(page: Page, label: string) {
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth > 1);
  expect(overflow, `${label} presente un debordement horizontal.`).toBeFalsy();
}

function toDate(date: Date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function toDateTimeLocal(date: Date) {
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${toDate(date)}T${hours}:${minutes}`;
}
