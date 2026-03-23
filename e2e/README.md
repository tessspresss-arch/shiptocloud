# E2E Playwright

Cette application dispose d'une couche E2E navigateur basee sur Playwright dans `e2e/tests`.

## Prerequis

- application Laravel accessible via `E2E_BASE_URL`
- comptes de test valides
- base de donnees seedee avec les jeux de donnees de test
- navigateurs Playwright installes

## Variables utiles

- `E2E_BASE_URL` : URL cible, par defaut `http://cabinet-medical-laravel.test`
- `E2E_ADMIN_EMAIL`
- `E2E_ADMIN_PASSWORD`
- `E2E_RECEPTION_EMAIL`
- `E2E_RECEPTION_PASSWORD`
- `PHP_BINARY` si le binaire PHP n'est pas `php`

## Commandes

- suite complete : `npm run test:e2e`
- lot critique : `npm run test:e2e:critical`
- audit preproduction : `npm run test:e2e:audit`
- mode headed : `npm run test:e2e:headed`
- rapport HTML : `npm run test:e2e:report`

## Specs critiques

- `auth.spec.ts` : smoke login
- `core-flow.spec.ts` : flux principal rapide
- `critical-patient-consultation-billing-rdv.spec.ts` : parcours patient -> consultation -> facture -> rendez-vous
- `business-critical-regression.spec.ts` : parcours etendu patient -> consultation -> ordonnance -> facture -> paiement -> rendez-vous
- `preproduction-audit.spec.ts` : audit de navigation, responsive, erreurs JS/reseau et workflows

## Sorties

- HTML : `storage/test-reports/playwright-html`
- JSON : `storage/test-reports/playwright-results.json`
- JUnit : `storage/test-reports/playwright.junit.xml`
- artefacts : `storage/test-reports/playwright-artifacts`

## Recommandation d'usage

En preproduction :

1. `composer test:seed-e2e`
2. `npm run test:e2e:critical`
3. `npm run test:e2e:audit`

En CI :

1. lancer `php artisan test`
2. lancer `npm run test:e2e:critical`
3. publier les rapports Playwright
