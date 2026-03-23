<?php

namespace App\Services\Settings;

use App\Models\Medecin;
use App\Models\Medicament;
use App\Models\ModeleOrdonnance;
use App\Models\Setting;
use App\Models\User;

class SettingsDashboardService
{
    public function __construct(private readonly SettingsMaintenanceService $maintenanceService)
    {
    }

    public function buildIndexData(bool $isAdmin): array
    {
        $allSettings = Setting::pluck('value', 'key')->toArray();
        $permissionUsers = User::query()->where('role', '!=', 'admin')->orderBy('name')->get();
        $managedModules = User::managedModules();
        $backupFiles = $this->maintenanceService->listBackupFiles();
        $latestBackup = $backupFiles[0] ?? null;
        $medecins = Medecin::query()->orderBy('prenom')->orderBy('nom')->get(['id', 'prenom', 'nom', 'specialite']);
        $medicaments = Medicament::query()->orderBy('nom_commercial')->get(['id', 'nom_commercial', 'dci', 'presentation', 'posologie']);
        $ordonnanceTemplates = ModeleOrdonnance::query()
            ->with('medecin:id,prenom,nom,specialite')
            ->orderByDesc('is_actif')
            ->orderByDesc('est_template_general')
            ->orderBy('nom')
            ->get();

        $ordonnanceTemplates->transform(function (ModeleOrdonnance $template) {
            $template->template_rows = $template->medicaments_template ?: [[]];
            return $template;
        });

        $ordonnanceTemplateCategories = $this->ordonnanceTemplateCategories();
        $ordonnanceTemplateStats = [
            'total' => $ordonnanceTemplates->count(),
            'active' => $ordonnanceTemplates->where('is_actif', true)->count(),
            'general' => $ordonnanceTemplates->where('est_template_general', true)->count(),
            'medications' => $ordonnanceTemplates->sum(fn (ModeleOrdonnance $template): int => count($template->medicaments_template ?? [])),
        ];

        $navSections = [
            ['id' => 'general', 'label' => __('messages.settings.general'), 'icon' => 'fa-sliders-h', 'desc' => __('messages.settings.base_system'), 'domain' => 'Configuration systeme'],
            ['id' => 'cabinet', 'label' => __('messages.settings.cabinet'), 'icon' => 'fa-hospital', 'desc' => __('messages.settings.identity_contact'), 'domain' => 'Configuration systeme'],
            ['id' => 'medical', 'label' => __('messages.settings.medical'), 'icon' => 'fa-stethoscope', 'desc' => __('messages.settings.clinical_flow'), 'domain' => 'Configuration systeme'],
            ['id' => 'integration', 'label' => __('messages.settings.integrations'), 'icon' => 'fa-plug', 'desc' => 'SMTP, SMS, API', 'domain' => 'Configuration systeme'],
            ['id' => 'security', 'label' => __('messages.settings.security'), 'icon' => 'fa-shield-halved', 'desc' => __('messages.settings.access_and_logs'), 'domain' => 'Securite'],
            ['id' => 'backup', 'label' => __('messages.settings.backups'), 'icon' => 'fa-database', 'desc' => __('messages.settings.backup_restore'), 'domain' => 'Sauvegardes'],
            ['id' => 'communication', 'label' => 'Communication', 'icon' => 'fa-comments', 'desc' => 'SMS et messages', 'domain' => 'Communication'],
            ['id' => 'ordonnances', 'label' => __('messages.settings.prescriptions'), 'icon' => 'fa-prescription-bottle-medical', 'desc' => __('messages.settings.templates_and_prescriptions'), 'domain' => 'Ordonnances'],
        ];

        if ($isAdmin) {
            $navSections[] = ['id' => 'permissions', 'label' => __('messages.settings.permissions'), 'icon' => 'fa-user-lock', 'desc' => __('messages.settings.modules_roles'), 'domain' => 'Securite'];
        }

        $cabinetLogo = $allSettings['cabinet_logo'] ?? '';
        $cabinetLogoUrl = $cabinetLogo ? asset('storage/' . ltrim($cabinetLogo, '/')) : null;
        $backupCount = count($backupFiles);
        $activeTemplateCount = $ordonnanceTemplateStats['active'] ?? 0;
        $templateMedicationCount = $ordonnanceTemplateStats['medications'] ?? 0;
        $smsEnabled = !empty($allSettings['sms_enabled']);
        $remindersEnabled = !empty($allSettings['appointment_reminders_enabled']);
        $communicationState = $smsEnabled && $remindersEnabled ? __('messages.settings.operational') : ($smsEnabled ? __('messages.settings.partial') : __('messages.settings.review'));
        $configurationReadiness = collect([count($navSections) >= 8, $backupCount > 0, $smsEnabled, $activeTemplateCount > 0])->filter()->count();
        $readinessLabel = $configurationReadiness >= 4 ? __('messages.settings.configuration_mature') : ($configurationReadiness >= 2 ? __('messages.settings.configuration_progress') : __('messages.settings.configuration_initial'));
        $settingsOverview = [
            ['label' => 'Domaines actifs', 'value' => count(collect($navSections)->pluck('domain')->unique()), 'hint' => 'Configuration pilotee', 'meta' => __('messages.settings.structured_navigation'), 'status' => __('messages.settings.core_domains'), 'icon' => 'fa-layer-group', 'tone' => 'slate'],
            ['label' => __('messages.settings.backups'), 'value' => $backupCount, 'hint' => 'Copies disponibles', 'meta' => $backupCount > 0 ? __('messages.settings.instant_restore') : __('messages.settings.protection_to_improve'), 'status' => $backupCount > 0 ? __('messages.settings.operational') : 'A configurer', 'icon' => 'fa-database', 'tone' => 'slate'],
            ['label' => __('messages.settings.communication'), 'value' => $communicationState, 'hint' => $remindersEnabled ? 'Rappels automatiques actifs' : __('messages.settings.reminders_to_configure'), 'meta' => $smsEnabled ? __('messages.settings.sms_channel_available') : __('messages.settings.sms_channel_inactive'), 'status' => $smsEnabled ? 'Canal actif' : 'Canal inactif', 'icon' => 'fa-comment-medical', 'tone' => 'slate'],
            ['label' => "Modeles d'ordonnance", 'value' => $activeTemplateCount, 'hint' => $templateMedicationCount . ' ' . __('messages.settings.memorized_treatment_lines'), 'meta' => $activeTemplateCount > 0 ? __('messages.settings.prescriptions_faster') : __('messages.settings.template_to_initialize'), 'status' => $activeTemplateCount > 0 ? __('messages.settings.template_ready') : 'En attente', 'icon' => 'fa-prescription-bottle-medical', 'tone' => 'slate'],
        ];
        $newTemplateRows = old('medicaments_template', [[]]);

        return compact('allSettings', 'permissionUsers', 'managedModules', 'backupFiles', 'latestBackup', 'medecins', 'medicaments', 'ordonnanceTemplates', 'ordonnanceTemplateCategories', 'ordonnanceTemplateStats', 'navSections', 'cabinetLogoUrl', 'backupCount', 'activeTemplateCount', 'templateMedicationCount', 'communicationState', 'configurationReadiness', 'readinessLabel', 'settingsOverview', 'newTemplateRows');
    }

    private function ordonnanceTemplateCategories(): array
    {
        return ModeleOrdonnance::query()->whereNotNull('categorie')->where('categorie', '!=', '')->distinct()->orderBy('categorie')->pluck('categorie')->values()->all();
    }
}
