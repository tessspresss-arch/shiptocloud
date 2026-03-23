import { expect, type Locator, type Page } from '@playwright/test';

export type RuntimeIssueCollector = {
  pageErrors: string[];
  consoleErrors: string[];
  networkErrors: string[];
};

export function createRuntimeIssueCollector(page: Page): RuntimeIssueCollector {
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
      networkErrors.push(`${response.status()} ${request.method()} ${request.url()}`);
    }
  });

  page.on('requestfailed', (request) => {
    networkErrors.push(`FAILED ${request.method()} ${request.url()} ${request.failure()?.errorText ?? 'unknown'}`);
  });

  return { pageErrors, consoleErrors, networkErrors };
}

export async function waitForStable(page: Page): Promise<void> {
  await page.waitForLoadState('networkidle', { timeout: 20_000 }).catch(() => undefined);
  await page.waitForTimeout(300);
}

export async function setInputValue(page: Page, selector: string, value: string) {
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

export async function fillIfVisible(locator: Locator, value: string) {
  const target = locator.first();
  if (await target.count()) {
    await target.fill(value);
  }
}

export async function selectOptionContainingText(select: Locator, text: string) {
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

export async function expectSelectedOptionToContain(select: Locator, text: string) {
  const selectedText = await select.locator('option:checked').textContent();
  expect(selectedText ?? '').toContain(text);
}

export async function assertNoHorizontalOverflow(page: Page, label: string) {
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth > 1);
  expect(overflow, `${label} presente un debordement horizontal.`).toBeFalsy();
}

export function toDate(date: Date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

export function toDateTimeLocal(date: Date) {
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${toDate(date)}T${hours}:${minutes}`;
}
