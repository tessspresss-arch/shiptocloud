import fs from 'node:fs';
import path from 'node:path';
import { expect, test, type Page } from '@playwright/test';
import { ensureLoggedIn, login } from '../helpers/auth';

type AuditViewport = {
  name: string;
  width: number;
  height: number;
};

type AuditModule = {
  key: string;
  label: string;
  route?: string;
  resolveRoute?: (page: Page) => Promise<string | null>;
};

type AuditEntry = {
  module: string;
  route: string;
  viewport: string;
  title: string;
  heading: string;
  overflow: boolean;
  overflowNodes: string[];
  screenshot: string;
};

const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@medisys.test';
const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password';

const viewports: AuditViewport[] = [
  { name: 'desktop-1440', width: 1440, height: 900 },
  { name: 'mobile-390', width: 390, height: 844 },
];

const modules: AuditModule[] = [
  { key: 'patients', label: 'Patients', route: '/patients' },
  { key: 'consultations', label: 'Consultations', route: '/consultations' },
  { key: 'agenda', label: 'Agenda', route: '/agenda' },
  { key: 'rendezvous', label: 'Rendez-vous', route: '/rendezvous' },
  { key: 'documents', label: 'Documents', route: '/documents' },
  { key: 'depenses-stats', label: 'Depenses statistiques', route: '/depenses/statistiques' },
  { key: 'factures', label: 'Factures', route: '/factures' },
  { key: 'factures-create', label: 'Factures create', route: '/factures/create' },
  { key: 'factures-show', label: 'Factures show', resolveRoute: resolveFirstFactureShowRoute },
  { key: 'factures-edit', label: 'Factures edit', resolveRoute: resolveFirstFactureEditRoute },
];

test.describe('Premium UI coherence audit', () => {
  test('capture desktop and mobile references', async ({ page }) => {
    test.setTimeout(8 * 60_000);

    const screenshotRoot = path.resolve('storage/test-reports/premium-ui');
    const reportPath = path.resolve(screenshotRoot, 'premium-ui-coherence.json');
    const entries: AuditEntry[] = [];

    fs.mkdirSync(screenshotRoot, { recursive: true });

    await login(page, adminEmail, adminPassword);
    await ensureLoggedIn(page);
    await waitForStable(page);

    for (const viewport of viewports) {
      await page.setViewportSize({ width: viewport.width, height: viewport.height });

      for (const module of modules) {
        const route = module.resolveRoute
          ? await module.resolveRoute(page)
          : module.route ?? null;

        await expect.soft(route, `${module.label} n'a pas de route exploitable pour l'audit`).toBeTruthy();

        if (!route) {
          continue;
        }

        await page.goto(route, { waitUntil: 'domcontentloaded', timeout: 45_000 });
        await waitForStable(page);

        await expect.soft(page, `${module.label} redirige vers le login`).not.toHaveURL(/\/login/);

        const headingLocator = page.locator('h1').first();
        await expect.soft(headingLocator, `${module.label} doit afficher un titre principal`).toBeVisible();

        const heading = (await headingLocator.textContent().catch(() => ''))?.trim() ?? '';
        const title = await page.title();
        const overflow = await collectOverflow(page);
        const screenshotName = `${module.key}-${viewport.name}.png`;
        const screenshotPath = path.resolve(screenshotRoot, screenshotName);

        await page.screenshot({
          path: screenshotPath,
          fullPage: true,
          animations: 'disabled',
        });

        await expect.soft(
          overflow.hasOverflow,
          `${module.label} présente un débordement horizontal en ${viewport.name}: ${overflow.nodes.join(' | ')}`,
        ).toBeFalsy();

        entries.push({
          module: module.label,
          route,
          viewport: viewport.name,
          title,
          heading,
          overflow: overflow.hasOverflow,
          overflowNodes: overflow.nodes,
          screenshot: `storage/test-reports/premium-ui/${screenshotName}`,
        });
      }
    }

    fs.writeFileSync(
      reportPath,
      JSON.stringify(
        {
          generatedAt: new Date().toISOString(),
          baseUrl: process.env.E2E_BASE_URL ?? 'http://cabinet-medical-laravel.test',
          viewports,
          modules,
          entries,
        },
        null,
        2,
      ),
      'utf8',
    );
  });

  test('depenses statistiques conserve ses sections premium', async ({ page }) => {
    await login(page, adminEmail, adminPassword);
    await ensureLoggedIn(page);

    const viewports = [
      { label: 'desktop', width: 1440, height: 900 },
      { label: 'mobile', width: 390, height: 844 },
    ];

    for (const viewport of viewports) {
      await test.step(`Validation depenses statistiques ${viewport.label}`, async () => {
        await page.setViewportSize({ width: viewport.width, height: viewport.height });
        await page.goto('/depenses/statistiques', { waitUntil: 'domcontentloaded', timeout: 45_000 });
        await waitForStable(page);

        await expect(page).not.toHaveURL(/\/login/);
        await expect(page.locator('.dep-stat-title')).toContainText(/Statistiques des depenses/i);
        await expect(page.locator('.dep-stat-eyebrow')).toContainText(/Pilotage financier/i);
        await expect(page.locator('.dep-stat-filters')).toBeVisible();
        await expect(page.locator('select[name="period"]')).toBeVisible();
        await expect(page.locator('select[name="categorie"]')).toBeVisible();
        await expect(page.locator('select[name="statut"]')).toBeVisible();
        await expect(page.locator('input[name="search"]')).toBeVisible();
        await expect(page.getByRole('link', { name: /Nouvelle depense/i })).toBeVisible();
        await expect(page.getByRole('link', { name: /Exporter CSV/i })).toBeVisible();

        const emptyState = page.locator('.dep-stat-empty');
        if (await emptyState.isVisible().catch(() => false)) {
          await expect(emptyState).toContainText(/Aucune donnee exploitable/i);
          await expect(emptyState.getByRole('link', { name: /Ajouter une depense/i })).toBeVisible();
        } else {
          await expect(page.locator('.dep-stat-kpis')).toBeVisible();
          await expect(page.locator('.dep-stat-panel').filter({ hasText: /Tendance mensuelle/i })).toBeVisible();
          await expect(page.locator('.dep-stat-panel').filter({ hasText: /Repartition par statut/i })).toBeVisible();
          await expect(page.locator('.dep-stat-panel').filter({ hasText: /Repartition par categorie/i })).toBeVisible();
          await expect(page.locator('.dep-stat-panel').filter({ hasText: /Top depenses de la selection/i })).toBeVisible();
        }

        const overflow = await collectOverflow(page);
        await expect.soft(
          overflow.hasOverflow,
          `Depenses statistiques presente un debordement horizontal en ${viewport.label}: ${overflow.nodes.join(' | ')}`,
        ).toBeFalsy();
      });
    }
  });
});

async function waitForStable(page: Page): Promise<void> {
  await page.waitForLoadState('networkidle', { timeout: 20_000 }).catch(() => undefined);
  await page.waitForTimeout(350);
}

async function collectOverflow(page: Page): Promise<{ hasOverflow: boolean; nodes: string[] }> {
  return page.evaluate(() => {
    const doc = document.documentElement;
    const limit = doc.clientWidth + 1;
    const candidates = Array.from(document.querySelectorAll<HTMLElement>('body *'))
      .filter((node) => {
        const rect = node.getBoundingClientRect();
        return rect.width > 0 && rect.right > limit;
      })
      .slice(0, 8)
      .map((node) => {
        const tag = node.tagName.toLowerCase();
        const id = node.id ? `#${node.id}` : '';
        const classes = node.className && typeof node.className === 'string'
          ? `.${node.className.trim().split(/\s+/).slice(0, 2).join('.')}`
          : '';
        return `${tag}${id}${classes}`;
      });

    return {
      hasOverflow: document.body.scrollWidth > doc.clientWidth + 1,
      nodes: candidates,
    };
  });
}

async function resolveFirstFactureShowRoute(page: Page): Promise<string | null> {
  return resolveFactureActionRoute(page, 'a.billing-icon-btn.view');
}

async function resolveFirstFactureEditRoute(page: Page): Promise<string | null> {
  return resolveFactureActionRoute(page, 'a.billing-icon-btn.edit');
}

async function resolveFactureActionRoute(page: Page, selector: string): Promise<string | null> {
  await page.goto('/factures', { waitUntil: 'domcontentloaded', timeout: 45_000 });
  await waitForStable(page);

  const actionLink = page.locator(selector).first();
  const linkCount = await actionLink.count();

  if (linkCount === 0) {
    return null;
  }

  return actionLink.getAttribute('href');
}