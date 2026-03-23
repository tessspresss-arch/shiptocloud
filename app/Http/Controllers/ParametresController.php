<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Models\Medicament;
use App\Models\ModeleOrdonnance;
use App\Models\Setting;
use App\Models\User;
use App\Services\Settings\SettingsDashboardService;
use App\Services\Settings\SettingsMaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ParametresController extends Controller
{
    public function __construct(
        private readonly SettingsDashboardService $settingsDashboardService,
        private readonly SettingsMaintenanceService $settingsMaintenanceService
    ) {
    }
    /**
     * Afficher la page des parametres.
     */
    public function index()
    {
        return view('parametres.index', $this->settingsDashboardService->buildIndexData(
            (bool) (auth()->user()?->isAdmin())
        ));
    }

    /**
     * Mettre a jour les parametres.
     */
    public function update(Request $request)
    {
        try {
            $section = $request->input('section', 'general');

            $request->validate([
                'cabinet_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ], [], [
                'cabinet_logo' => 'logo du cabinet',
            ]);

            if ($section === 'permissions') {
                return $this->updateModulePermissions($request);
            }

            if ($request->boolean('remove_cabinet_logo')) {
                $this->deleteStoredLogo((string) ($this->getSettingValue('cabinet_logo') ?? ''));
                Setting::updateOrCreate(['key' => 'cabinet_logo'], ['value' => '']);
            }

            if ($request->hasFile('cabinet_logo')) {
                $path = $request->file('cabinet_logo')->store('settings/cabinet', 'public');
                $this->deleteStoredLogo((string) ($this->getSettingValue('cabinet_logo') ?? ''));
                Setting::updateOrCreate(['key' => 'cabinet_logo'], ['value' => $path]);
            }

            foreach ($request->all() as $key => $value) {
                if (in_array($key, ['_token', '_method', 'section'], true)) {
                    continue;
                }

                if (in_array($key, ['cabinet_logo', 'restore_backup_file', 'remove_cabinet_logo'], true)) {
                    continue;
                }

                if ($value === '0' || $value === null) {
                    $value = false;
                }

                if ($value === '1') {
                    $value = true;
                }

                $attributes = [
                    'value' => is_array($value) ? json_encode($value) : $value,
                ];

                if ($key === 'session_timeout') {
                    $attributes['value'] = max(1, (int) $value);
                    $attributes['type'] = 'integer';
                }

                Setting::updateOrCreate(['key' => $key], $attributes);
            }

            Cache::forget('all_settings');

            Log::channel('security_stack')->info('security.settings.updated', [
                'actor_user_id' => auth()->id(),
                'section' => $section,
                'keys_changed' => array_values(array_filter(array_keys($request->except(['_token', '_method', 'section'])))),
                'ip' => $request->ip(),
            ]);

            return redirect()->back()->with('success', 'Parametres sauvegardes avec succes.');
        } catch (\Exception $e) {
            Log::error('Settings update failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise a jour des parametres.']);
        }
    }

    public function storeOrdonnanceTemplate(Request $request)
    {
        $validated = $this->validateOrdonnanceTemplate($request);
        $payload = $this->buildOrdonnanceTemplatePayload($validated);

        ModeleOrdonnance::query()->create($this->filterExistingOrdonnanceTemplateColumns($payload));

        return redirect()
            ->to(route('parametres.index') . '#ordonnances')
            ->with('success', "Modele d'ordonnance ajoute avec succes.");
    }

    public function updateOrdonnanceTemplate(Request $request, ModeleOrdonnance $template)
    {
        $validated = $this->validateOrdonnanceTemplate($request);
        $payload = $this->buildOrdonnanceTemplatePayload($validated, $template);

        $template->update($this->filterExistingOrdonnanceTemplateColumns($payload));

        return redirect()
            ->to(route('parametres.index') . '#ordonnances')
            ->with('success', "Modele d'ordonnance mis a jour avec succes.");
    }

    public function destroyOrdonnanceTemplate(ModeleOrdonnance $template)
    {
        $template->delete();

        return redirect()
            ->to(route('parametres.index') . '#ordonnances')
            ->with('success', "Modele d'ordonnance supprime avec succes.");
    }

    public function toggleOrdonnanceTemplate(ModeleOrdonnance $template)
    {
        $template->update(['is_actif' => !$template->is_actif]);

        return redirect()
            ->to(route('parametres.index') . '#ordonnances')
            ->with('success', $template->is_actif ? "Modele d'ordonnance active." : "Modele d'ordonnance desactive.");
    }

    /**
     * Exporter les parametres.
     */
    public function export()
    {
        $settings = Setting::all()->toArray();
        $filename = 'parametres_' . date('Y-m-d_H-i-s') . '.json';

        return response()->json($settings)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Reinitialiser les parametres.
     */
    public function reset()
    {
        try {
            Setting::where('key', 'not like', 'system_%')->delete();
            Cache::forget('all_settings');

            Log::channel('security_stack')->warning('security.settings.reset', [
                'actor_user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return redirect()->back()->with('success', 'Parametres reinitialises aux valeurs par defaut.');
        } catch (\Exception $e) {
            Log::error('Settings reset failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erreur lors de la reinitialisation des parametres.']);
        }
    }

    /**
     * Tester la configuration SMTP.
     */
    public function testSmtp()
    {
        try {
            $settings = Setting::pluck('value', 'key')->toArray();

            config([
                'mail.host' => $settings['smtp_host'] ?? 'smtp.gmail.com',
                'mail.port' => $settings['smtp_port'] ?? 587,
                'mail.username' => $settings['smtp_username'] ?? '',
                'mail.password' => $settings['smtp_password'] ?? '',
            ]);

            \Mail::raw('Test email from SCABINET', function ($message) use ($settings) {
                $message->to($settings['email_principal'] ?? auth()->user()->email)
                    ->subject('Test SMTP - SCABINET');
            });

            return response()->json([
                'success' => true,
                'message' => 'Email de test envoye avec succes.',
            ]);
        } catch (\Exception $e) {
            Log::error('SMTP test failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test SMTP.',
            ], 400);
        }
    }

    /**
     * Generer une sauvegarde manuelle.
     */
    public function backup()
    {
        try {
            $this->settingsMaintenanceService->runDatabaseBackup();

            return response()->json([
                'success' => true,
                'message' => 'Sauvegarde generee avec succes.',
            ]);
        } catch (\Exception $e) {
            Log::error('Manual backup failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la generation de la sauvegarde.',
            ], 400);
        }
    }

    public function downloadBackup(Request $request)
    {
        $fullPath = $this->settingsMaintenanceService->resolveDownloadPath((string) $request->query('file', ''));
        if ($fullPath === null) {
            return redirect()->back()->withErrors(['error' => 'Sauvegarde introuvable.']);
        }

        return response()->download($fullPath);
    }

    public function restoreBackup(Request $request)
    {
        $request->validate([
            'restore_backup_file' => ['required', 'file', 'mimes:sql,sqlite,db'],
        ], [], [
            'restore_backup_file' => 'fichier de restauration',
        ]);

        $uploaded = $request->file('restore_backup_file');
        $tempPath = $uploaded->store('backups/imports', 'local');
        $absolutePath = storage_path('app/' . $tempPath);

        try {
            $this->settingsMaintenanceService->runDatabaseBackup();
            $this->settingsMaintenanceService->restoreDatabaseFromFile($absolutePath);

            Log::channel('security_stack')->warning('security.settings.backup_restored', [
                'actor_user_id' => auth()->id(),
                'restored_file' => $uploaded->getClientOriginalName(),
                'ip' => $request->ip(),
            ]);

            return redirect()->to(route('parametres.index') . '#backup')->with('success', 'Sauvegarde restauree avec succes.');
        } catch (\Throwable $e) {
            Log::error('Backup restore failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Erreur lors de la restauration de la sauvegarde. Verifiez le format du fichier et la configuration du client de base de donnees.',
            ]);
        } finally {
            Storage::disk('local')->delete($tempPath);
        }
    }

    /**
     * Vider les caches systeme Laravel.
     */
    public function clearSystemCaches()
    {
        try {
            $this->settingsMaintenanceService->clearCaches();

            return redirect()->back()->with('success', 'Caches systeme vides avec succes.');
        } catch (\Exception $e) {
            Log::error('System cache clear failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erreur lors du vidage des caches systeme.']);
        }
    }

    /**
     * Obtenir les statistiques systeme.
     */
    public function systemStats()
    {
        return response()->json($this->settingsMaintenanceService->systemStats());
    }

    private function validateOrdonnanceTemplate(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:150'],
            'categorie' => ['nullable', 'string', 'max:120'],
            'diagnostic_contexte' => ['nullable', 'string'],
            'instructions_generales' => ['nullable', 'string'],
            'est_template_general' => ['nullable', 'boolean'],
            'is_actif' => ['nullable', 'boolean'],
            'medecin_id' => ['nullable', 'exists:medecins,id'],
            'medicaments_template' => ['nullable', 'array'],
            'medicaments_template.*.medicament_id' => ['nullable', 'exists:medicaments,id'],
            'medicaments_template.*.medicament_label' => ['nullable', 'string', 'max:190'],
            'medicaments_template.*.posologie' => ['nullable', 'string', 'max:255'],
            'medicaments_template.*.duree' => ['nullable', 'string', 'max:255'],
            'medicaments_template.*.quantite' => ['nullable', 'string', 'max:255'],
            'medicaments_template.*.instructions' => ['nullable', 'string', 'max:500'],
        ], [], [
            'nom' => 'nom du modele',
            'categorie' => 'categorie',
            'diagnostic_contexte' => 'diagnostic ou contexte',
            'instructions_generales' => 'instructions generales',
            'medecin_id' => 'medecin rattache',
            'medicaments_template' => 'lignes medicaments du modele',
        ]);
    }

    private function buildOrdonnanceTemplatePayload(array $validated, ?ModeleOrdonnance $template = null): array
    {
        $medicaments = $this->normalizeTemplateMedications($validated['medicaments_template'] ?? []);
        $estTemplateGeneral = (bool) ($validated['est_template_general'] ?? false);
        $medecinId = $estTemplateGeneral ? null : ($validated['medecin_id'] ?? null);

        if (!$estTemplateGeneral && $medecinId === null) {
            $estTemplateGeneral = true;
        }

        $diagnostic = trim((string) ($validated['diagnostic_contexte'] ?? ''));
        $instructions = trim((string) ($validated['instructions_generales'] ?? ''));

        return [
            'nom' => trim((string) $validated['nom']),
            'categorie' => $validated['categorie'] !== null && $validated['categorie'] !== ''
                ? trim((string) $validated['categorie'])
                : null,
            'diagnostic_contexte' => $diagnostic !== '' ? $diagnostic : null,
            'instructions_generales' => $instructions !== '' ? $instructions : null,
            'medicaments_template' => $medicaments,
            'contenu_html' => $this->renderOrdonnanceTemplateContent(
                trim((string) $validated['nom']),
                $diagnostic,
                $instructions,
                $medicaments,
                $validated['categorie'] ?? null
            ),
            'medecin_id' => $medecinId,
            'est_template_general' => $estTemplateGeneral,
            'is_actif' => array_key_exists('is_actif', $validated)
                ? (bool) $validated['is_actif']
                : ($template?->is_actif ?? true),
        ];
    }

    private function normalizeTemplateMedications(array $rows): array
    {
        return collect($rows)
            ->map(function ($row): array {
                $medicamentId = isset($row['medicament_id']) && $row['medicament_id'] !== ''
                    ? (int) $row['medicament_id']
                    : null;
                $medicament = $medicamentId ? Medicament::query()->find($medicamentId) : null;

                return [
                    'medicament_id' => $medicamentId,
                    'medicament_label' => $medicament
                        ? trim($medicament->nom_commercial . ($medicament->presentation ? ' (' . $medicament->presentation . ')' : ''))
                        : trim((string) ($row['medicament_label'] ?? '')),
                    'posologie' => trim((string) ($row['posologie'] ?? '')),
                    'duree' => trim((string) ($row['duree'] ?? '')),
                    'quantite' => trim((string) ($row['quantite'] ?? '')),
                    'instructions' => trim((string) ($row['instructions'] ?? '')),
                ];
            })
            ->filter(function (array $row): bool {
                return collect($row)
                    ->except(['medicament_id'])
                    ->filter(fn ($value) => $value !== null && $value !== '')
                    ->isNotEmpty() || $row['medicament_id'] !== null;
            })
            ->values()
            ->all();
    }

    private function renderOrdonnanceTemplateContent(
        string $name,
        string $diagnostic,
        string $instructions,
        array $medicaments,
        ?string $categorie
    ): string {
        $sections = array_filter([
            $categorie ? 'Categorie : ' . trim($categorie) : null,
            $diagnostic !== '' ? 'Diagnostic / contexte : ' . $diagnostic : null,
            $instructions !== '' ? 'Instructions generales : ' . $instructions : null,
        ]);

        $lines = [];
        foreach ($medicaments as $row) {
            $parts = array_filter([
                $row['medicament_label'] ?? null,
                $row['posologie'] ? 'Posologie : ' . $row['posologie'] : null,
                $row['duree'] ? 'Duree : ' . $row['duree'] : null,
                $row['quantite'] ? 'Quantite : ' . $row['quantite'] : null,
                $row['instructions'] ? 'Instructions : ' . $row['instructions'] : null,
            ]);

            if ($parts !== []) {
                $lines[] = '- ' . implode(' | ', $parts);
            }
        }

        if ($lines !== []) {
            $sections[] = "Traitement type :\n" . implode("\n", $lines);
        }

        if ($sections === []) {
            return $name;
        }

        return implode("\n\n", $sections);
    }

    private function filterExistingOrdonnanceTemplateColumns(array $payload): array
    {
        static $columns = null;

        if ($columns === null) {
            $columns = array_flip(Schema::getColumnListing('modele_ordonnances'));
        }

        return array_filter(
            $payload,
            static fn (string $column): bool => isset($columns[$column]),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function ordonnanceTemplateCategories(): array
    {
        return [
            'Douleur / antalgique',
            'Infection / antibiotique',
            'Hypertension',
            'Diabete',
            'Suivi standard',
            'Pediatrie',
            'Cardiologie',
            'Dermatologie',
            'Autre',
        ];
    }

    private function updateModulePermissions(Request $request)
    {
        $request->validate([
            'module_permissions' => ['nullable', 'array'],
            'module_permissions.*' => ['nullable', 'array'],
        ]);

        $moduleIds = array_column($this->managedModules(), 'id');
        $submitted = $request->input('module_permissions', []);

        $users = User::query()->where('role', '!=', 'admin')->get();

        foreach ($users as $user) {
            $allowed = $submitted[$user->id] ?? [];
            $allowed = array_values(array_intersect($moduleIds, $allowed));

            $permissions = [];
            foreach ($moduleIds as $moduleId) {
                $permissions[$moduleId] = in_array($moduleId, $allowed, true);
            }

            $user->forceFill(['module_permissions' => $permissions])->save();
        }

        Log::channel('security_stack')->warning('security.rbac.module_permissions.updated', [
            'actor_user_id' => auth()->id(),
            'affected_users' => $users->pluck('id')->all(),
            'ip' => $request->ip(),
        ]);

        return redirect()->back()->with('success', "Droits d'acces modules mis a jour avec succes.");
    }

    private function managedModules(): array
    {
        return User::managedModules();
    }

    private function getSettingValue(string $key, $default = null)
    {
        return Setting::where('key', $key)->value('value') ?? $default;
    }

    private function deleteStoredLogo(string $path): void
    {
        if ($path !== '' && !str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function listBackupFiles(): array
    {
        $backupRoot = storage_path('app/backups/database');
        if (!File::exists($backupRoot)) {
            return [];
        }

        return collect(File::allFiles($backupRoot))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(function ($file) {
                $absolutePath = $file->getRealPath();
                $relativePath = str_replace(storage_path('app') . DIRECTORY_SEPARATOR, '', $absolutePath);

                return [
                    'name' => $file->getFilename(),
                    'relative_path' => str_replace('\\', '/', $relativePath),
                    'size_human' => $this->formatBytes($file->getSize()),
                    'updated_at' => date('d/m/Y H:i', $file->getMTime()),
                ];
            })
            ->values()
            ->all();
    }

    private function restoreDatabaseFromFile(string $restoreFile): void
    {
        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");

        if (!is_array($connection)) {
            throw new \RuntimeException("Database connection [{$connectionName}] is not configured.");
        }

        $driver = (string) ($connection['driver'] ?? '');

        match ($driver) {
            'mysql', 'mariadb' => $this->restoreMysql($connection, $restoreFile),
            'pgsql' => $this->restorePgsql($connection, $restoreFile),
            'sqlite' => $this->restoreSqlite($connection, $restoreFile),
            default => throw new \RuntimeException("Unsupported driver [{$driver}] for restore."),
        };
    }

    private function restoreMysql(array $connection, string $restoreFile): void
    {
        $binary = (string) env('DB_CLIENT_BINARY', 'mysql');
        $command = [
            $binary,
            '--host=' . (string) ($connection['host'] ?? '127.0.0.1'),
            '--port=' . (string) ($connection['port'] ?? '3306'),
            '--user=' . (string) ($connection['username'] ?? ''),
        ];

        if ((string) ($connection['password'] ?? '') !== '') {
            $command[] = '--password=' . (string) $connection['password'];
        }

        $command[] = (string) ($connection['database'] ?? '');

        $process = new Process($command);
        $process->setTimeout(300);
        $process->setInput(File::get($restoreFile));
        $process->run();

        if (! $process->isSuccessful()) {
            $error = trim($process->getErrorOutput()) ?: 'mysql command failed.';
            throw new \RuntimeException($error . ' Configurez DB_CLIENT_BINARY si mysql n est pas dans le PATH.');
        }
    }

    private function restorePgsql(array $connection, string $restoreFile): void
    {
        $binary = (string) env('PG_CLIENT_BINARY', 'psql');
        $env = (string) ($connection['password'] ?? '') !== '' ? ['PGPASSWORD' => (string) $connection['password']] : null;
        $command = [
            $binary,
            '--host=' . (string) ($connection['host'] ?? '127.0.0.1'),
            '--port=' . (string) ($connection['port'] ?? '5432'),
            '--username=' . (string) ($connection['username'] ?? ''),
            '--dbname=' . (string) ($connection['database'] ?? ''),
        ];

        $process = new Process($command, null, $env);
        $process->setTimeout(300);
        $process->setInput(File::get($restoreFile));
        $process->run();

        if (! $process->isSuccessful()) {
            $error = trim($process->getErrorOutput()) ?: 'psql command failed.';
            throw new \RuntimeException($error . ' Configurez PG_CLIENT_BINARY si psql n est pas dans le PATH.');
        }
    }

    private function restoreSqlite(array $connection, string $restoreFile): void
    {
        $databasePath = (string) ($connection['database'] ?? '');
        if ($databasePath === '' || $databasePath === ':memory:') {
            throw new \RuntimeException('Cannot restore sqlite in-memory database.');
        }

        if (!preg_match('/\.(sqlite|db)$/i', $restoreFile)) {
            throw new \RuntimeException('Pour SQLite, chargez un fichier .sqlite ou .db.');
        }

        if (!preg_match('/^[A-Za-z]:[\\\\\\/]/', $databasePath) && !str_starts_with($databasePath, '/')) {
            $databasePath = base_path($databasePath);
        }

        File::copy($restoreFile, $databasePath);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' o';
        }

        $units = ['Ko', 'Mo', 'Go', 'To'];
        $value = $bytes / 1024;
        foreach ($units as $unit) {
            if ($value < 1024 || $unit === 'To') {
                return number_format($value, $value >= 10 ? 1 : 2, ',', ' ') . ' ' . $unit;
            }
            $value /= 1024;
        }

        return number_format($value, 2, ',', ' ') . ' To';
    }

    /**
     * Calcul l'utilisation disque.
     */
    private function getDiskUsage()
    {
        $path = storage_path('app');
        $size = 0;

        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $size += $this->getDirectorySize($path . '/' . $file);
                }
            }
        }

        return $this->formatBytes($size);
    }

    /**
     * Calcul la taille totale d'un repertoire.
     */
    private function getDirectorySize($path)
    {
        $size = 0;
        if (is_file($path)) {
            $size = filesize($path);
        } elseif (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $size += $this->getDirectorySize($path . '/' . $file);
                }
            }
        }
        return $size;
    }

    /**
     * Calcul la taille de la base de donnees.
     */
    private function getDatabaseSize()
    {
        try {
            $database = env('DB_DATABASE');
            $results = \DB::select(
                'SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size FROM information_schema.tables WHERE table_schema = ?',
                [$database]
            );

            if ($results && isset($results[0]->size)) {
                return round($results[0]->size, 2) . ' MB';
            }

            return '0 MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}
