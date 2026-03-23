import fs from 'node:fs';
import path from 'node:path';
import { chromium } from '@playwright/test';

const baseUrl = process.env.E2E_BASE_URL || 'http://cabinet-medical-laravel.test';
const adminEmail = process.env.E2E_ADMIN_EMAIL || 'admin@medisys.test';
const adminPassword = process.env.E2E_ADMIN_PASSWORD || 'password';
const medecinEmail = process.env.E2E_MEDECIN_EMAIL || 'medecin@medisys.test';
const medecinPassword = process.env.E2E_MEDECIN_PASSWORD || 'password';

const desktopViewports = [
  { name: 'desktop-1280', width: 1280, height: 900, kind: 'desktop' },
  { name: 'desktop-1440', width: 1440, height: 900, kind: 'desktop' },
  { name: 'desktop-1920', width: 1920, height: 1080, kind: 'desktop' },
];
const mobileViewports = [
  { name: 'mobile-320', width: 320, height: 740, kind: 'mobile' },
  { name: 'mobile-375', width: 375, height: 812, kind: 'mobile' },
  { name: 'mobile-390', width: 390, height: 844, kind: 'mobile' },
  { name: 'mobile-414', width: 414, height: 896, kind: 'mobile' },
  { name: 'mobile-768', width: 768, height: 1024, kind: 'mobile' },
];

const modules = [
  { key: 'dashboard', label: 'Dashboard', route: '/dashboard' },
  { key: 'patients', label: 'Patients', route: '/patients' },
  { key: 'consultations', label: 'Consultations', route: '/consultations' },
  { key: 'agenda', label: 'Agenda medical', route: '/agenda' },
  { key: 'salle_attente', label: 'Salle d’attente intelligente', route: '/salle-attente' },
  { key: 'ordonnances', label: 'Ordonnances', route: '/ordonnances' },
  { key: 'utilisateurs', label: 'Utilisateurs', route: '/utilisateurs' },
  { key: 'parametres', label: 'Parametres', route: '/parametres' },
  { key: 'facturation', label: 'Facturation', route: '/factures' },
  { key: 'documents', label: 'Documents', route: '/documents' },
  { key: 'statistiques', label: 'Statistiques', route: '/statistiques' },
  { key: 'rapports', label: 'Rapports', route: '/rapports' },
];

const report = {
  generatedAt: new Date().toISOString(),
  baseUrl,
  checklist: 'QA ULTRA 120 tests',
  pageAudits: [],
  workflows: [],
  security: [],
  findings: [],
};

function pushFinding(finding) {
  report.findings.push(finding);
}

function dedupe(values) {
  return [...new Set(values)];
}

async function login(page, email, password) {
  await page.goto(baseUrl + '/login', { waitUntil: 'domcontentloaded' });
  await page.locator('input[name="email"]').fill(email);
  await page.locator('input[name="password"]').fill(password);
  await Promise.all([
    page.waitForLoadState('networkidle').catch(() => null),
    page.locator('button[type="submit"]').click(),
  ]);
}

async function waitForStable(page) {
  await page.waitForTimeout(300);
  await page.waitForLoadState('networkidle', { timeout: 6000 }).catch(() => null);
  await page.waitForTimeout(250);
}

async function collectPerf(page) {
  return page.evaluate(() => {
    const nav = performance.getEntriesByType('navigation')[0];
    const paints = performance.getEntriesByType('paint');
    const fcp = paints.find((entry) => entry.name === 'first-contentful-paint');
    return {
      loadMs: Math.round(nav?.duration ?? performance.now()),
      domContentLoadedMs: nav ? Math.round(nav.domContentLoadedEventEnd) : null,
      loadEventMs: nav ? Math.round(nav.loadEventEnd) : null,
      fcpMs: fcp ? Math.round(fcp.startTime) : null,
    };
  });
}

async function collectOverflow(page) {
  return page.evaluate(() => {
    const hasOverflow = document.documentElement.scrollWidth > window.innerWidth + 2;
    const nodes = !hasOverflow ? [] : Array.from(document.querySelectorAll('body *'))
      .filter((element) => {
        const style = getComputedStyle(element);
        const rect = element.getBoundingClientRect();
        return style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.right > window.innerWidth + 2;
      })
      .slice(0, 8)
      .map((element) => {
        const tag = element.tagName.toLowerCase();
        const id = element.id ? `#${element.id}` : '';
        const cls = element.className ? `.${String(element.className).trim().split(/\s+/).slice(0, 2).join('.')}` : '';
        const text = (element.textContent || '').trim().replace(/\s+/g, ' ').slice(0, 50);
        return `${tag}${id}${cls} ${text}`.trim();
      });
    return { hasOverflow, nodes };
  });
}

async function collectTargets(page, mobile) {
  return page.evaluate((isMobile) => {
    const selectors = 'a, button, input, select, textarea, [role="button"], summary';
    return Array.from(document.querySelectorAll(selectors))
      .map((element) => {
        const style = getComputedStyle(element);
        const rect = element.getBoundingClientRect();
        const text = (element.innerText || element.getAttribute('aria-label') || element.getAttribute('title') || element.getAttribute('name') || element.tagName)
          .trim().replace(/\s+/g, ' ').slice(0, 50);
        return {
          visible: style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.height > 0,
          width: Math.round(rect.width),
          height: Math.round(rect.height),
          text,
        };
      })
      .filter((item) => item.visible && (isMobile ? (item.width < 44 || item.height < 44) : (item.width < 36 || item.height < 36)))
      .slice(0, 12);
  }, mobile);
}

async function collectUiSignals(page) {
  return page.evaluate(() => ({
    title: document.title,
    h1: document.querySelector('h1')?.textContent?.trim() || '',
    sidebarVisible: !!document.querySelector('.sidebar, #sidebar, [data-sidebar]'),
    topbarVisible: !!document.querySelector('.app-topbar, .topbar, header'),
    cards: document.querySelectorAll('.card, .dashboard-card, .stat-card, .ui-card').length,
    charts: document.querySelectorAll('canvas, svg').length,
    forms: document.querySelectorAll('form').length,
  }));
}

async function auditPage(page, module, viewport, logs) {
  const before = {
    consoleErrors: logs.consoleErrors.length,
    jsErrors: logs.jsErrors.length,
    failedRequests: logs.failedRequests.length,
  };
  const startedAt = Date.now();
  let status = 'ok';
  try {
    await page.goto(baseUrl + module.route, { waitUntil: 'domcontentloaded', timeout: 45000 });
  } catch (error) {
    pushFinding({
      module: module.label,
      page: module.route,
      screen: viewport.name,
      step: 'Ouvrir la page',
      expected: 'La page se charge.',
      observed: error instanceof Error ? error.message : 'Navigation impossible',
      severity: 'critique',
      recommendation: 'Verifier la route, le middleware et les dependances de chargement.',
      category: 'navigation',
    });
    return;
  }
  await waitForStable(page);
  if (page.url().includes('/login')) {
    status = 'blocked';
    pushFinding({
      module: module.label,
      page: module.route,
      screen: viewport.name,
      step: 'Ouvrir la page apres connexion admin',
      expected: 'Acces autorise pour le profil admin.',
      observed: 'Redirection vers /login.',
      severity: 'critique',
      recommendation: 'Verifier la session, les permissions ou la logique de redirection.',
      category: 'navigation',
    });
  }
  const perf = await collectPerf(page);
  const overflow = await collectOverflow(page);
  const targets = await collectTargets(page, viewport.kind === 'mobile');
  const ui = await collectUiSignals(page);
  const pageLogs = {
    consoleErrors: dedupe(logs.consoleErrors.slice(before.consoleErrors)),
    jsErrors: dedupe(logs.jsErrors.slice(before.jsErrors)),
    failedRequests: dedupe(logs.failedRequests.slice(before.failedRequests)),
  };
  if (overflow.hasOverflow) {
    status = 'issue';
    pushFinding({
      module: module.label,
      page: module.route,
      screen: viewport.name,
      step: 'Verifier le responsive et l affichage',
      expected: 'Aucun scroll horizontal.',
      observed: `Debordement horizontal detecte: ${overflow.nodes.join(' | ') || 'elements non identifies'}`,
      severity: viewport.kind === 'mobile' ? 'majeur' : 'mineur',
      recommendation: 'Revoir les largeurs minimales, grilles et conteneurs forcant la largeur.',
      category: 'responsive',
    });
  }
  if (targets.length > 0) {
    status = 'issue';
    pushFinding({
      module: module.label,
      page: module.route,
      screen: viewport.name,
      step: 'Verifier l accessibilite des actions',
      expected: viewport.kind === 'mobile' ? 'Actions >= 44x44.' : 'Actions facilement cliquables.',
      observed: targets.map((t) => `${t.text} (${t.width}x${t.height})`).join(' | '),
      severity: viewport.kind === 'mobile' ? 'majeur' : 'mineur',
      recommendation: 'Augmenter la hauteur/largeur minimale et la zone interactive des controles.',
      category: 'uiux',
    });
  }
  if (pageLogs.jsErrors.length > 0) {
    status = 'issue';
    pushFinding({
      module: module.label,
      page: module.route,
      screen: viewport.name,
      step: 'Verifier la console navigateur',
      expected: 'Aucune erreur JavaScript.',
      observed: pageLogs.jsErrors.slice(0, 3).join(' | '),
      severity: 'majeur',
      recommendation: 'Corriger les erreurs runtime avant mise en production.',
      category: 'technical',
    });
  }
  if (pageLogs.failedRequests.length > 0) {
    status = 'issue';
    pushFinding({
      module: module.label,
      page: module.route,
      screen: viewport.name,
      step: 'Verifier le chargement reseau',
      expected: 'Aucune requete critique en echec.',
      observed: pageLogs.failedRequests.slice(0, 3).join(' | '),
      severity: 'majeur',
      recommendation: 'Verifier les endpoints, la gestion d erreurs et les appels frontend.',
      category: 'technical',
    });
  }
  if (perf.loadMs > 5000 || (perf.domContentLoadedMs && perf.domContentLoadedMs > 3000)) {
    status = 'issue';
    pushFinding({
      module: module.label,
      page: module.route,
      screen: viewport.name,
      step: 'Mesurer le temps de chargement',
      expected: 'Chargement fluide sous seuil.',
      observed: `load=${perf.loadMs}ms dom=${perf.domContentLoadedMs ?? 'n/a'}ms fcp=${perf.fcpMs ?? 'n/a'}ms`,
      severity: perf.loadMs > 7000 ? 'majeur' : 'mineur',
      recommendation: 'Profiler la page, reduire les requetes et simplifier le rendu initial.',
      category: 'performance',
    });
  }
  report.pageAudits.push({
    module: module.label,
    route: module.route,
    viewport: viewport.name,
    kind: viewport.kind,
    status,
    loadMs: perf.loadMs,
    domContentLoadedMs: perf.domContentLoadedMs,
    fcpMs: perf.fcpMs,
    hasOverflow: overflow.hasOverflow,
    overflowNodes: overflow.nodes,
    undersizedTargets: targets,
    consoleErrors: pageLogs.consoleErrors,
    jsErrors: pageLogs.jsErrors,
    failedRequests: pageLogs.failedRequests,
    ui,
    durationMs: Date.now() - startedAt,
  });
}

async function runWorkflow(name, module, pagePath, action) {
  const startedAt = Date.now();
  try {
    await action();
    report.workflows.push({ name, module, page: pagePath, status: 'passed', detail: 'Scenario execute sans blocage detecte.', durationMs: Date.now() - startedAt });
  } catch (error) {
    const detail = error instanceof Error ? error.message : 'Echec workflow';
    report.workflows.push({ name, module, page: pagePath, status: 'failed', detail, durationMs: Date.now() - startedAt });
    pushFinding({
      module,
      page: pagePath,
      screen: 'desktop-1440',
      step: name,
      expected: 'Le workflow se termine sans erreur ni blocage.',
      observed: detail,
      severity: 'critique',
      recommendation: 'Corriger la logique metier ou le formulaire bloque.',
      category: 'functionality',
    });
  }
}

async function firstNonEmptyOption(select) {
  const options = await select.locator('option').evaluateAll((nodes) => nodes.map((n) => ({ value: n.getAttribute('value') || '', text: (n.textContent || '').trim() })));
  const match = options.find((option) => option.value.trim() !== '');
  if (!match) throw new Error('Aucune option selectable disponible.');
  await select.selectOption(match.value);
  return match.value;
}

async function selectOptionContaining(select, needle) {
  const lower = needle.toLowerCase();
  const options = await select.locator('option').evaluateAll((nodes) => nodes.map((n) => ({ value: n.getAttribute('value') || '', text: (n.textContent || '').trim() })));
  const match = options.find((option) => option.value.trim() !== '' && option.text.toLowerCase().includes(lower));
  if (!match) return null;
  await select.selectOption(match.value);
  return match.value;
}

function formatDate(date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await context.newPage();
  const logs = { consoleErrors: [], jsErrors: [], failedRequests: [] };
  page.on('console', (msg) => { if (msg.type() === 'error') logs.consoleErrors.push(msg.text()); });
  page.on('pageerror', (error) => logs.jsErrors.push(String(error.message || error)));
  page.on('requestfailed', (request) => logs.failedRequests.push(`${request.method()} ${request.url()} - ${request.failure()?.errorText || 'failed'}`));

  await page.goto(baseUrl + '/dashboard', { waitUntil: 'domcontentloaded' }).catch(() => null);
  report.security.push({ test: 'Acces sans login /dashboard', redirectedToLogin: page.url().includes('/login') });
  if (!page.url().includes('/login')) {
    pushFinding({
      module: 'Securite',
      page: '/dashboard',
      screen: 'desktop-1280',
      step: 'Acces sans authentification',
      expected: 'Redirection vers /login.',
      observed: `URL obtenue: ${page.url()}`,
      severity: 'critique',
      recommendation: 'Proteger la route par auth middleware.',
      category: 'technical',
    });
  }

  await login(page, adminEmail, adminPassword);
  await waitForStable(page);

  for (const viewport of [...desktopViewports, ...mobileViewports]) {
    await page.setViewportSize({ width: viewport.width, height: viewport.height });
    for (const module of modules) {
      await auditPage(page, module, viewport, logs);
    }
  }

  const unique = Date.now().toString().slice(-6);
  const patientNom = `Audit${unique}`;
  const patientPrenom = 'Ultra';
  const patientEmail = `audit.${unique}@medisys.test`;
  const patientTel = `+2126${unique}88`;
  const patientCin = `QA${unique}`;
  const today = formatDate(new Date());
  let consultationEditUrl = null;
  let consultationId = null;

  await page.setViewportSize({ width: 1440, height: 900 });

  await runWorkflow('1 creer un patient', 'Patients', '/patients/create', async () => {
    await page.goto(baseUrl + '/patients/create', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    await page.fill('input[name="nom"]', patientNom);
    await page.fill('input[name="prenom"]', patientPrenom);
    await page.fill('input[name="date_naissance"]', '1990-05-20');
    const male = page.locator('input[name="genre"][value="M"]').first();
    if (await male.count()) await male.check();
    await page.fill('input[name="telephone"]', patientTel);
    await page.fill('input[name="cin"]', patientCin);
    await page.fill('input[name="email"]', patientEmail).catch(() => null);
    await page.fill('input[name="adresse"]', 'Adresse audit QA').catch(() => null);
    await page.locator('form[action*="/patients"] button[type="submit"]').first().click();
    await waitForStable(page);
    await page.goto(baseUrl + '/patients', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    if (!(await page.locator(`text=${patientNom}`).first().isVisible().catch(() => false))) {
      throw new Error('Patient cree mais introuvable dans la liste.');
    }
  });

  await runWorkflow('2 creer un rendez-vous', 'Agenda medical', '/rendezvous/create', async () => {
    await page.goto(baseUrl + '/rendezvous/create', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const patientSelect = page.locator('#patientSelect');
    const patientValue = await selectOptionContaining(patientSelect, patientNom);
    if (!patientValue) throw new Error('Patient introuvable dans la liste rendez-vous.');
    const medecinRadio = page.locator('input[name="medecin_id"]').first();
    if (!(await medecinRadio.count())) throw new Error('Aucun medecin selectable.');
    await medecinRadio.check();
    const motifRadio = page.locator('input[name="motif"]').first();
    if (!(await motifRadio.count())) throw new Error('Aucun motif selectable.');
    await motifRadio.check();
    await page.evaluate((date) => { const i = document.querySelector('#inputDate'); if (i) { i.value = date; i.dispatchEvent(new Event('input', { bubbles: true })); i.dispatchEvent(new Event('change', { bubbles: true })); } }, today);
    await page.evaluate(() => { const i = document.querySelector('#inputTime'); if (i) { i.value = '10:00'; i.dispatchEvent(new Event('input', { bubbles: true })); i.dispatchEvent(new Event('change', { bubbles: true })); } });
    await page.fill('#notes', 'Rendez-vous audit QA').catch(() => null);
    await page.click('#submitBtn');
    await waitForStable(page);
    if (page.url().includes('/rendezvous/create')) throw new Error('Le rendez-vous n a pas ete enregistre.');
  });

  await runWorkflow('3 deplacer patient salle d attente', 'Salle d’attente intelligente', '/salle-attente', async () => {
    await page.goto(baseUrl + '/salle-attente', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    await page.fill('#wr-date', today);
    await page.fill('#wr-search', patientNom);
    await page.click('#wr-refresh');
    await page.waitForTimeout(1500);
    const card = page.locator('.wr-list[data-status="a_venir"] .wr-patient-card').filter({ hasText: patientNom }).first();
    if (!(await card.isVisible().catch(() => false))) throw new Error('Patient absent de la colonne A venir.');
    await card.locator('[data-action="call"]').click();
    await page.waitForTimeout(1500);
    if (!(await page.locator('.wr-list[data-status="en_attente"] .wr-patient-card').filter({ hasText: patientNom }).first().isVisible().catch(() => false))) {
      throw new Error('Le patient n a pas bascule en attente.');
    }
  });

  await runWorkflow('4 demarrer consultation', 'Salle d’attente intelligente', '/salle-attente', async () => {
    await page.goto(baseUrl + '/salle-attente', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    await page.fill('#wr-date', today);
    await page.fill('#wr-search', patientNom);
    await page.click('#wr-refresh');
    await page.waitForTimeout(1200);
    const waitingCard = page.locator('.wr-list[data-status="en_attente"] .wr-patient-card').filter({ hasText: patientNom }).first();
    if (!(await waitingCard.isVisible().catch(() => false))) throw new Error('Patient non present en attente.');
    await waitingCard.locator('[data-action="start"]').click();
    await page.waitForTimeout(1500);
    if (!(await page.locator('.wr-list[data-status="en_soins"] .wr-patient-card').filter({ hasText: patientNom }).first().isVisible().catch(() => false))) {
      throw new Error('Le patient n a pas bascule en consultation.');
    }
  });

  await runWorkflow('5 creer consultation + assistant IA', 'Consultations', '/consultations/create', async () => {
    await page.goto(baseUrl + '/salle-attente', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    await page.fill('#wr-date', today);
    await page.fill('#wr-search', patientNom);
    await page.click('#wr-refresh');
    await page.waitForTimeout(1200);
    const activeCard = page.locator('.wr-list[data-status="en_soins"] .wr-patient-card').filter({ hasText: patientNom }).first();
    if (!(await activeCard.isVisible().catch(() => false))) throw new Error('Patient introuvable en consultation.');
    await activeCard.locator('[data-action="consultation"]').click();
    await page.waitForURL(/\/consultations\/create/);
    await waitForStable(page);
    const patientSelect = page.locator('#patient_id');
    if (!(await patientSelect.inputValue().catch(() => ''))) {
      const ok = await selectOptionContaining(patientSelect, patientNom);
      if (!ok) throw new Error('Patient introuvable dans le select consultation.');
    }
    await firstNonEmptyOption(page.locator('#medecin_id'));
    await page.fill('#date_consultation', today);
    await page.fill('#symptomes', 'Cefalees et fatigue');
    await page.fill('#diagnostic', 'Syndrome viral benin');
    await page.fill('textarea[name="examen_clinique"]', 'Examen stable').catch(() => null);
    await page.fill('textarea[name="traitement_prescrit"]', 'Repos et hydratation').catch(() => null);
    await page.fill('textarea[name="recommandations"]', 'Controle si aggravation').catch(() => null);
    await page.locator('button[type="submit"]').filter({ hasText: /Enregistrer/i }).first().click();
    await waitForStable(page);
    if (page.url().includes('/consultations/create')) throw new Error('La consultation n a pas ete enregistree.');
    await page.goto(baseUrl + '/consultations', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const row = page.locator('tr, .consultation-card, .cs-card').filter({ hasText: patientNom }).first();
    if (!(await row.isVisible().catch(() => false))) throw new Error('Consultation creee mais introuvable dans la liste.');
    const editLink = row.locator('a[href*="/consultations/"][href$="/edit"]').first();
    if (!(await editLink.isVisible().catch(() => false))) throw new Error('Lien de modification consultation introuvable.');
    await editLink.click();
    await page.waitForURL(/\/consultations\/\d+\/edit/);
    await waitForStable(page);
    consultationEditUrl = page.url();
    consultationId = page.url().match(/consultations\/(\d+)\/edit/)?.[1] || null;
    const assistantRoot = page.locator('[data-generate-url]').first();
    if (!(await assistantRoot.isVisible().catch(() => false))) throw new Error('Bloc assistant IA introuvable.');
    await page.locator('[data-ai-source]').first().fill('Patient stable. Resume clinique pour test automatique.');
    await page.locator('[data-ai-action="summary"]').first().click();
    await page.waitForTimeout(2500);
    const summary = await page.locator('[data-ai-summary-result]').first().inputValue().catch(() => '');
    if (!summary.trim()) throw new Error('Aucun contenu IA genere apres clic sur resume.');
  });

  await runWorkflow('6 creer ordonnance', 'Ordonnances', '/ordonnances/create', async () => {
    await page.goto(baseUrl + `/ordonnances/create${consultationId ? `?consultation_id=${consultationId}` : ''}`, { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const consultationSelect = page.locator('#consultation_id');
    if ((await consultationSelect.count()) && !(await consultationSelect.inputValue().catch(() => ''))) await firstNonEmptyOption(consultationSelect);
    const medecinSelect = page.locator('select[name="medecin_id"]');
    if ((await medecinSelect.count()) && !(await medecinSelect.inputValue().catch(() => ''))) await firstNonEmptyOption(medecinSelect);
    await page.fill('#date_prescription', today);
    await page.fill('#diagnostic', 'Traitement symptomatique');
    await page.fill('#instructions', 'Repos et hydratation').catch(() => null);
    const medSearch = page.locator('.js-medication-search').first();
    await medSearch.fill('a');
    await page.waitForTimeout(700);
    const medOption = page.locator('.js-medication-results [data-medication-id]').first();
    if (!(await medOption.isVisible().catch(() => false))) throw new Error('Aucun medicament propose.');
    await medOption.click();
    await page.fill('input[name="medicaments[0][posologie]"]', '1 comprime matin et soir');
    await page.fill('input[name="medicaments[0][duree]"]', '5 jours');
    await page.fill('input[name="medicaments[0][quantite]"]', '10').catch(() => null);
    await page.locator('button[type="submit"]').first().click();
    await waitForStable(page);
    if (page.url().includes('/ordonnances/create')) throw new Error('L ordonnance n a pas ete enregistree.');
  });

  await runWorkflow('7 generer facture', 'Facturation', '/factures/create', async () => {
    await page.goto(baseUrl + '/factures/create', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const patientSelect = page.locator('#selectPatient');
    const patientValue = await selectOptionContaining(patientSelect, patientNom);
    if (!patientValue) throw new Error('Patient introuvable dans la liste facturation.');
    await firstNonEmptyOption(page.locator('#selectMedecin')).catch(() => null);
    await page.fill('input[name="prestations[0][description]"]', 'Consultation QA preproduction');
    await page.fill('input[name="prestations[0][quantite]"]', '1');
    await page.fill('input[name="prestations[0][prix_unitaire]"]', '250');
    await page.fill('input[name="date_facture"]', today);
    await page.click('button[type="submit"][name="action"][value="en_attente"]');
    await waitForStable(page);
    if (page.url().includes('/factures/create')) throw new Error('La facture n a pas ete creee.');
  });

  await runWorkflow('8 modifier utilisateur', 'Utilisateurs', '/utilisateurs', async () => {
    await page.goto(baseUrl + '/utilisateurs', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    const editLink = page.locator('a[href*="/utilisateurs/"][href$="/edit"]').first();
    if (!(await editLink.isVisible().catch(() => false))) throw new Error('Aucun utilisateur editable accessible.');
    await editLink.click();
    await page.waitForURL(/\/utilisateurs\/\d+\/edit/);
    await waitForStable(page);
    const firstName = page.locator('input[name="first_name"]');
    const current = await firstName.inputValue().catch(() => '');
    await firstName.fill(`${current} QA`.trim());
    await page.locator('button[type="submit"]').filter({ hasText: /Enregistrer|Sauvegarder/i }).first().click();
    await waitForStable(page);
    if (page.url().includes('/edit')) {
      const errors = await page.locator('.alert-danger, .invalid-feedback').allTextContents().catch(() => []);
      throw new Error(`Mise a jour utilisateur non confirmee. ${errors.join(' | ') || ''}`.trim());
    }
  });

  await runWorkflow('9 changer parametres', 'Parametres', '/parametres', async () => {
    await page.goto(baseUrl + '/parametres', { waitUntil: 'domcontentloaded' });
    await waitForStable(page);
    await page.fill('input[name="cabinet_name"]', `Cabinet Audit ${unique}`);
    await page.fill('input[name="cabinet_phone"]', `+2125${unique}11`).catch(() => null);
    await page.fill('input[name="cabinet_email"]', `cabinet.${unique}@medisys.test`).catch(() => null);
    const saveButton = page.locator('form[action$="/parametres"] button[type="submit"]').first();
    if (!(await saveButton.isVisible().catch(() => false))) throw new Error('Bouton de sauvegarde des parametres introuvable.');
    await saveButton.click();
    await waitForStable(page);
    const value = await page.locator('input[name="cabinet_name"]').inputValue().catch(() => '');
    if (!value.includes(unique)) throw new Error('La mise a jour des parametres ne semble pas persister.');
  });

  const medecinContext = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const medecinPage = await medecinContext.newPage();
  await login(medecinPage, medecinEmail, medecinPassword);
  await waitForStable(medecinPage);
  await medecinPage.goto(baseUrl + '/factures', { waitUntil: 'domcontentloaded' });
  await waitForStable(medecinPage);
  const forbidden = await medecinPage.locator('body').textContent();
  report.security.push({ test: 'Permission medecin sur factures', blocked: /403|Acces refuse|forbidden/i.test(forbidden || '') });
  if (!/403|Acces refuse|forbidden/i.test(forbidden || '')) {
    pushFinding({
      module: 'Securite',
      page: '/factures',
      screen: 'desktop-1280',
      step: 'Connexion medecin sans droit facturation',
      expected: 'Acces refuse.',
      observed: 'Le module factures reste accessible.',
      severity: 'critique',
      recommendation: 'Verifier le middleware module.access:facturation et la matrice des roles.',
      category: 'technical',
    });
  }
  await medecinContext.close();

  const outPath = path.resolve('storage/test-reports/qa-ultra-audit.json');
  fs.mkdirSync(path.dirname(outPath), { recursive: true });
  fs.writeFileSync(outPath, JSON.stringify(report, null, 2), 'utf8');
  console.log(JSON.stringify({ findings: report.findings.length, workflows: report.workflows.length, reportPath: outPath }, null, 2));
  await context.close();
  await browser.close();
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
