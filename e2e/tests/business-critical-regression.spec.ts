import { expect, test } from '@playwright/test';
import { ensureLoggedIn, login } from '../helpers/auth';
import { runPhpJsonScript } from '../helpers/fixtures';
import { selectFirstNonEmptyOption } from '../helpers/forms';
import {
  assertNoHorizontalOverflow,
  createRuntimeIssueCollector,
  expectSelectedOptionToContain,
  fillIfVisible,
  selectOptionContainingText,
  setInputValue,
  toDate,
  toDateTimeLocal,
  waitForStable,
} from '../helpers/runtime';

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

test.describe('Business critical regression', () => {
  test('covers patient, consultation, ordonnance, facture, paiement and rendez-vous', async ({ page }) => {
    test.slow();
    test.setTimeout(8 * 60_000);

    const token = Date.now().toString().slice(-8);
    const patientNom = `Biz${token}`;
    const patientPrenom = 'Critical';
    const patientEmail = `business.${token}@medisys.test`;
    const patientCin = `BIZ-${token}`;
    const patientPhone = `+2126${token.slice(-6)}`;
    const consultationDiagnostic = `Diagnostic business ${token}`;
    const factureNote = `Facture business ${token}`;
    const rendezVousNote = `Rendez-vous business ${token}`;
    const runtimeIssues = createRuntimeIssueCollector(page);
    const consultationDate = toDateTimeLocal(new Date(Date.now() - 60 * 60 * 1000));
    const ordonnanceDate = toDate(new Date());
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const rendezVousDate = toDate(tomorrow);

    await login(page, adminEmail, adminPassword);
    await ensureLoggedIn(page);
    await waitForStable(page);

    let consultationId = 0;
    let factureId = 0;

    await test.step('Create patient', async () => {
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
      await fillIfVisible(page.locator('textarea[name="adresse"], input[name="adresse"]'), '12 rue QA business');
      await fillIfVisible(page.locator('input[name="ville"]'), 'Casablanca');
      await fillIfVisible(page.locator('input[name="profession"]'), 'QA Lead');
      await fillIfVisible(page.locator('textarea[name="notes"]'), `Patient e2e critique ${token}`);

      await page.getByRole('button', { name: /Enregistrer/i }).first().click();
      await expect(page).toHaveURL(/\/patients/);

      await page.goto(`/patients?search=${patientNom}`, { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await expect(page.locator('body')).toContainText(new RegExp(token));
    });

    await test.step('Create consultation', async () => {
      await page.goto('/consultations/create', { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await assertNoHorizontalOverflow(page, 'Consultations create desktop');

      const selectedPatientValue = await selectOptionContainingText(page.locator('#patient_id'), patientNom);
      expect(selectedPatientValue).not.toBeNull();
      await expectSelectedOptionToContain(page.locator('#patient_id'), patientNom);
      await selectFirstNonEmptyOption(page.locator('#medecin_id'));
      await setInputValue(page, '#date_consultation', consultationDate);
      await page.locator('textarea[name="symptomes"], input[name="symptomes"]').first().fill(`Symptomes business ${token}`);
      await page.locator('textarea[name="diagnostic"], input[name="diagnostic"]').first().fill(consultationDiagnostic);
      await fillIfVisible(page.locator('textarea[name="traitement_prescrit"]'), 'Hydratation et surveillance.');
      await fillIfVisible(page.locator('textarea[name="recommandations"]'), 'Controle sous 24h.');

      const consultationResponsePromise = page.waitForResponse(
        (response) => response.request().method() === 'POST' && /\/consultations$/.test(response.url()),
      );

      await page.locator('#consultationCreateForm').evaluate((form: HTMLFormElement) => {
        form.requestSubmit();
      });

      const consultationResponse = await consultationResponsePromise;
      expect(consultationResponse.status()).toBe(302);

      const snapshot = runPhpJsonScript<FlowSnapshot>('check_critical_patient_flow.php', [token]);
      consultationId = snapshot.consultation_id ?? 0;
      expect(consultationId).toBeGreaterThan(0);

      await page.goto(`/consultations/${consultationId}`, { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await expect(page.locator('body')).toContainText(new RegExp(token));
    });

    await test.step('Create ordonnance from consultation', async () => {
      await page.goto(`/ordonnances/create?consultation_id=${consultationId}`, { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await assertNoHorizontalOverflow(page, 'Ordonnances create desktop');

      const consultationSelect = page.locator('#consultation_id');
      if ((await consultationSelect.count()) && !(await consultationSelect.inputValue().catch(() => ''))) {
        await selectFirstNonEmptyOption(consultationSelect);
      }

      const medecinSelect = page.locator('select[name="medecin_id"]');
      if ((await medecinSelect.count()) && !(await medecinSelect.inputValue().catch(() => ''))) {
        await selectFirstNonEmptyOption(medecinSelect);
      }

      await page.fill('#date_prescription', ordonnanceDate);
      await page.fill('#diagnostic', `Ordonnance business ${token}`);
      await page.fill('#instructions', 'Repos et hydration').catch(() => null);

      const medSearch = page.locator('.js-medication-search').first();
      await medSearch.fill('a');
      await page.waitForTimeout(500);
      const medOption = page.locator('.js-medication-results [data-medication-id]').first();
      await expect(medOption).toBeVisible();
      await medOption.click();
      await page.fill('input[name="medicaments[0][posologie]"]', '1 comprime matin et soir');
      await page.fill('input[name="medicaments[0][duree]"]', '5 jours');
      await page.fill('input[name="medicaments[0][quantite]"]', '10').catch(() => null);

      await page.locator('form#ordonnanceForm button[type="submit"]').first().click();
      await waitForStable(page);
      expect(page.url()).not.toContain('/ordonnances/create');
    });

    await test.step('Create facture and mark it paid', async () => {
      await page.goto(`/factures/create?consultation_id=${consultationId}`, { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await assertNoHorizontalOverflow(page, 'Factures create desktop');

      await expect(page.locator('input[name="consultation_id"]')).toHaveValue(String(consultationId));
      await expectSelectedOptionToContain(page.locator('#selectPatient'), token);
      await page.fill('input[name="prestations[0][description]"]', `Consultation business ${token}`);
      await page.fill('input[name="prestations[0][quantite]"]', '1');
      await page.fill('input[name="prestations[0][prix_unitaire]"]', '420');
      await page.fill('textarea[name="notes"]', factureNote);

      await page.getByRole('button', { name: /Cr[eé]er la facture/i }).click();
      await expect(page).toHaveURL(/\/factures/);

      await page.goto(`/factures?search=${patientNom}`, { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      const row = page.locator('tr').filter({ hasText: token }).first();
      await expect(row).toBeVisible();
      await row.locator('a.billing-icon-btn.view').first().click();
      await expect(page).toHaveURL(/\/factures\/\d+$/);
      factureId = Number(page.url().match(/factures\/(\d+)$/)?.[1] ?? '0');
      expect(factureId).toBeGreaterThan(0);

      const payButton = page.getByRole('button', { name: /Marquer comme pay/i });
      await expect(payButton).toBeVisible();
      await payButton.click();
      await waitForStable(page);
      await expect(page.locator('.facture-status')).toContainText(/Pay/i);
    });

    await test.step('Create rendez-vous for same patient', async () => {
      await page.goto('/rendezvous/create', { waitUntil: 'domcontentloaded' });
      await waitForStable(page);
      await assertNoHorizontalOverflow(page, 'Rendez-vous create desktop');

      const patientValue = await selectOptionContainingText(page.locator('#patientSelect'), patientNom);
      expect(patientValue).not.toBeNull();
      await page.locator('input[name="medecin_id"]').first().check();
      await page.locator('input[name="motif"]').first().check();
      await setInputValue(page, '#inputDate', rendezVousDate);
      await setInputValue(page, '#inputTime', '10:30');
      await fillIfVisible(page.locator('#notes'), rendezVousNote);

      await page.locator('#submitBtn').click();
      await waitForStable(page);
      expect(page.url()).not.toContain('/rendezvous/create');
    });

    await test.step('Validate persistence and mobile rendering', async () => {
      const snapshot = runPhpJsonScript<FlowSnapshot>('check_critical_patient_flow.php', [token]);

      expect(snapshot.patient_count).toBe(1);
      expect(snapshot.patient_id).not.toBeNull();
      expect(snapshot.consultation_id).toBe(consultationId);
      expect(snapshot.consultation_patient_id).toBe(snapshot.patient_id);
      expect(snapshot.facture_id).toBe(factureId);
      expect(snapshot.facture_patient_id).toBe(snapshot.patient_id);
      expect(snapshot.facture_consultation_id).toBe(snapshot.consultation_id);
      expect(snapshot.facture_statut).toMatch(/pay/i);
      expect(snapshot.facture_date_paiement).toBeTruthy();
      expect(snapshot.rendezvous_id).not.toBeNull();
      expect(snapshot.rendezvous_patient_id).toBe(snapshot.patient_id);

      const mobilePages = [
        { route: `/consultations/${consultationId}`, selector: 'main h1' },
        { route: `/factures/${factureId}`, selector: '.facture-status' },
        { route: `/ordonnances`, selector: 'body' },
        { route: `/patients?search=${patientNom}`, selector: 'body' },
      ];

      for (const currentPage of mobilePages) {
        await page.setViewportSize({ width: 390, height: 844 });
        await page.goto(currentPage.route, { waitUntil: 'domcontentloaded' });
        await waitForStable(page);
        await expect(page.locator(currentPage.selector).first()).toBeVisible();
        await assertNoHorizontalOverflow(page, `Mobile ${currentPage.route}`);
      }
    });

    expect(runtimeIssues.pageErrors, `Erreurs JS detectees: ${runtimeIssues.pageErrors.join(' | ')}`).toEqual([]);
    expect(runtimeIssues.consoleErrors, `Console errors detectees: ${runtimeIssues.consoleErrors.join(' | ')}`).toEqual([]);
    expect(runtimeIssues.networkErrors, `Erreurs reseau/backend detectees: ${runtimeIssues.networkErrors.join(' | ')}`).toEqual([]);
  });
});
