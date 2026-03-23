const { chromium } = require('playwright');

async function login(page) {
  await page.goto('http://cabinet-medical-laravel.test/login', { waitUntil: 'networkidle' });
  const emailField = page.locator('input[name="email"]');
  if (await emailField.isVisible().catch(() => false)) {
    await emailField.fill('admin@medisys.test');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
  }
}

async function createSampleMedicament(page) {
  const suffix = Date.now().toString();
  const cip = `34009${suffix.slice(-8)}`;
  const code = `VAL-UI-${suffix.slice(-6)}`;
  const expirationDate = new Date();
  expirationDate.setMonth(expirationDate.getMonth() + 8);
  const fabricationDate = new Date();
  fabricationDate.setMonth(fabricationDate.getMonth() - 4);

  const formatDate = (value) => value.toISOString().slice(0, 10);

  await page.goto('http://cabinet-medical-laravel.test/medicaments/create', { waitUntil: 'networkidle' });
  await page.fill('input[name="nom_commercial"]', `Validation UI ${suffix.slice(-4)}`);
  await page.fill('input[name="dci"]', 'Paracetamol');
  await page.fill('input[name="code_cip"]', cip);
  await page.fill('input[name="code_medicament"]', code);
  await page.fill('input[name="categorie"]', 'Antalgique');
  await page.fill('input[name="laboratoire"]', 'Demo Pharma');
  await page.selectOption('select[name="type"]', 'otc');
  await page.fill('input[name="classe_therapeutique"]', 'Analgesiques');
  await page.fill('input[name="prix_achat"]', '12.50');
  await page.fill('input[name="prix_vente"]', '18.90');
  await page.fill('input[name="taux_remboursement"]', '70');
  await page.fill('input[name="prix_remboursement"]', '13.23');
  await page.selectOption('select[name="statut"]', 'actif');
  await page.check('input[name="generique"]');
  await page.check('input[name="remboursable"]');
  await page.fill('input[name="quantite_stock"]', '48');
  await page.fill('input[name="quantite_seuil"]', '10');
  await page.fill('input[name="quantite_ideale"]', '80');
  await page.fill('input[name="date_peremption"]', formatDate(expirationDate));
  await page.fill('input[name="date_fabrication"]', formatDate(fabricationDate));
  await page.fill('input[name="numero_lot"]', `LOT-${suffix.slice(-6)}`);
  await page.fill('input[name="fournisseur"]', 'Validation Supply');
  await page.fill('input[name="presentation"]', 'Boite de 16 comprimes');
  await page.selectOption('select[name="voie_administration"]', 'orale');
  await page.fill('textarea[name="posologie"]', '1 comprime matin et soir');
  await page.fill('textarea[name="precautions"]', 'Eviter en cas d allergie connue');
  await page.fill('textarea[name="conservation"]', 'Conserver a temperature ambiante');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle' }),
    page.click('button[type="submit"]'),
  ]);
}

async function getSampleRoutes(page) {
  await page.goto('http://cabinet-medical-laravel.test/medicaments', { waitUntil: 'networkidle' });

  let showHref = await page.locator('a[title="Voir"]').first().getAttribute('href').catch(() => null);
  let editHref = await page.locator('a[title="Modifier"]').first().getAttribute('href').catch(() => null);

  if (!showHref || !editHref) {
    await createSampleMedicament(page);
    await page.goto('http://cabinet-medical-laravel.test/medicaments', { waitUntil: 'networkidle' });
    showHref = await page.locator('a[title="Voir"]').first().getAttribute('href').catch(() => null);
    editHref = await page.locator('a[title="Modifier"]').first().getAttribute('href').catch(() => null);
  }

  return {
    create: 'http://cabinet-medical-laravel.test/medicaments/create',
    show: showHref ? new URL(showHref, page.url()).href : null,
    edit: editHref ? new URL(editHref, page.url()).href : null,
  };
}

async function inspectForm(page, url) {
  if (!url) {
    return { missing: true };
  }

  await page.goto(url, { waitUntil: 'networkidle' });
  return page.evaluate(() => ({
    title: document.querySelector('.med-form-title')?.textContent?.trim() || null,
    hero: !!document.querySelector('.med-form-hero'),
    side: !!document.querySelector('.med-form-side'),
    sections: document.querySelectorAll('.med-form-section').length,
    mobileBarVisible: (() => {
      const el = document.querySelector('.med-form-mobile-actions');
      return !!el && getComputedStyle(el).display !== 'none';
    })(),
    overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
  }));
}

async function inspectShow(page, url) {
  if (!url) {
    return { missing: true };
  }

  await page.goto(url, { waitUntil: 'networkidle' });
  return page.evaluate(() => ({
    title: document.querySelector('.med-show-title')?.textContent?.trim() || null,
    hero: !!document.querySelector('.med-show-hero'),
    side: !!document.querySelector('.med-show-side'),
    tabs: document.querySelectorAll('.med-show-tab').length,
    mobileBarVisible: (() => {
      const el = document.querySelector('.med-show-mobile-actions');
      return !!el && getComputedStyle(el).display !== 'none';
    })(),
    overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
  }));
}

async function inspectViewport(page) {
  await login(page);
  const routes = await getSampleRoutes(page);

  return {
    create: await inspectForm(page, routes.create),
    show: await inspectShow(page, routes.show),
    edit: await inspectForm(page, routes.edit),
  };
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const results = {};

  const desktop = await browser.newPage({ viewport: { width: 1440, height: 1400 } });
  results.desktop = await inspectViewport(desktop);

  const tablet = await browser.newPage({ viewport: { width: 1024, height: 1366 }, hasTouch: true });
  results.tablet = await inspectViewport(tablet);

  const mobile = await browser.newPage({ viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true });
  results.mobile = await inspectViewport(mobile);

  console.log(JSON.stringify(results, null, 2));
  await browser.close();
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});