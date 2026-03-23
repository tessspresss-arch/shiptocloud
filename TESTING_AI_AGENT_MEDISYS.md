# Medisys Pro - Architecture de tests automatisee (Backend + E2E + Agent IA)

## 1) Objectif
Mettre en place un pipeline de test professionnel en 3 couches:

1. Backend Laravel (Unit + Feature).
2. E2E navigateur avec Playwright.
3. Agent IA local d'analyse des resultats et classification des bugs.

## 2) Couches techniques

### A. Backend Laravel
- Outil: `php artisan test` (PHPUnit).
- Suites: `tests/Unit`, `tests/Feature`.
- Focus prioritaire:
  - authentification et redirection par role
  - patients
  - rendez-vous/planning
  - salle d'attente
  - facturation
  - permissions

### B. E2E navigateur
- Outil: `@playwright/test`.
- Dossier: `e2e/tests`.
- Rapports:
  - HTML: `storage/test-reports/playwright-html`
  - JSON: `storage/test-reports/playwright-results.json`
  - JUnit XML: `storage/test-reports/playwright.junit.xml`
- Captures/videos/traces sur erreur actives dans `playwright.config.ts`.

### C. Agent IA d'analyse
- Script: `tools/test-agent/analyze-results.mjs`
- Sources:
  - `storage/test-reports/phpunit.junit.xml`
  - `storage/test-reports/playwright-results.json`
- Sorties:
  - `storage/test-reports/agent-summary.json`
  - `storage/test-reports/agent-report.md`
  - `storage/test-reports/agent-report.html`
- Classement des findings: `critical`, `high`, `medium`.

## 3) Jeu de donnees de test

Seeders ajoutés:
- `Database\\Seeders\\Testing\\MedisysTestUsersSeeder`
- `Database\\Seeders\\Testing\\MedisysTestDataSeeder`
- `Database\\Seeders\\Testing\\MedisysTestingSeeder`

Comptes de test:
- admin: `admin@medisys.test / password`
- medecin: `medecin@medisys.test / password`
- reception: `reception@medisys.test / password`

## 4) Commandes d'execution

### Installation (si necessaire)
```bash
npm install
npx playwright install
```

### Preparation environnement de test
```bash
cp .env.testing.example .env.testing
php artisan key:generate --env=testing
```

Important: la base `.env.testing` doit etre dediee aux tests (ex: `medisys_test`).
Un garde-fou est ajoute dans `tests/TestCase.php` pour bloquer l'execution sur une base non-test.

### Lancer tout le pipeline (PowerShell)
```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\run-ai-test-agent.ps1
```

### Lancer couche par couche
```bash
php artisan test --testsuite=Unit,Feature --log-junit=storage/test-reports/phpunit.junit.xml
npx playwright test
node tools/test-agent/analyze-results.mjs
```

## 5) Notes de stabilite

- `phpunit.xml` est configure en `APP_ENV=testing`.
- Les parametres DB viennent de `.env.testing`.
- Les migrations SQL specifiques MySQL (ALTER ENUM) ont ete rendues compatibles test/CI.
- Le seeding E2E utilise `updateOrCreate` ou donnees predictibles pour eviter les erreurs.

## 6) Evolution recommandee (phase 2)

- Ajouter des `data-testid` sur les elements critiques UI pour fiabiliser Playwright.
- Ajouter pipeline CI (GitHub Actions/GitLab CI) avec execution journaliere.
- Integrer alerting (mail/Slack) en cas d'echec `critical`.
- Ajouter tests de non-regression sur encodage FR/AR.
