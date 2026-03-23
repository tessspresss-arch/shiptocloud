<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\PermissionAuditLog;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserPermissionOverride;
use App\Services\Security\PermissionResolver;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GovernanceCenterController extends Controller
{
    public function index()
    {
        return $this->renderSection('overview');
    }

    public function general()
    {
        return $this->renderSection('general');
    }

    public function rbac(Request $request, PermissionResolver $permissionResolver)
    {
        $roles = Role::query()->with('parent')->orderBy('name')->get();
        $permissions = Permission::query()->orderBy('resource')->orderBy('action')->get();
        $users = User::query()->orderBy('name')->get(['id', 'name', 'email', 'role']);

        $selectedUserId = (int) $request->query('user_id', 0);
        if ($selectedUserId <= 0) {
            $selectedUserId = (int) ($users->first()->id ?? 0);
        }

        $selectedUser = $selectedUserId > 0
            ? User::query()->with(['roles.parent', 'permissionOverrides.permission'])->find($selectedUserId)
            : null;

        $selectedUserRoleIds = $selectedUser ? $selectedUser->roles->pluck('id')->all() : [];
        $selectedUserOverrides = $selectedUser
            ? $selectedUser->permissionOverrides->mapWithKeys(function ($override) {
                return [$override->permission_id => $override->effect];
            })->toArray()
            : [];

        $effectivePermissions = $selectedUser
            ? $permissionResolver->getEffectivePermissions($selectedUser)
            : [];

        $permissionsByResource = $permissions
            ->groupBy('resource')
            ->map(function ($items) use ($effectivePermissions) {
                return $items->map(function ($permission) use ($effectivePermissions) {
                    return [
                        'code' => $permission->code,
                        'action' => $permission->action,
                        'allowed' => (bool) ($effectivePermissions[$permission->code] ?? false),
                    ];
                })->values();
            });

        return $this->renderSection('rbac', [
            'roles' => $roles,
            'permissions' => $permissions,
            'permissionsCount' => $permissions->count(),
            'usersForPreview' => $users,
            'selectedUser' => $selectedUser,
            'selectedUserId' => $selectedUserId,
            'selectedUserRoleIds' => $selectedUserRoleIds,
            'selectedUserOverrides' => $selectedUserOverrides,
            'permissionsByResource' => $permissionsByResource,
            'effectiveCount' => collect($effectivePermissions)->filter()->count(),
        ]);
    }

    public function updateRbacRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ]);

        if ((int) $validated['user_id'] !== (int) $user->id) {
            return redirect()->route('admin.settings.rbac', ['user_id' => $user->id])
                ->withErrors(['error' => 'Incoherence utilisateur detectee.']);
        }

        $newRoleIds = array_values(array_unique(array_map('intval', $validated['role_ids'] ?? [])));

        if ($newRoleIds === [] && $user->isAdmin()) {
            return redirect()->route('admin.settings.rbac', ['user_id' => $user->id])
                ->withErrors(['error' => 'Un administrateur doit conserver au moins un role.']);
        }

        $oldRoleIds = $user->roles()->pluck('roles.id')->map(fn ($id) => (int) $id)->all();

        $user->roles()->sync($newRoleIds);

        $this->logPermissionChange(
            targetType: User::class,
            targetId: $user->id,
            changeSet: [
                'type' => 'roles_sync',
                'old_role_ids' => $oldRoleIds,
                'new_role_ids' => $newRoleIds,
            ],
            request: $request
        );

        return redirect()->route('admin.settings.rbac', ['user_id' => $user->id])
            ->with('success', 'Roles utilisateur mis a jour avec succes.');
    }

    public function updateRbacOverrides(Request $request, User $user)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'overrides' => ['nullable', 'array'],
            'overrides.*' => ['nullable', 'in:inherit,allow,deny'],
        ]);

        if ((int) $validated['user_id'] !== (int) $user->id) {
            return redirect()->route('admin.settings.rbac', ['user_id' => $user->id])
                ->withErrors(['error' => 'Incoherence utilisateur detectee.']);
        }

        $submittedOverrides = $validated['overrides'] ?? [];
        $permissionIds = Permission::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $permissionIdLookup = array_flip($permissionIds);

        $existing = UserPermissionOverride::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('permission_id');

        $changes = [];

        DB::transaction(function () use ($submittedOverrides, $permissionIdLookup, $existing, $user, $request, &$changes) {
            foreach ($submittedOverrides as $permissionIdRaw => $effect) {
                $permissionId = (int) $permissionIdRaw;
                if (!isset($permissionIdLookup[$permissionId])) {
                    continue;
                }

                $current = $existing->get($permissionId);
                $currentEffect = $current?->effect;

                if ($effect === 'inherit' || $effect === null || $effect === '') {
                    if ($current) {
                        $current->delete();
                        $changes[] = [
                            'permission_id' => $permissionId,
                            'from' => $currentEffect,
                            'to' => 'inherit',
                        ];
                    }

                    continue;
                }

                if ($current) {
                    if ($currentEffect !== $effect) {
                        $current->update([
                            'effect' => $effect,
                            'created_by' => $request->user()?->id,
                        ]);

                        $changes[] = [
                            'permission_id' => $permissionId,
                            'from' => $currentEffect,
                            'to' => $effect,
                        ];
                    }
                } else {
                    UserPermissionOverride::create([
                        'user_id' => $user->id,
                        'permission_id' => $permissionId,
                        'effect' => $effect,
                        'created_by' => $request->user()?->id,
                    ]);

                    $changes[] = [
                        'permission_id' => $permissionId,
                        'from' => 'inherit',
                        'to' => $effect,
                    ];
                }
            }
        });

        if ($changes !== []) {
            $this->logPermissionChange(
                targetType: User::class,
                targetId: $user->id,
                changeSet: [
                    'type' => 'permission_overrides_sync',
                    'changes' => $changes,
                ],
                request: $request
            );
        }

        return redirect()->route('admin.settings.rbac', ['user_id' => $user->id])
            ->with('success', 'Overrides de permissions mis a jour avec succes.');
    }

    public function security()
    {
        return $this->renderSection('security');
    }

    public function audit(Request $request)
    {
        $auditQuery = $this->buildAuditQueryFromRequest($request);

        $auditLogs = $auditQuery
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->query());

        $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);
        $modules = AuditLog::query()->select('module')->distinct()->orderBy('module')->pluck('module');
        $retentionDays = (int) Setting::get('audit.retention_days', 365);

        return $this->renderSection('audit', [
            'auditLogs' => $auditLogs,
            'auditUsers' => $users,
            'auditModules' => $modules,
            'auditFilters' => [
                'user_id' => $request->query('user_id'),
                'module' => $request->query('module'),
                'date_from' => $request->query('date_from'),
                'date_to' => $request->query('date_to'),
            ],
            'auditRetentionDays' => $retentionDays,
        ]);
    }

    public function exportAudit(Request $request): StreamedResponse
    {
        $logs = $this->buildAuditQueryFromRequest($request)
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->get();

        $filename = 'audit_logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility (accents/arabe).
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'id',
                'date',
                'utilisateur',
                'email',
                'module',
                'action',
                'target_type',
                'target_id',
                'ip_address',
            ]);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    optional($log->created_at)?->format('Y-m-d H:i:s'),
                    $log->user?->name,
                    $log->user?->email,
                    $log->module,
                    $log->action,
                    $log->target_type,
                    $log->target_id,
                    $log->ip_address,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function updateAuditRetention(Request $request)
    {
        $validated = $request->validate([
            'retention_days' => ['required', 'integer', 'min:1', 'max:3650'],
        ]);

        Setting::set('audit.retention_days', (int) $validated['retention_days'], 'integer');

        return redirect()->route('admin.settings.audit')
            ->with('success', 'Retention des logs d audit mise a jour.');
    }

    public function notifications()
    {
        return $this->renderSection('notifications');
    }

    public function performance()
    {
        return $this->renderSection('performance');
    }

    public function integrations()
    {
        return $this->renderSection('integrations');
    }

    private function renderSection(string $section, array $payload = [])
    {
        $settings = Setting::query()
            ->whereIn('key', ['sms_enabled', 'appointment_reminders_enabled', 'smtp_host', 'external_api_base_url', 'google_maps_key', 'sms_api_key'])
            ->pluck('value', 'key');

        $backupDirectory = storage_path('app/backups/database');
        $backupCount = File::isDirectory($backupDirectory)
            ? count(File::files($backupDirectory))
            : 0;

        $rbacConfigured = false;
        $auditEnabled = false;

        try {
            $rbacConfigured = Role::query()->exists() && Permission::query()->exists();
        } catch (\Throwable) {
            $rbacConfigured = false;
        }

        try {
            AuditLog::query()->latest('id')->limit(1)->get();
            $auditEnabled = true;
        } catch (\Throwable) {
            $auditEnabled = false;
        }

        $notificationsConfigured = !empty($settings['smtp_host'])
            || !empty($settings['sms_enabled'])
            || !empty($settings['appointment_reminders_enabled']);

        $apiSignals = collect([
            !empty($settings['external_api_base_url']),
            !empty($settings['google_maps_key']),
            !empty($settings['sms_api_key']),
        ])->filter()->count();

        $platformStatuses = [
            $this->makePlatformStatus('Securite applicative', !empty(config('app.key')) ? 'actif' : 'partiel', 'Cle applicative et protections de base disponibles.', 'fa-shield-halved'),
            $this->makePlatformStatus('RBAC actif', $rbacConfigured ? 'actif' : 'desactive', $rbacConfigured ? 'Roles et permissions exploitables.' : 'Configuration des roles ou permissions incomplete.', 'fa-user-lock'),
            $this->makePlatformStatus('Cache actif', config('cache.default') !== 'array' ? 'actif' : 'partiel', 'Store de cache configure pour les operations systeme.', 'fa-gauge-high'),
            $this->makePlatformStatus('API disponibles', $apiSignals >= 2 ? 'actif' : ($apiSignals === 1 ? 'partiel' : 'desactive'), 'Connecteurs externes et cles techniques detectes.', 'fa-plug'),
            $this->makePlatformStatus('Notifications actives', $notificationsConfigured ? 'actif' : 'desactive', 'SMTP, SMS ou rappels automatiques configures.', 'fa-bell'),
            $this->makePlatformStatus('Sauvegardes', $backupCount > 0 ? 'actif' : 'desactive', $backupCount > 0 ? $backupCount . ' sauvegarde(s) disponible(s).' : 'Aucune sauvegarde detectee.', 'fa-database'),
            $this->makePlatformStatus('Audit log', $auditEnabled ? 'actif' : 'partiel', $auditEnabled ? 'Journal d audit accessible.' : 'Journal a verifier cote schema ou donnees.', 'fa-clipboard-list'),
        ];

        $sections = [
            'overview' => 'Centre gouvernance',
            'general' => 'Parametres avances',
            'rbac' => 'RBAC',
            'security' => 'Securite',
            'audit' => 'Audit',
            'notifications' => 'Notifications',
            'performance' => 'Performance',
            'integrations' => 'API',
        ];

        $sectionCards = [
            'overview' => ['icon' => 'fa-grid-2', 'desc' => 'Vue d ensemble et etat plateforme'],
            'general' => ['icon' => 'fa-sliders', 'desc' => 'Reglages systeme et gouvernance'],
            'rbac' => ['icon' => 'fa-user-lock', 'desc' => 'Roles, permissions et heritage'],
            'security' => ['icon' => 'fa-shield-halved', 'desc' => 'Protection applicative et acces'],
            'audit' => ['icon' => 'fa-clipboard-list', 'desc' => 'Tracabilite et conformite'],
            'notifications' => ['icon' => 'fa-bell', 'desc' => 'Canaux et alertes operationnelles'],
            'performance' => ['icon' => 'fa-gauge-high', 'desc' => 'Cache, maintenance et performance'],
            'integrations' => ['icon' => 'fa-plug', 'desc' => 'API, connecteurs et webhooks'],
        ];

        return view('parametres.v2.index', array_merge([
            'activeSection' => $section,
            'sections' => $sections,
            'sectionCards' => $sectionCards,
            'platformStatuses' => $platformStatuses,
        ], $payload));
    }

    private function makePlatformStatus(string $label, string $state, string $hint, string $icon): array
    {
        $stateMeta = match ($state) {
            'actif' => ['badge' => 'success', 'label' => 'Actif'],
            'partiel' => ['badge' => 'warning', 'label' => 'Partiel'],
            default => ['badge' => 'danger', 'label' => 'Desactive'],
        };

        return [
            'label' => $label,
            'state' => $state,
            'badge' => $stateMeta['badge'],
            'state_label' => $stateMeta['label'],
            'hint' => $hint,
            'icon' => $icon,
        ];
    }

    private function buildAuditQueryFromRequest(Request $request)
    {
        $query = AuditLog::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->query('user_id'));
        }

        if ($request->filled('module')) {
            $query->where('module', (string) $request->query('module'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', (string) $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', (string) $request->query('date_to'));
        }

        return $query;
    }

    private function logPermissionChange(string $targetType, ?int $targetId, array $changeSet, Request $request): void
    {
        PermissionAuditLog::create([
            'actor_user_id' => $request->user()?->id,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'change_set' => $changeSet,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'module' => 'rbac',
            'action' => (string) ($changeSet['type'] ?? 'permission_change'),
            'target_type' => $targetType,
            'target_id' => $targetId,
            'old_values' => null,
            'new_values' => $changeSet,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}




