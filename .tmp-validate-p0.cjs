const { chromium } = require('playwright');

(async () => {
  const base = 'http://cabinet-medical-laravel.test';
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
  const unique = Date.now().toString().slice(-6);
  const patientNom = `Fix${unique}`;
  const patientPrenom = 'QA';
  const today = new Date().toISOString().slice(0, 10);

  const wait = async () => {
    await page.waitForTimeout(300);
    await page.waitForLoadState('networkidle', { timeout: 5000 }).catch(() => null);
    await page.waitForTimeout(250);
  };

  const selectContaining = async (select, needle) => {
    const lower = needle.toLowerCase();
    const options = await select.locator('option').evaluateAll((nodes) =>
      nodes.map((node) => ({
        value: node.getAttribute('value') || '',
        text: (node.textContent || '').trim(),
      }))
    );
    const match = options.find((option) => option.value.trim() !== '' && option.text.toLowerCase().includes(lower));
    if (!match) return null;
    await select.selectOption(match.value);
    return match.value;
  };

  await page.goto(base + '/login');
  await page.fill('input[name=email]', 'admin@medisys.test');
  await page.fill('input[name=password]', 'password');
  await Promise.all([
    page.waitForURL('**/dashboard'),
    page.click('button[type=submit]'),
  ]);

  await page.goto(base + '/patients/create');
  await wait();
  await page.fill('input[name=nom]', patientNom);
  await page.fill('input[name=prenom]', patientPrenom);
  await page.fill('input[name=date_naissance]', '1990-05-20');
  await page.locator('input[name=genre][value=M]').first().check().catch(() => null);
  await page.fill('input[name=telephone]', '+212600' + unique);
  await page.fill('input[name=cin]', 'FX' + unique);
  await page.locator('form[action*="/patients"] button[type=submit]').first().click();
  await wait();

  await page.goto(base + '/rendezvous/create');
  await wait();
  const patientSelect = page.locator('#patientSelect');
  const patientValue = await selectContaining(patientSelect, patientNom);
  if (!patientValue) throw new Error('Patient introuvable dans RDV');
  await page.locator('input[name="medecin_id"]').first().check();
  await page.locator('input[name="motif"]').first().check();
  await page.evaluate((date) => {
    const input = document.querySelector('#inputDate');
    if (input) {
      input.value = date;
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }, today);
  await page.evaluate(() => {
    const input = document.querySelector('#inputTime');
    if (input) {
      input.value = '10:00';
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }
  });
  await page.waitForFunction(() => {
    const button = document.querySelector('#submitBtn');
    return !!button && !button.disabled;
  });
  await page.click('#submitBtn');
  await wait();
  const rdvCreated = !page.url().includes('/rendezvous/create');

  await page.goto(base + '/salle-attente');
  await wait();
  await page.fill('#wr-date', today);
  await page.fill('#wr-search', patientNom);
  await page.click('#wr-refresh');
  await page.waitForTimeout(1500);
  const aVenirCard = page.locator('.wr-list[data-status="a_venir"] .wr-patient-card').filter({ hasText: patientNom }).first();
  const aVenir = await aVenirCard.isVisible().catch(() => false);
  if (aVenir) {
    await aVenirCard.locator('[data-action="call"]').click();
    await page.waitForTimeout(1500);
  }

  const enAttenteCard = page.locator('.wr-list[data-status="en_attente"] .wr-patient-card').filter({ hasText: patientNom }).first();
  const enAttente = await enAttenteCard.isVisible().catch(() => false);
  if (enAttente) {
    await enAttenteCard.locator('[data-action="start"]').click();
    await page.waitForTimeout(1500);
  }

  const enSoinsCard = page.locator('.wr-list[data-status="en_soins"] .wr-patient-card').filter({ hasText: patientNom }).first();
  const enSoins = await enSoinsCard.isVisible().catch(() => false);

  let consultationCreated = false;
  let consultationContext = null;
  let consultationId = null;
  if (enSoins) {
    await enSoinsCard.locator('[data-action="consultation"]').click();
    await page.waitForURL(/\/consultations\/create/);
    await wait();
    consultationContext = {
      patientValueForm: await page.locator('#patient_id').inputValue(),
      medValueForm: await page.locator('#medecin_id').inputValue(),
      rdvLinkValue: await page.locator('input[name="rendez_vous_id"]').inputValue(),
    };
    await page.fill('#symptomes', 'Test symptomes');
    await page.fill('#diagnostic', 'Test diagnostic');
    await page.locator('button[type="submit"]').filter({ hasText: /Enregistrer/i }).first().click();
    await wait();
    consultationCreated = !page.url().includes('/consultations/create');
    consultationId = page.url().match(/consultations\/(\d+)/)?.[1] || null;
  }

  await page.goto(base + `/ordonnances/create${consultationId ? `?consultation_id=${consultationId}` : ''}`);
  await wait();
  await page.locator('.js-medication-search').first().fill('Paracetamol test');
  await page.fill('input[name="medicaments[0][posologie]"]', '1 comprime matin et soir');
  await page.fill('input[name="medicaments[0][duree]"]', '5 jours');
  await page.fill('#diagnostic', 'Diagnostic test');
  const ordonnanceFreeLabel = await page.locator('.js-medication-label').first().inputValue().catch(() => null);
  await page.locator('button[type="submit"]').filter({ hasText: /Enregistrer|Creer/i }).first().click();
  await wait();
  const ordonnanceCreated = !page.url().includes('/ordonnances/create');

  console.log(JSON.stringify({
    rdvCreated,
    aVenir,
    enAttente,
    enSoins,
    consultationCreated,
    consultationContext,
    ordonnanceFreeLabel,
    ordonnanceCreated,
  }, null, 2));

  await browser.close();
})().catch(async (error) => {
  console.error(error);
  process.exit(1);
});
