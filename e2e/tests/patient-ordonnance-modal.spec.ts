import { expect, test, type Page } from '@playwright/test';
import { ensureLoggedIn, login } from '../helpers/auth';

const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@medisys.test';
const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password';

test.describe('Patient ordonnance modal', () => {
  test('opens quick ordonnance modal from patient show page', async ({ page }) => {
    const runtimeIssues = createRuntimeIssueCollector(page);

    await login(page, adminEmail, adminPassword);
    await ensureLoggedIn(page);

    await goToAnyPatientShow(page);
    runtimeIssues.pageErrors.length = 0;
    runtimeIssues.consoleErrors.length = 0;
    runtimeIssues.networkErrors.length = 0;
    await expect(page.locator('[data-open-ordonnance-modal]')).toBeVisible();

    console.log('modal-debug-before', await page.evaluate(() => {
      const modal = document.querySelector('#modal-ordonnance') as HTMLElement | null;
      const form = document.querySelector('#patientOrdonnanceModalForm') as HTMLFormElement | null;
      const button = document.querySelector('[data-open-ordonnance-modal]') as HTMLElement | null;

      return {
        buttonTag: button?.tagName ?? null,
        buttonCount: document.querySelectorAll('[data-open-ordonnance-modal]').length,
        formBound: form?.dataset.medisysBound ?? null,
        modalDisplay: modal?.style.display ?? null,
        modalHidden: modal?.getAttribute('aria-hidden') ?? null,
      };
    }));

    await page.locator('[data-open-ordonnance-modal]').click();

    console.log('modal-debug-after', await page.evaluate(() => {
      const modal = document.querySelector('#modal-ordonnance') as HTMLElement | null;
      const form = document.querySelector('#patientOrdonnanceModalForm') as HTMLFormElement | null;

      return {
        formBound: form?.dataset.medisysBound ?? null,
        modalDisplay: modal?.style.display ?? null,
        modalHidden: modal?.getAttribute('aria-hidden') ?? null,
        bodyClasses: document.body.className,
      };
    }));

    await expect(page.locator('#modal-ordonnance')).toBeVisible();
    await expect(page.locator('#patientOrdonnanceModalForm')).toBeVisible();
    await expect(page.locator('#ordonnanceQuickMedecin')).toBeVisible();

    expect(runtimeIssues.pageErrors, `Erreurs JS detectees: ${runtimeIssues.pageErrors.join(' | ')}`).toEqual([]);
    expect(runtimeIssues.consoleErrors, `Console errors detectees: ${runtimeIssues.consoleErrors.join(' | ')}`).toEqual([]);
    expect(runtimeIssues.networkErrors, `Erreurs reseau detectees: ${runtimeIssues.networkErrors.join(' | ')}`).toEqual([]);
  });
});

async function goToAnyPatientShow(page: Page) {
  await page.goto('/patients', { waitUntil: 'domcontentloaded' });
  await page.waitForLoadState('networkidle').catch(() => {});

  const firstViewAction = page.locator('a[title="Voir dossier"]').first();
  if (await firstViewAction.count()) {
    await firstViewAction.click();
    await page.waitForLoadState('networkidle').catch(() => {});
    return;
  }

  const token = Date.now().toString().slice(-8);

  await page.goto('/patients/create', { waitUntil: 'domcontentloaded' });
  await page.waitForLoadState('networkidle').catch(() => {});

  await page.fill('input[name="nom"]', `Modal${token}`);
  await page.fill('input[name="prenom"]', 'Ordonnance');
  await page.fill('input[name="date_naissance"]', '1993-09-11');
  await page.locator('input[name="genre"][value="F"]').check();
  await page.fill('input[name="telephone"]', `6${token.slice(-7)}`);
  await page.fill('input[name="cin"]', `MOD-${token}`);

  await page.locator('button[type="submit"]').first().click();
  await page.waitForLoadState('networkidle').catch(() => {});

  await page.goto(`/patients?search=Modal${token}`, { waitUntil: 'domcontentloaded' });
  await page.waitForLoadState('networkidle').catch(() => {});
  await page.locator('a[title="Voir dossier"]').first().click();
  await page.waitForLoadState('networkidle').catch(() => {});
}

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
