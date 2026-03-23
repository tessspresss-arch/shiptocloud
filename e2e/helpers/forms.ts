import { Locator } from '@playwright/test';

export async function selectFirstNonEmptyOption(select: Locator) {
  const options = select.locator('option');
  const count = await options.count();

  for (let i = 0; i < count; i += 1) {
    const option = options.nth(i);
    const value = (await option.getAttribute('value')) ?? '';
    if (value.trim() !== '') {
      await select.selectOption(value);
      return value;
    }
  }

  return null;
}
