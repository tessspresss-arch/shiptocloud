import fs from 'node:fs';
import path from 'node:path';
import { expect, test } from '@playwright/test';

const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@medisys.test';
const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password';

test('AI audit - creation patient et diagnostic des bugs', async ({ page }) => {
  test.slow();

  const issues: Array<{ severity: 'critical' | 'high' | 'medium'; step: string; detail: string }> = [];
  const jsErrors: string[] = [];
  const consoleErrors: string[] = [];
  const failedRequests: string[] = [];
  const unique = Date.now().toString().slice(-6);
  const patientNom = `AI${unique}`;
  const patientPrenom = 'Testeur';
  const patientCin = `AI${unique}`;
  const patientTel = `+2126${unique}77`;

  const markIssue = (severity: 'critical' | 'high' | 'medium', step: string, detail: string) => {
    issues.push({ severity, step, detail });
  };

  page.on('pageerror', (error) => {
    jsErrors.push(error.message);
  });

  page.on('console', (msg) => {
    if (msg.type() === 'error') {
      consoleErrors.push(msg.text());
    }
  });

  page.on('requestfailed', (request) => {
    failedRequests.push(`${request.method()} ${request.url()} - ${request.failure()?.errorText ?? 'failed'}`);
  });

  // 1) Login
  await page.goto('/login');
  if (!(await page.locator('input[name="email"]').isVisible())) {
    markIssue('critical', 'login', 'Champ email introuvable.');
  } else {
    await page.fill('input[name="email"]', adminEmail);
  }

  if (!(await page.locator('input[name="password"]').isVisible())) {
    markIssue('critical', 'login', 'Champ mot de passe introuvable.');
  } else {
    await page.fill('input[name="password"]', adminPassword);
  }

  const loginSubmit = page.locator('button[type="submit"]');
  if (!(await loginSubmit.isVisible())) {
    markIssue('critical', 'login', 'Bouton de connexion introuvable.');
  } else {
    await loginSubmit.click();
    await page.waitForLoadState('networkidle');
  }

  const stillOnLogin = page.url().includes('/login');
  const loginError = await page.locator('.alert-danger, .invalid-feedback').first().isVisible().catch(() => false);
  if (stillOnLogin || loginError) {
    markIssue('critical', 'login', 'Echec de connexion: identifiant ou mot de passe invalide, ou blocage auth.');
  }

  // 2) Open patient create page
  await page.goto('/patients/create');
  await page.waitForLoadState('networkidle');

  if (page.url().includes('/login')) {
    markIssue('critical', 'navigation', 'Redirection vers login sur /patients/create (permission/session).');
  }

  const requiredSelectors = [
    'form[action*="/patients"]',
    'input[name="nom"]',
    'input[name="prenom"]',
    'input[name="date_naissance"]',
    'input[name="genre"][value="M"]',
    'input[name="telephone"]',
    'form[action*="/patients"] button[type="submit"]',
  ];

  for (const selector of requiredSelectors) {
    const isVisible = await page.locator(selector).first().isVisible().catch(() => false);
    if (!isVisible) {
      markIssue('high', 'form', `Element requis manquant: ${selector}`);
    }
  }

  // 3) Fill form
  if (issues.filter((i) => i.step === 'form' && i.severity !== 'medium').length === 0) {
    await page.fill('input[name="nom"]', patientNom);
    await page.fill('input[name="prenom"]', patientPrenom);
    await page.fill('input[name="date_naissance"]', '1990-05-20');
    await page.check('input[name="genre"][value="M"]');
    await page.fill('input[name="cin"]', patientCin);
    await page.fill('input[name="telephone"]', patientTel);
    await page.fill('input[name="email"]', `ai.${unique}@medisys.test`);
    await page.fill('input[name="adresse"]', 'Adresse IA test');
    await page.fill('input[name="ville"]', 'Casablanca');

    const assuranceSelect = page.locator('select[name="assurance_medicale"]');
    if (await assuranceSelect.isVisible().catch(() => false)) {
      await assuranceSelect.selectOption({ label: 'CNSS' }).catch(() => undefined);
    }

    const patientForm = page.locator('form[action*="/patients"]').first();
    const patientSubmit = patientForm.locator('button[type="submit"]').first();
    await patientSubmit.click();
    await page.waitForLoadState('networkidle');
  }

  // 4) Post-submit checks
  if (page.url().includes('/patients/create')) {
    const errorText = await page.locator('.invalid-feedback, .text-danger, .alert-danger').allTextContents().catch(() => []);
    markIssue(
      'high',
      'submit',
      `Formulaire non soumis ou validation bloquante. Details: ${errorText.join(' | ') || 'n/a'}`
    );
  } else if (!page.url().includes('/patients')) {
    markIssue('medium', 'submit', `URL inattendue apres soumission: ${page.url()}`);
  }

  const createdVisible = await page.locator(`text=${patientNom}`).first().isVisible().catch(() => false);
  if (!createdVisible) {
    markIssue('medium', 'verification', `Patient cree non visible dans la liste (${patientNom}).`);
  }

  // Technical diagnostics
  jsErrors.forEach((err) => markIssue('high', 'javascript', err));
  consoleErrors.forEach((err) => markIssue('medium', 'console', err));
  failedRequests.forEach((req) => markIssue('medium', 'network', req));

  const report = {
    generated_at: new Date().toISOString(),
    scenario: 'Creation patient via interface',
    credentials_used: {
      email: adminEmail,
      password_masked: adminPassword ? '***' : '',
    },
    patient_payload: {
      nom: patientNom,
      prenom: patientPrenom,
      cin: patientCin,
      telephone: patientTel,
    },
    url_finale: page.url(),
    status: issues.length > 0 ? 'failed' : 'passed',
    issue_count: issues.length,
    issues,
  };

  const reportDir = path.resolve('storage/test-reports');
  fs.mkdirSync(reportDir, { recursive: true });
  fs.writeFileSync(path.join(reportDir, 'patient-create-ai-report.json'), JSON.stringify(report, null, 2), 'utf8');

  await page.screenshot({ path: path.join(reportDir, 'patient-create-ai-final.png'), fullPage: true });

  expect(
    issues,
    `Des anomalies ont ete detectees pendant la creation patient. Consultez storage/test-reports/patient-create-ai-report.json`
  ).toEqual([]);
});
