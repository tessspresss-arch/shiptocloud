import fs from 'node:fs';
import path from 'node:path';
import { test, type Locator, type Page } from '@playwright/test';
import { ensureLoggedIn, login } from '../helpers/auth';
import { ensurePhpFixture } from '../helpers/fixtures';

type Severity = 'critical' | 'major' | 'minor';
type Category = 'responsive' | 'uiux' | 'navigation' | 'forms' | 'functionality' | 'performance' | 'technical';

type ViewportAudit = {
  name: string;
  width: number;
  height: number;
  kind: 'desktop' | 'mobile';
};

type ModuleTarget = {
  key: string;
  label: string;
  route: string;
};

type Finding = {
  severity: Severity;
  category: Category;
  module: string;
  step: string;
  detail: string;
  route?: string;
  viewport?: string;
  repro?: string;
};

type PageAuditResult = {
  module: string;
  route: string;
  viewport: string;
  status: 'ok' | 'issue' | 'blocked';
  title: string;
  heading: string;
  loadMs: number;
  domContentLoadedMs: number | null;
  loadEventMs: number | null;
  fcpMs: number | null;
  hasOverflow: boolean;
  overflowNodes: string[];
  undersizedTargets: Array<{ text: string; width: number; height: number }>;
  consoleErrors: string[];
  jsErrors: string[];
  failedRequests: string[];
};

type WorkflowResult = {
  name: string;
  status: 'passed' | 'failed' | 'blocked';
  detail: string;
  durationMs: number;
};

type RuntimeLogs = {
  consoleErrors: string[];
  jsErrors: string[];
  failedRequests: string[];
};

type AuditFixtures = {
  medecinId: string;
  consultationId: string;
  consultationEditUrl: string;
  factureId: string;
  examenId: string;
  activeDossierId: string;
  archivedDossierId: string;
  reminderId: string;
};

const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@medisys.test';
const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password';

const viewports: ViewportAudit[] = [
  { name: 'desktop-1280', width: 1280, height: 900, kind: 'desktop' },
  { name: 'desktop-1440', width: 1440, height: 900, kind: 'desktop' },
  { name: 'desktop-1920', width: 1920, height: 1080, kind: 'desktop' },
  { name: 'mobile-320', width: 320, height: 740, kind: 'mobile' },
  { name: 'mobile-375', width: 375, height: 812, kind: 'mobile' },
  { name: 'mobile-390', width: 390, height: 844, kind: 'mobile' },
  { name: 'mobile-414', width: 414, height: 896, kind: 'mobile' },
  { name: 'mobile-768', width: 768, height: 1024, kind: 'mobile' },
];

const modules: ModuleTarget[] = [
  { key: 'dashboard', label: 'Dashboard', route: '/dashboard' },
  { key: 'patients', label: 'Patients', route: '/patients' },
  { key: 'consultations', label: 'Consultations', route: '/consultations' },
  { key: 'agenda', label: 'Agenda medical', route: '/agenda' },
  { key: 'salle-attente', label: 'Salle d attente intelligente', route: '/salle-attente' },
  { key: 'ordonnances', label: 'Ordonnances', route: '/ordonnances' },
  { key: 'utilisateurs', label: 'Utilisateurs', route: '/utilisateurs' },
  { key: 'parametres', label: 'Parametres', route: '/parametres' },
  { key: 'facturation', label: 'Facturation', route: '/factures' },
  { key: 'documents', label: 'Documents', route: '/documents' },
  { key: 'statistiques', label: 'Statistiques', route: '/statistiques' },
  { key: 'rapports', label: 'Rapports', route: '/rapports' },
];

test.describe('Audit preproduction complet', () => {
  test('desktop mobile workflows performance', async ({ page }) => {
    test.setTimeout(15 * 60_000);
    test.slow();

    const reportPath = path.resolve('storage/test-reports/preproduction-audit.json');
    const report = {
      generatedAt: new Date().toISOString(),
      baseUrl: process.env.E2E_BASE_URL ?? 'http://cabinet-medical-laravel.test',
      credentials: { email: adminEmail, passwordMasked: adminPassword ? '***' : '' },
      pageAudits: [] as PageAuditResult[],
      workflows: [] as WorkflowResult[],
      findings: [] as Finding[],
      summary: {
        totals: { critical: 0, major: 0, minor: 0 },
        byCategory: {} as Record<string, number>,
        byModule: {} as Record<string, number>,
        unstableModules: [] as Array<{ module: string; count: number }>,
      },
      generatedData: {} as Record<string, string | null>,
    };

    const logs: RuntimeLogs = {
      consoleErrors: [],
      jsErrors: [],
      failedRequests: [],
    };

    const auditFixtures: AuditFixtures = {
      medecinId: String(ensurePhpFixture<{ medecin_id: number }>('ensure_e2e_medecins.php').medecin_id),
      consultationId: String(ensurePhpFixture<{ consultation_id: number }>('ensure_e2e_consultations.php').consultation_id),
      consultationEditUrl: `/consultations/${ensurePhpFixture<{ consultation_id: number }>('ensure_e2e_consultations.php').consultation_id}/edit`,
      factureId: String(ensurePhpFixture<{ facture_id: number }>('ensure_e2e_factures.php').facture_id),
      examenId: String(ensurePhpFixture<{ examen_id: number }>('ensure_e2e_examens.php').examen_id),
      activeDossierId: String(ensurePhpFixture<{ active_dossier_id: number; archived_dossier_id: number }>('ensure_e2e_dossiers.php').active_dossier_id),
      archivedDossierId: String(ensurePhpFixture<{ active_dossier_id: number; archived_dossier_id: number }>('ensure_e2e_dossiers.php').archived_dossier_id),
      reminderId: String(ensurePhpFixture<{ reminder_id: number }>('ensure_e2e_sms.php').reminder_id),
    };

    page.on('console', (msg) => {
      if (msg.type() === 'error') {
        logs.consoleErrors.push(msg.text());
      }
    });
    page.on('pageerror', (error) => {
      logs.jsErrors.push(error.message);
    });
    page.on('requestfailed', (request) => {
      logs.failedRequests.push(`${request.method()} ${request.url()} - ${request.failure()?.errorText ?? 'failed'}`);
    });

    await login(page, adminEmail, adminPassword);
    await ensureLoggedIn(page);
    await waitForStable(page);

    for (const viewport of viewports) {
      await page.setViewportSize({ width: viewport.width, height: viewport.height });
      for (const module of modules) {
        const pageAudit = await auditModulePage(page, module, viewport, logs, report.findings);
        report.pageAudits.push(pageAudit);
      }
    }

    const workflowContext = await runWorkflows(page, logs, report.findings, auditFixtures);
    report.workflows.push(...workflowContext.results);
    report.generatedData = {
      patientName: workflowContext.patientName,
      patientEmail: workflowContext.patientEmail,
      consultationEditUrl: workflowContext.consultationEditUrl,
      consultationId: workflowContext.consultationId,
      fixtureMedecinId: auditFixtures.medecinId,
      fixtureConsultationId: auditFixtures.consultationId,
      fixtureConsultationEditUrl: auditFixtures.consultationEditUrl,
      fixtureFactureId: auditFixtures.factureId,
      fixtureExamenId: auditFixtures.examenId,
      fixtureActiveDossierId: auditFixtures.activeDossierId,
      fixtureArchivedDossierId: auditFixtures.archivedDossierId,
      fixtureSmsReminderId: auditFixtures.reminderId,
    };

    finalizeSummary(report);
    fs.mkdirSync(path.dirname(reportPath), { recursive: true });
    fs.writeFileSync(reportPath, JSON.stringify(report, null, 2), 'utf8');
  });
});

async function auditModulePage(
  page: Page,
  module: ModuleTarget,
  viewport: ViewportAudit,
  logs: RuntimeLogs,
  findings: Finding[],
): Promise<PageAuditResult> {
  const snapshot = snapshotLogs(logs);
  const startedAt = Date.now();
  let status: PageAuditResult['status'] = 'ok';

  try {
    await page.goto(module.route, { waitUntil: 'domcontentloaded', timeout: 45_000 });
  } catch (error) {
    const detail = error instanceof Error ? error.message : 'Navigation impossible';
    findings.push({
      severity: 'critical',
      category: 'navigation',
      module: module.label,
      route: module.route,
      viewport: viewport.name,
      step: 'chargement page',
      detail,
      repro: `Ouvrir ${module.route} en ${viewport.width}px`,
    });
    return {
      module: module.label,
      route: module.route,
      viewport: viewport.name,
      status: 'blocked',
      title: '',
      heading: '',
      loadMs: Date.now() - startedAt,
      domContentLoadedMs: null,
      loadEventMs: null,
      fcpMs: null,
      hasOverflow: false,
      overflowNodes: [],
      undersizedTargets: [],
      consoleErrors: [],
      jsErrors: [],
      failedRequests: [],
    };
  }

  await waitForStable(page);

  if (page.url().includes('/login')) {
    status = 'blocked';
    findings.push({
      severity: 'critical',
      category: 'navigation',
      module: module.label,
      route: module.route,
      viewport: viewport.name,
      step: 'controle acces',
      detail: 'Redirection vers la page de connexion.',
      repro: `Se connecter en admin puis ouvrir ${module.route}`,
    });
  }

  const perf = await collectPerformance(page);
  const overflow = await collectOverflow(page);
  const undersizedTargets = viewport.kind === 'mobile' ? await collectUndersizedTargets(page) : [];
  const title = await page.title();
  const heading = await page.locator('h1').first().textContent().catch(() => '') ?? '';
  const pageLogs = sliceLogs(logs, snapshot);

  if (overflow.hasOverflow) {
    status = status === 'blocked' ? status : 'issue';
    findings.push({
      severity: viewport.kind === 'mobile' ? 'major' : 'minor',
      category: 'responsive',
      module: module.label,
      route: module.route,
      viewport: viewport.name,
      step: 'mise en page',
      detail: `Debordement horizontal detecte. Elements suspects: ${overflow.nodes.join(' | ') || 'inconnus'}`,
      repro: `Ouvrir ${module.route} en ${viewport.width}px`,
    });
  }

  if (undersizedTargets.length > 0) {
    status = status === 'blocked' ? status : 'issue';
    findings.push({
      severity: 'minor',
      category: 'uiux',
      module: module.label,
      route: module.route,
      viewport: viewport.name,
      step: 'cibles tactiles',
      detail: `${undersizedTargets.length} cibles tactiles sous 44x44 detectees. Exemples: ${undersizedTargets.slice(0, 4).map((item) => `${item.text} (${item.width}x${item.height})`).join(' | ')}`,
      repro: `Ouvrir ${module.route} sur mobile`,
    });
  }

  if (pageLogs.jsErrors.length > 0) {
    status = status === 'blocked' ? status : 'issue';
    findings.push({
      severity: 'major',
      category: 'technical',
      module: module.label,
      route: module.route,
      viewport: viewport.name,
      step: 'javascript runtime',
      detail: pageLogs.jsErrors.slice(0, 3).join(' | '),
      repro: `Ouvrir ${module.route} et surveiller la console`,
    });
  }

  if (pageLogs.failedRequests.length > 0) {
    status = status === 'blocked' ? status : 'issue';
    findings.push({
      severity: 'major',
      category: 'technical',
      module: module.label,
      route: module.route,
      viewport: viewport.name,
      step: 'reseau',
      detail: pageLogs.failedRequests.slice(0, 3).join(' | '),
      repro: `Ouvrir ${module.route} et verifier l onglet reseau`,
    });
  }

  if (perf.loadMs > 5000 || (perf.domContentLoadedMs !== null && perf.domContentLoadedMs > 3000)) {
    status = status === 'blocked' ? status : 'issue';
    findings.push({
      severity: perf.loadMs > 7000 ? 'major' : 'minor',
      category: 'performance',
      module: module.label,
      route: module.route,
      viewport: viewport.name,
      step: 'temps de chargement',
      detail: `Chargement lent: total ${perf.loadMs}ms, DOMContentLoaded ${perf.domContentLoadedMs ?? 'n/a'}ms, FCP ${perf.fcpMs ?? 'n/a'}ms`,
      repro: `Mesurer ${module.route} en ${viewport.name}`,
    });
  }

  return {
    module: module.label,
    route: module.route,
    viewport: viewport.name,
    status,
    title,
    heading: heading.trim(),
    loadMs: perf.loadMs,
    domContentLoadedMs: perf.domContentLoadedMs,
    loadEventMs: perf.loadEventMs,
    fcpMs: perf.fcpMs,
    hasOverflow: overflow.hasOverflow,
    overflowNodes: overflow.nodes,
    undersizedTargets,
    consoleErrors: pageLogs.consoleErrors,
    jsErrors: pageLogs.jsErrors,
    failedRequests: pageLogs.failedRequests,
  };
}

async function runWorkflows(page: Page, logs: RuntimeLogs, findings: Finding[], fixtures: AuditFixtures) {
  const results: WorkflowResult[] = [];
  const unique = Date.now().toString().slice(-6);
  const patientNom = `Audit${unique}`;
  const patientPrenom = 'Preprod';
  const patientEmail = `audit.${unique}@medisys.test`;
  const patientTel = `+2126${unique}88`;
  const patientCin = `QA${unique}`;
  const patientFullName = `${patientNom} ${patientPrenom}`;
  const today = formatDate(new Date());
  const rdvTime = '10:00';
  let consultationEditUrl: string | null = fixtures.consultationEditUrl;
  let consultationId: string | null = fixtures.consultationId;

  results.push(await runWorkflowStep('Creer un patient', async () => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/patients/create', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    await page.fill('input[name="nom"]', patientNom);
    await page.fill('input[name="prenom"]', patientPrenom);
    await page.fill('input[name="date_naissance"]', '1990-05-20');
    const male = page.locator('input[name="genre"][value="M"]');
    if (await male.count()) {
      await male.check();
    }
    await page.fill('input[name="telephone"]', patientTel);
    await page.fill('input[name="cin"]', patientCin);
    await page.fill('input[name="email"]', patientEmail).catch(() => null);
    await page.fill('input[name="adresse"]', 'Adresse audit preprod').catch(() => null);
    await page.fill('input[name="ville"]', 'Casablanca').catch(() => null);
    await page.locator('form[action*="/patients"] button[type="submit"]').first().click();
    await waitForStable(page);
    await page.goto('/patients', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const visible = await page.locator(`text=${patientNom}`).first().isVisible().catch(() => false);
    if (!visible) {
      throw new Error('Patient cree mais introuvable dans la liste.');
    }
  }, findings, 'Patients', '/patients/create'));

  results.push(await runWorkflowStep('Creer un rendez-vous', async () => {
    await page.goto('/rendezvous/create', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const patientSelect = page.locator('#patientSelect');
    const patientValue = await selectOptionByText(patientSelect, patientNom);
    if (!patientValue) {
      throw new Error('Patient non disponible dans la liste rendez-vous.');
    }
    const medecinRadio = page.locator('input[name="medecin_id"]').first();
    if (!(await medecinRadio.count())) {
      throw new Error('Aucun medecin selectable sur le formulaire rendez-vous.');
    }
    await medecinRadio.check();
    const motifRadio = page.locator('input[name="motif"]').first();
    if (!(await motifRadio.count())) {
      throw new Error('Aucun motif selectable sur le formulaire rendez-vous.');
    }
    await motifRadio.check();
    await setInputValue(page, '#inputDate', today);
    await setInputValue(page, '#inputTime', rdvTime);
    await page.fill('#notes', 'Rendez-vous automatise preproduction').catch(() => null);
    await page.click('#submitBtn');
    await waitForStable(page);
    if (page.url().includes('/rendezvous/create')) {
      throw new Error('Le rendez-vous n a pas ete enregistre.');
    }
  }, findings, 'Agenda medical', '/rendezvous/create'));

  results.push(await runWorkflowStep('Deplacer patient salle d attente', async () => {
    await page.goto('/salle-attente', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    await page.fill('#wr-date', today);
    await page.fill('#wr-search', patientNom);
    await page.click('#wr-refresh');
    await page.waitForTimeout(1500);
    const sourceCard = page.locator('.wr-list[data-status="a_venir"] .wr-patient-card').filter({ hasText: patientNom }).first();
    if (!(await sourceCard.isVisible().catch(() => false))) {
      throw new Error('Patient absent de la colonne A venir dans la salle d attente.');
    }
    await sourceCard.locator('[data-action="call"]').click();
    await page.waitForTimeout(1500);
    const waitingCard = page.locator('.wr-list[data-status="en_attente"] .wr-patient-card').filter({ hasText: patientNom }).first();
    if (!(await waitingCard.isVisible().catch(() => false))) {
      throw new Error('Le patient n a pas ete deplace vers En attente.');
    }
  }, findings, 'Salle d attente intelligente', '/salle-attente'));

  results.push(await runWorkflowStep('Demarrer consultation', async () => {
    await page.goto('/salle-attente', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    await page.fill('#wr-date', today);
    await page.fill('#wr-search', patientNom);
    await page.click('#wr-refresh');
    await page.waitForTimeout(1200);
    const waitingCard = page.locator('.wr-list[data-status="en_attente"] .wr-patient-card').filter({ hasText: patientNom }).first();
    if (!(await waitingCard.isVisible().catch(() => false))) {
      throw new Error('Patient non present en attente avant le demarrage consultation.');
    }
    await waitingCard.locator('[data-action="start"]').click();
    await page.waitForTimeout(1500);
    const activeCard = page.locator('.wr-list[data-status="en_soins"] .wr-patient-card').filter({ hasText: patientNom }).first();
    if (!(await activeCard.isVisible().catch(() => false))) {
      throw new Error('Le patient n a pas bascule en consultation.');
    }
  }, findings, 'Agenda medical', '/salle-attente'));

  results.push(await runWorkflowStep('Creer une consultation', async () => {
    await page.goto('/salle-attente', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    await page.fill('#wr-date', today);
    await page.fill('#wr-search', patientNom);
    await page.click('#wr-refresh');
    await page.waitForTimeout(1200);
    const activeCard = page.locator('.wr-list[data-status="en_soins"] .wr-patient-card').filter({ hasText: patientNom }).first();
    if (!(await activeCard.isVisible().catch(() => false))) {
      throw new Error('Patient introuvable dans la colonne En consultation.');
    }
    await activeCard.locator('[data-action="consultation"]').click();
    await page.waitForURL(/\/consultations\/create/);
    await waitForStable(page);
    await ensurePatientSelected(page.locator('#patient_id'), patientNom);
    await ensureFirstSelectValue(page.locator('#medecin_id'));
    await page.fill('#date_consultation', today);
    await page.fill('#symptomes', 'Cefalees et fatigue');
    await page.fill('#diagnostic', 'Syndrome viral benin');
    await page.fill('textarea[name="examen_clinique"]', 'Examen clinique stable').catch(() => null);
    await page.fill('textarea[name="traitement_prescrit"]', 'Repos et hydratation').catch(() => null);
    await page.fill('textarea[name="recommandations"]', 'Controle sous 48h si aggravation').catch(() => null);
    await page.locator('button[type="submit"]').filter({ hasText: /Enregistrer/i }).first().click();
    await waitForStable(page);
    if (page.url().includes('/consultations/create')) {
      throw new Error('La consultation n a pas ete enregistree.');
    }
    await page.goto('/consultations', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const row = page.locator('tr, .consultation-card, .cs-card').filter({ hasText: patientNom }).first();
    if (!(await row.isVisible().catch(() => false))) {
      throw new Error('Consultation creee mais introuvable dans la liste.');
    }
    const editLink = row.locator('a[href*="/consultations/"][href$="/edit"]').first();
    if (!(await editLink.isVisible().catch(() => false))) {
      throw new Error('Lien de modification consultation introuvable pour le test IA.');
    }
    await editLink.click();
    await page.waitForURL(/\/consultations\/\d+\/edit/);
    await waitForStable(page);
    consultationEditUrl = page.url();
    consultationId = page.url().match(/consultations\/(\d+)\/edit/)?.[1] ?? null;
  }, findings, 'Consultations', '/consultations/create'));

  results.push(await runWorkflowStep('Tester assistant IA', async () => {
    if (!consultationEditUrl) {
      throw new Error('Consultation de reference indisponible pour l assistant IA.');
    }
    await page.goto(consultationEditUrl, { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const assistantRoot = page.locator('[data-generate-url]').first();
    if (!(await assistantRoot.isVisible().catch(() => false))) {
      throw new Error('Bloc assistant IA introuvable sur l edition consultation.');
    }
    const source = page.locator('[data-ai-source]').first();
    await source.fill('Patient stable. Resume clinique pour test automatique.');
    await page.locator('[data-ai-action="summary"]').first().click();
    await page.waitForTimeout(2500);
    const summary = await page.locator('[data-ai-summary-result]').first().inputValue().catch(() => '');
    const statusText = await page.locator('[data-ai-status]').first().textContent().catch(() => '');
    if (!summary.trim()) {
      throw new Error(`Aucun contenu IA genere. Statut: ${String(statusText || '').trim() || 'n/a'}`);
    }
  }, findings, 'Assistant IA', consultationEditUrl ?? '/consultations'));

  results.push(await runWorkflowStep('Creer une ordonnance', async () => {
    if (!consultationId) {
      throw new Error('Consultation indisponible pour preselectionner l ordonnance.');
    }
    await page.goto(`/ordonnances/create?consultation_id=${consultationId}`, { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const consultationSelect = page.locator('#consultation_id');
    if ((await consultationSelect.count()) && !(await consultationSelect.inputValue().catch(() => ''))) {
      await ensureFirstSelectValue(consultationSelect);
    }
    const medecinSelect = page.locator('select[name="medecin_id"]');
    if ((await medecinSelect.count()) && !(await medecinSelect.inputValue().catch(() => ''))) {
      await ensureFirstSelectValue(medecinSelect);
    }
    await page.fill('#date_prescription', today);
    await page.fill('#diagnostic', 'Traitement symptomatique');
    await page.fill('#instructions', 'Boire beaucoup d eau et repos').catch(() => null);
    const medSearch = page.locator('.js-medication-search').first();
    await medSearch.fill('a');
    await page.waitForTimeout(500);
    const medOption = page.locator('.js-medication-results [data-medication-id]').first();
    if (!(await medOption.isVisible().catch(() => false))) {
      throw new Error('Aucun medicament propose dans la recherche ordonnance.');
    }
    await medOption.click();
    await page.fill('input[name="medicaments[0][posologie]"]', '1 comprime matin et soir');
    await page.fill('input[name="medicaments[0][duree]"]', '5 jours');
    await page.fill('input[name="medicaments[0][quantite]"]', '10').catch(() => null);
    await page.locator('button[type="submit"]').first().click();
    await waitForStable(page);
    if (page.url().includes('/ordonnances/create')) {
      throw new Error('L ordonnance n a pas ete enregistree.');
    }
  }, findings, 'Ordonnances', '/ordonnances/create'));

  results.push(await runWorkflowStep('Generer une facture', async () => {
    await page.goto('/factures/create', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const patientSelect = page.locator('#selectPatient');
    const patientValue = await selectOptionByText(patientSelect, patientNom);
    if (!patientValue) {
      throw new Error('Patient introuvable dans la liste de facturation.');
    }
    await ensureFirstSelectValue(page.locator('#selectMedecin')).catch(() => null);
    await page.fill('input[name="prestations[0][description]"]', 'Consultation QA preproduction');
    await page.fill('input[name="prestations[0][quantite]"]', '1');
    await page.fill('input[name="prestations[0][prix_unitaire]"]', '250');
    await page.fill('input[name="date_facture"]', today);
    await page.click('button[type="submit"][name="action"][value="en_attente"]');
    await waitForStable(page);
    if (page.url().includes('/factures/create')) {
      throw new Error('La facture n a pas ete creee.');
    }
  }, findings, 'Facturation', '/factures/create'));

  results.push(await runWorkflowStep('Modifier un utilisateur', async () => {
    await page.goto('/utilisateurs', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const editLink = page.locator('a[href*="/utilisateurs/"][href$="/edit"]').first();
    if (!(await editLink.isVisible().catch(() => false))) {
      throw new Error('Aucun utilisateur editable accessible.');
    }
    await editLink.click();
    await page.waitForURL(/\/utilisateurs\/\d+\/edit/);
    await waitForStable(page);
    const initialValue = await page.inputValue('input[name="first_name"]').catch(() => '');
    await page.fill('input[name="first_name"]', `${initialValue} QA`.trim());
    await page.locator('button[type="submit"]').filter({ hasText: /Enregistrer|Sauvegarder/i }).first().click();
    await waitForStable(page);
    if (page.url().includes('/edit')) {
      const errors = await page.locator('.alert-danger, .invalid-feedback').allTextContents().catch(() => []);
      throw new Error(`Mise a jour utilisateur non confirmee. ${errors.join(' | ') || ''}`.trim());
    }
  }, findings, 'Utilisateurs', '/utilisateurs'));

  results.push(await runWorkflowStep('Changer les parametres', async () => {
    await page.goto('/parametres', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    await page.fill('input[name="cabinet_name"]', `Cabinet Audit ${unique}`);
    await page.fill('input[name="cabinet_phone"]', `+2125${unique}11`).catch(() => null);
    await page.fill('input[name="cabinet_email"]', `cabinet.${unique}@medisys.test`).catch(() => null);
    const saveButton = page.locator('form[action$="/parametres"] button[type="submit"]').first();
    if (!(await saveButton.isVisible().catch(() => false))) {
      throw new Error('Bouton de sauvegarde des parametres introuvable.');
    }
    await saveButton.click();
    await waitForStable(page);
    const currentValue = await page.inputValue('input[name="cabinet_name"]').catch(() => '');
    if (!currentValue.includes(unique)) {
      throw new Error('La mise a jour des parametres ne semble pas persister.');
    }
  }, findings, 'Parametres', '/parametres'));

  captureWorkflowLogs(logs, findings);

  return {
    results,
    patientName: patientFullName,
    patientEmail,
    consultationEditUrl: consultationEditUrl ?? fixtures.consultationEditUrl,
    consultationId: consultationId ?? fixtures.consultationId,
  };
}

async function runWorkflowStep(
  name: string,
  action: () => Promise<void>,
  findings: Finding[],
  module: string,
  route: string,
): Promise<WorkflowResult> {
  const startedAt = Date.now();
  try {
    await action();
    return {
      name,
      status: 'passed',
      detail: 'Scenario execute sans blocage detecte.',
      durationMs: Date.now() - startedAt,
    };
  } catch (error) {
    const detail = error instanceof Error ? error.message : 'Echec workflow';
    findings.push({
      severity: 'critical',
      category: 'functionality',
      module,
      route,
      step: name,
      detail,
      repro: name,
    });
    return {
      name,
      status: 'failed',
      detail,
      durationMs: Date.now() - startedAt,
    };
  }
}

function captureWorkflowLogs(logs: RuntimeLogs, findings: Finding[]) {
  const uniqueJsErrors = [...new Set(logs.jsErrors)].slice(0, 5);
  const uniqueFailedRequests = [...new Set(logs.failedRequests)].slice(0, 5);
  if (uniqueJsErrors.length > 0) {
    findings.push({
      severity: 'major',
      category: 'technical',
      module: 'Global',
      step: 'workflows',
      detail: uniqueJsErrors.join(' | '),
      repro: 'Executer les workflows metier complets',
    });
  }
  if (uniqueFailedRequests.length > 0) {
    findings.push({
      severity: 'major',
      category: 'technical',
      module: 'Global',
      step: 'workflows reseau',
      detail: uniqueFailedRequests.join(' | '),
      repro: 'Executer les workflows metier complets',
    });
  }
}

async function waitForStable(page: Page) {
  await page.waitForTimeout(350);
  await page.waitForLoadState('networkidle', { timeout: 5_000 }).catch(() => null);
  await page.waitForTimeout(250);
}

async function collectPerformance(page: Page) {
  return page.evaluate(() => {
    const navigation = performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming | undefined;
    const paints = performance.getEntriesByType('paint');
    const fcp = paints.find((entry) => entry.name === 'first-contentful-paint');
    return {
      loadMs: Math.round(navigation?.duration ?? performance.now()),
      domContentLoadedMs: navigation ? Math.round(navigation.domContentLoadedEventEnd) : null,
      loadEventMs: navigation ? Math.round(navigation.loadEventEnd) : null,
      fcpMs: fcp ? Math.round(fcp.startTime) : null,
    };
  });
}

async function collectOverflow(page: Page) {
  return page.evaluate(() => {
    const hasOverflow = document.documentElement.scrollWidth > window.innerWidth + 2;
    if (!hasOverflow) {
      return { hasOverflow, nodes: [] as string[] };
    }

    const candidates = Array.from(document.querySelectorAll<HTMLElement>('body *'))
      .filter((element) => {
        const style = window.getComputedStyle(element);
        const rect = element.getBoundingClientRect();
        return style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.right > window.innerWidth + 2;
      })
      .slice(0, 8)
      .map((element) => {
        const text = (element.innerText || element.textContent || '').trim().replace(/\s+/g, ' ').slice(0, 50);
        const selector = [element.tagName.toLowerCase(), element.id ? `#${element.id}` : '', element.className ? `.${String(element.className).trim().split(/\s+/).slice(0, 2).join('.')}` : '']
          .join('')
          .trim();
        return `${selector || element.tagName.toLowerCase()} ${text}`.trim();
      });

    return { hasOverflow, nodes: candidates };
  });
}

async function collectUndersizedTargets(page: Page) {
  return page.evaluate(() => {
    const selectors = 'a, button, input, select, textarea, [role="button"], summary';
    return Array.from(document.querySelectorAll<HTMLElement>(selectors))
      .map((element) => {
        const style = window.getComputedStyle(element);
        const rect = element.getBoundingClientRect();
        return {
          visible: style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.height > 0,
          width: Math.round(rect.width),
          height: Math.round(rect.height),
          text: (element.innerText || element.getAttribute('aria-label') || element.getAttribute('title') || element.getAttribute('name') || element.tagName)
            .trim()
            .replace(/\s+/g, ' ')
            .slice(0, 40),
        };
      })
      .filter((item) => item.visible && (item.width < 44 || item.height < 44))
      .slice(0, 12)
      .map((item) => ({ text: item.text || 'element', width: item.width, height: item.height }));
  });
}

function snapshotLogs(logs: RuntimeLogs) {
  return {
    consoleErrors: logs.consoleErrors.length,
    jsErrors: logs.jsErrors.length,
    failedRequests: logs.failedRequests.length,
  };
}

function sliceLogs(logs: RuntimeLogs, snapshot: ReturnType<typeof snapshotLogs>) {
  return {
    consoleErrors: dedupe(logs.consoleErrors.slice(snapshot.consoleErrors)).slice(0, 5),
    jsErrors: dedupe(logs.jsErrors.slice(snapshot.jsErrors)).slice(0, 5),
    failedRequests: dedupe(logs.failedRequests.slice(snapshot.failedRequests)).slice(0, 5),
  };
}

function dedupe(values: string[]) {
  return [...new Set(values)];
}

function finalizeSummary(report: {
  findings: Finding[];
  workflows: WorkflowResult[];
  summary: {
    totals: Record<Severity, number>;
    byCategory: Record<string, number>;
    byModule: Record<string, number>;
    unstableModules: Array<{ module: string; count: number }>;
  };
}) {
  for (const finding of report.findings) {
    report.summary.totals[finding.severity] += 1;
    report.summary.byCategory[finding.category] = (report.summary.byCategory[finding.category] ?? 0) + 1;
    report.summary.byModule[finding.module] = (report.summary.byModule[finding.module] ?? 0) + 1;
  }

  const failedWorkflowCount = report.workflows.filter((workflow) => workflow.status === 'failed').length;
  if (failedWorkflowCount > 0) {
    report.summary.byModule['Workflows'] = (report.summary.byModule['Workflows'] ?? 0) + failedWorkflowCount;
  }

  report.summary.unstableModules = Object.entries(report.summary.byModule)
    .map(([module, count]) => ({ module, count }))
    .sort((left, right) => right.count - left.count)
    .slice(0, 5);
}

async function selectOptionByText(select: Locator, text: string) {
  const options = select.locator('option');
  const count = await options.count();
  const needle = text.toLowerCase();

  for (let index = 0; index < count; index += 1) {
    const option = options.nth(index);
    const label = ((await option.textContent()) ?? '').toLowerCase();
    const value = (await option.getAttribute('value')) ?? '';
    if (value.trim() !== '' && label.includes(needle)) {
      await select.selectOption(value);
      return value;
    }
  }

  return null;
}

async function ensureFirstSelectValue(select: Locator) {
  const options = select.locator('option');
  const count = await options.count();
  for (let index = 0; index < count; index += 1) {
    const option = options.nth(index);
    const value = (await option.getAttribute('value')) ?? '';
    if (value.trim() !== '') {
      await select.selectOption(value);
      return value;
    }
  }
  throw new Error('Aucune option selectable disponible.');
}

async function ensurePatientSelected(select: Locator, patientNom: string) {
  const value = await select.inputValue().catch(() => '');
  if (value) {
    return value;
  }
  return ensureOptionText(select, patientNom);
}

async function ensureOptionText(select: Locator, text: string) {
  const value = await selectOptionByText(select, text);
  if (!value) {
    throw new Error(`Option contenant ${text} introuvable.`);
  }
  return value;
}

async function setInputValue(page: Page, selector: string, value: string) {
  await page.evaluate(
    ({ selector: currentSelector, value: currentValue }) => {
      const input = document.querySelector<HTMLInputElement>(currentSelector);
      if (!input) {
        throw new Error(`Input ${currentSelector} introuvable`);
      }
      input.value = currentValue;
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    },
    { selector, value },
  );
}

function formatDate(date: Date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}
