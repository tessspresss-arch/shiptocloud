import { expect, test } from '@playwright/test';

import { ensureLoggedIn, login } from '../helpers/auth';

const credentials = {
  email: 'admin@medisys.test',
  password: 'password',
};

const criticalPages = [
  {
    path: '/utilisateurs',
    expected: ['Gestion des utilisateurs', 'Comptes utilisateurs', 'Réinitialiser'],
    forbidden: [/\bReinitialiser\b/, /\bDerniere connexion\b/, /Voir activite utilisateur/],
  },
  {
    path: '/documents',
    expected: ['Gestion des documents', 'Téléverser un document', 'Catégories actives'],
    forbidden: [/Gestion des Documents/, /Telecharger un document/, /Categories actives/, /utilisee\(s\)/],
  },
  {
    path: '/documents/categories',
    expected: ['Catégories de documents', 'Ajouter une catégorie'],
    forbidden: [/Categories de documents/, /Gestion centralisee/, /Telecharger un document/, /Liste des categories/],
  },
  {
    path: '/documents/upload',
    expected: ['Téléverser un document', 'Informations complémentaires', 'Retour à la liste'],
    forbidden: [/Telecharger un document/, /Selectionner un patient/, /Glissez-deposez/, /Retour a la liste/],
  },
  {
    path: '/examens',
    expected: ['Gestion des examens', 'Liste des examens', 'Total affiché'],
    forbidden: [/Gestion des Examens/, /Gerez les demandes, suivis et resultats d'examens/, /\bPayee\b/, /\bNon payee\b/],
  },
  {
    path: '/sms',
    expected: ['Rappels SMS', 'Planifiés', 'Envoyés'],
    forbidden: [/Suivi des rappels SMS planifies, envoyes et a renvoyer\./, /\bPlanifies\b/, /\bEnvoyes\b/, /\bPlanifie\b/],
  },
];

test.describe('French copy QA', () => {
  test.beforeEach(async ({ page }) => {
    await login(page, credentials.email, credentials.password);
    await ensureLoggedIn(page);
  });

  for (const pageSpec of criticalPages) {
    test(`checks ${pageSpec.path}`, async ({ page }) => {
      await page.goto(pageSpec.path);
      await page.waitForLoadState('domcontentloaded');

      const body = page.locator('body');
      const bodyText = await body.innerText();

      for (const expectedText of pageSpec.expected) {
        await expect(body).toContainText(expectedText);
      }

      for (const forbiddenPattern of pageSpec.forbidden) {
        expect(bodyText).not.toMatch(forbiddenPattern);
      }
    });
  }
});