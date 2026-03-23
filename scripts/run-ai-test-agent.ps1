param(
    [string]$BaseUrl = "http://cabinet-medical-laravel.test"
)

$ErrorActionPreference = "Continue"
$reportDir = "storage/test-reports"

New-Item -ItemType Directory -Force -Path $reportDir | Out-Null

Write-Host "== Medisys Pro AI Test Agent =="
Write-Host "Base URL E2E: $BaseUrl"

$env:E2E_BASE_URL = $BaseUrl
if (-not $env:E2E_ADMIN_EMAIL) { $env:E2E_ADMIN_EMAIL = "admin@medisys.test" }
if (-not $env:E2E_ADMIN_PASSWORD) { $env:E2E_ADMIN_PASSWORD = "password" }
if (-not $env:E2E_MEDECIN_EMAIL) { $env:E2E_MEDECIN_EMAIL = "medecin@medisys.test" }
if (-not $env:E2E_MEDECIN_PASSWORD) { $env:E2E_MEDECIN_PASSWORD = "password" }
if (-not $env:E2E_RECEPTION_EMAIL) { $env:E2E_RECEPTION_EMAIL = "reception@medisys.test" }
if (-not $env:E2E_RECEPTION_PASSWORD) { $env:E2E_RECEPTION_PASSWORD = "password" }

$backendExit = 0
$e2eExit = 0
$analysisExit = 0
$seedExit = 0
$migrateExit = 0

Write-Host "`n[1/5] Run migrations (non destructive)"
php artisan migrate --force
if ($LASTEXITCODE -ne 0) { $migrateExit = $LASTEXITCODE }

Write-Host "`n[2/5] Seed test users/data (non destructive)"
php artisan db:seed --class="Database\Seeders\Testing\MedisysTestingSeeder"
if ($LASTEXITCODE -ne 0) { $seedExit = $LASTEXITCODE }

Write-Host "`n[3/5] Run backend tests (PHPUnit/Laravel)"
php artisan test --env=testing --testsuite=Unit,Feature --log-junit="$reportDir/phpunit.junit.xml"
if ($LASTEXITCODE -ne 0) { $backendExit = $LASTEXITCODE }

Write-Host "`n[4/5] Run browser tests (Playwright)"
npx playwright test
if ($LASTEXITCODE -ne 0) { $e2eExit = $LASTEXITCODE }

Write-Host "`n[5/5] Run AI analysis"
node tools/test-agent/analyze-results.mjs
if ($LASTEXITCODE -ne 0) { $analysisExit = $LASTEXITCODE }

Write-Host "`n==== SUMMARY ===="
Write-Host "Backend exit:  $backendExit"
Write-Host "E2E exit:      $e2eExit"
Write-Host "Analysis exit: $analysisExit"
Write-Host "Migrate exit:  $migrateExit"
Write-Host "Seed exit:     $seedExit"
Write-Host "AI report:     $reportDir/agent-report.html"

if ($migrateExit -ne 0 -or $seedExit -ne 0 -or $backendExit -ne 0 -or $e2eExit -ne 0 -or $analysisExit -ne 0) {
    exit 1
}

exit 0
