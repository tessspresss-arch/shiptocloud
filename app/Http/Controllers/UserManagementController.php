<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role') && in_array($request->input('role'), ['admin', 'medecin', 'secretaire'], true)) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $status = mb_strtolower(trim((string) $request->input('status')), 'UTF-8');
            if (in_array($status, ['actif', 'desactive', 'en_attente'], true)) {
                if ($status === 'desactive') {
                    $query->whereIn('account_status', ['desactive', 'suspendu']);
                } else {
                    $query->where('account_status', $status);
                }
            }
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->input('created_from'));
        }

        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->input('created_to'));
        }

        $allowedSorts = ['name', 'role', 'account_status', 'last_login_at', 'created_at'];
        $sort = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'name';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $summaryBaseQuery = clone $query;

        $summary = [
            'total' => (clone $summaryBaseQuery)->count(),
            'active' => (clone $summaryBaseQuery)->where('account_status', 'actif')->count(),
            'disabled' => (clone $summaryBaseQuery)->whereIn('account_status', ['desactive', 'suspendu'])->count(),
            'pending' => (clone $summaryBaseQuery)->where('account_status', 'en_attente')->count(),
            'admins' => (clone $summaryBaseQuery)->where('role', 'admin')->count(),
        ];

        $users = $query
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->appends($request->query());

        return view('utilisateurs.index', [
            'users' => $users,
            'sort' => $sort,
            'direction' => $direction,
            'roleOptions' => $this->roleOptions(),
            'accountStatusOptions' => $this->accountStatusOptions(),
            'summary' => $summary,
        ]);
    }

    public function create()
    {
        $managedModules = User::managedModules();

        return view('utilisateurs.create', [
            'managedModules' => $managedModules,
            'accountStatusOptions' => $this->accountStatusOptions(),
            'languageOptions' => $this->languageOptions(),
            'timezoneOptions' => $this->timezoneOptions(),
            'notificationChannelOptions' => $this->notificationChannelOptions(),
            'jobTitleOptions' => $this->jobTitleOptions(),
            'departmentOptions' => $this->departmentOptions(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $managedModules = User::managedModules();
        $moduleIds = array_column($managedModules, 'id');

        $validated = $request->validated();
        $plainPassword = (string) $validated['password'];
        unset($validated['first_name']);

        $validated['two_factor_enabled'] = $request->boolean('two_factor_enabled');
        $validated['force_password_change'] = $request->boolean('force_password_change');

        if ($request->hasFile('avatar')) {
            $this->assertAvatarFileIsSafe($request->file('avatar'));
            $validated['avatar'] = $request->file('avatar')->store('avatars/users', 'public');
        }

        if (($validated['role'] ?? null) === 'admin') {
            $validated['module_permissions'] = [];
        } else {
            $allowed = array_values(array_unique(array_intersect(
                $moduleIds,
                (array) $request->input('module_permissions', [])
            )));

            $permissions = [];
            foreach ($moduleIds as $moduleId) {
                $permissions[$moduleId] = in_array($moduleId, $allowed, true);
            }

            $validated['module_permissions'] = $permissions;
        }

        $created = User::create($validated);
        $mailWarning = null;

        if ($request->boolean('send_password_email')) {
            try {
                Mail::raw(
                    "Bonjour {$created->name},\n\n"
                    . "Votre compte a ete cree sur MEDISYS Pro.\n"
                    . "Identifiant : {$created->email}\n"
                    . "Mot de passe provisoire : {$plainPassword}\n"
                    . "Connexion : " . route('login') . "\n\n"
                    . "Merci de modifier votre mot de passe des la premiere connexion si cette option vous a ete imposee.",
                    function ($message) use ($created) {
                        $message->to($created->email)->subject('Acces a votre compte MEDISYS Pro');
                    }
                );
            } catch (Throwable $exception) {
                Log::warning('security.user.password_mail_failed', [
                    'actor_user_id' => auth()->id(),
                    'target_user_id' => $created->id,
                    'target_email' => $created->email,
                    'ip' => $request->ip(),
                    'error' => $exception->getMessage(),
                ]);

                $mailWarning = "L'utilisateur a ete cree, mais l'envoi de l'email de mot de passe a echoue.";
            }
        }

        Log::channel('security_stack')->info('security.user.created', [
            'actor_user_id' => auth()->id(),
            'target_user_id' => $created->id,
            'target_email' => $created->email,
            'target_role' => $created->role,
            'ip' => $request->ip(),
        ]);

        $redirect = redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur cree avec succes.');

        if ($mailWarning !== null) {
            $redirect->with('warning', $mailWarning);
        }

        return $redirect;
    }

    public function edit(User $utilisateur)
    {
        $managedModules = User::managedModules();
        $moduleIds = array_column($managedModules, 'id');
        $selectedModules = $this->extractAllowedModules($utilisateur, $moduleIds);

        return view('utilisateurs.edit', [
            'user' => $utilisateur,
            'managedModules' => $managedModules,
            'selectedModules' => $selectedModules,
            'accountStatusOptions' => $this->accountStatusOptions(),
            'languageOptions' => $this->languageOptions(),
            'timezoneOptions' => $this->timezoneOptions(),
            'notificationChannelOptions' => $this->notificationChannelOptions(),
            'jobTitleOptions' => $this->jobTitleOptions(),
            'departmentOptions' => $this->departmentOptions(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $utilisateur)
    {
        $managedModules = User::managedModules();
        $moduleIds = array_column($managedModules, 'id');

        $validated = $request->validated();
        unset($validated['first_name']);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['two_factor_enabled'] = $request->boolean('two_factor_enabled');
        $validated['force_password_change'] = $request->boolean('force_password_change');
        $removeAvatar = $request->boolean('remove_avatar');

        if ($removeAvatar && ! $request->hasFile('avatar')) {
            if (!empty($utilisateur->avatar) && !str_starts_with((string) $utilisateur->avatar, 'http')) {
                Storage::disk('public')->delete($utilisateur->avatar);
            }
            $validated['avatar'] = null;
        }

        if ($request->hasFile('avatar')) {
            $this->assertAvatarFileIsSafe($request->file('avatar'));
            if (!empty($utilisateur->avatar) && !str_starts_with((string) $utilisateur->avatar, 'http')) {
                Storage::disk('public')->delete($utilisateur->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars/users', 'public');
        }

        if (($validated['role'] ?? null) === 'admin') {
            $validated['module_permissions'] = [];
        } else {
            $allowed = array_values(array_unique(array_intersect(
                $moduleIds,
                (array) $request->input('module_permissions', [])
            )));

            $permissions = [];
            foreach ($moduleIds as $moduleId) {
                $permissions[$moduleId] = in_array($moduleId, $allowed, true);
            }

            $validated['module_permissions'] = $permissions;
        }

        $beforeRole = $utilisateur->role;
        $beforePermissions = $utilisateur->module_permissions;
        $utilisateur->update($validated);

        Log::channel('security_stack')->info('security.user.updated', [
            'actor_user_id' => auth()->id(),
            'target_user_id' => $utilisateur->id,
            'target_email' => $utilisateur->email,
            'role_before' => $beforeRole,
            'role_after' => $utilisateur->role,
            'permissions_before' => $beforePermissions,
            'permissions_after' => $utilisateur->module_permissions,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur mis a jour avec succes.');
    }

    public function destroy(User $utilisateur)
    {
        if ((int) $utilisateur->id === (int) auth()->id()) {
            return redirect()->route('utilisateurs.index')->withErrors(['error' => "Vous ne pouvez pas supprimer votre propre compte."]);
        }

        if (!empty($utilisateur->avatar) && !str_starts_with((string) $utilisateur->avatar, 'http')) {
            Storage::disk('public')->delete($utilisateur->avatar);
        }

        Log::channel('security_stack')->warning('security.user.deleted', [
            'actor_user_id' => auth()->id(),
            'target_user_id' => $utilisateur->id,
            'target_email' => $utilisateur->email,
            'target_role' => $utilisateur->role,
            'ip' => request()->ip(),
        ]);

        $utilisateur->delete();

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur supprime avec succes.');
    }

    public function toggleStatus(Request $request, User $utilisateur)
    {
        $currentStatus = $utilisateur->account_status_key;
        $requestedStatus = mb_strtolower(trim((string) $request->input('status')), 'UTF-8');
        $targetStatus = in_array($requestedStatus, ['actif', 'desactive', 'en_attente'], true)
            ? $requestedStatus
            : ($currentStatus === 'actif' ? 'desactive' : 'actif');

        if ((int) $utilisateur->id === (int) auth()->id() && $targetStatus !== 'actif') {
            return redirect()->route('utilisateurs.index')->withErrors(['error' => "Vous ne pouvez pas desactiver votre propre compte."]);
        }

        $beforeStatus = $currentStatus;
        $utilisateur->forceFill(['account_status' => $targetStatus])->save();

        Log::channel('security_stack')->warning('security.user.status_changed', [
            'actor_user_id' => auth()->id(),
            'target_user_id' => $utilisateur->id,
            'target_email' => $utilisateur->email,
            'status_before' => $beforeStatus,
            'status_after' => $targetStatus,
            'ip' => $request->ip(),
        ]);

        $message = match ($targetStatus) {
            'actif' => 'Compte utilisateur active avec succes.',
            'en_attente' => 'Compte utilisateur place en attente.',
            default => 'Compte utilisateur desactive avec succes.',
        };

        return redirect()->route('utilisateurs.index')->with('success', $message);
    }

    public function resetPassword(Request $request, User $utilisateur)
    {
        if ((int) $utilisateur->id === (int) auth()->id()) {
            return redirect()->route('utilisateurs.index')->withErrors(['error' => "Utilisez votre profil pour modifier votre propre mot de passe."]);
        }

        $temporaryPassword = Str::password(16);
        $utilisateur->forceFill([
            'password' => $temporaryPassword,
            'force_password_change' => true,
            'remember_token' => Str::random(60),
        ])->save();

        Log::channel('security_stack')->warning('security.user.password_reset', [
            'actor_user_id' => auth()->id(),
            'target_user_id' => $utilisateur->id,
            'target_email' => $utilisateur->email,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('utilisateurs.edit', $utilisateur)
            ->with('success', 'Mot de passe reinitialise avec succes.')
            ->with('generated_password', $temporaryPassword);
    }

    public function activity(User $utilisateur)
    {
        $managedModules = User::managedModules();
        $moduleIds = array_column($managedModules, 'id');
        $selectedModules = $this->extractAllowedModules($utilisateur, $moduleIds);

        return view('utilisateurs.activity', [
            'user' => $utilisateur,
            'selectedModules' => $selectedModules,
            'managedModules' => collect($managedModules)->keyBy('id')->all(),
            'recentEvents' => $this->loadRecentSecurityEventsForUser($utilisateur),
        ]);
    }

    private function extractAllowedModules(User $user, array $moduleIds): array
    {
        $permissions = $user->module_permissions;

        if (empty($permissions)) {
            return [];
        }

        if (array_is_list($permissions)) {
            return array_values(array_intersect($moduleIds, $permissions));
        }

        $selected = [];
        foreach ($moduleIds as $moduleId) {
            if ((bool) ($permissions[$moduleId] ?? false)) {
                $selected[] = $moduleId;
            }
        }

        return $selected;
    }

    private function accountStatusOptions(): array
    {
        return [
            'actif' => 'Actif',
            'desactive' => 'Desactive',
            'en_attente' => 'En attente',
        ];
    }

    private function roleOptions(): array
    {
        return [
            'admin' => 'Admin',
            'medecin' => 'Medecin',
            'secretaire' => 'Secretaire',
        ];
    }

    private function languageOptions(): array
    {
        return [
            'fr' => 'Francais',
            'en' => 'English',
            'ar' => 'Arabe',
        ];
    }

    private function timezoneOptions(): array
    {
        return [
            'Africa/Casablanca' => 'Africa/Casablanca',
            'Europe/Paris' => 'Europe/Paris',
            'UTC' => 'UTC',
        ];
    }

    private function notificationChannelOptions(): array
    {
        return [
            'email' => 'Email',
            'sms' => 'SMS',
            'email_sms' => 'Email + SMS',
        ];
    }

    private function jobTitleOptions(): array
    {
        return [
            'administrateur' => 'Administrateur',
            'medecin' => 'Medecin',
            'secretaire' => 'Secretaire',
            'comptable' => 'Comptable',
        ];
    }

    private function departmentOptions(): array
    {
        return [
            'accueil' => 'Accueil',
            'consultation' => 'Consultation',
            'pharmacie' => 'Pharmacie',
            'administration' => 'Administration',
        ];
    }

    private function assertAvatarFileIsSafe(?UploadedFile $file): void
    {
        if (! $file || ! (bool) env('AV_SCAN_ENABLED', false)) {
            return;
        }

        $binary = (string) env('AV_CLAMSCAN_BINARY', '');
        if ($binary === '') {
            throw ValidationException::withMessages([
                'avatar' => 'Scan antivirus activé, mais aucun binaire clamscan n’est configuré.',
            ]);
        }

        $target = $file->getRealPath();
        if (! $target) {
            throw ValidationException::withMessages([
                'avatar' => 'Impossible de scanner le fichier téléversé.',
            ]);
        }

        $command = escapeshellcmd($binary) . ' --no-summary ' . escapeshellarg($target);
        exec($command, $output, $code);

        if ($code !== 0) {
            throw ValidationException::withMessages([
                'avatar' => 'Le scan antivirus a échoué pour ce fichier.',
            ]);
        }
    }
    private function loadRecentSecurityEventsForUser(User $user, int $limit = 12): array
    {
        $files = glob(storage_path('logs/security*.log')) ?: [];
        rsort($files);

        $events = [];

        foreach ($files as $file) {
            $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (!is_array($lines)) {
                continue;
            }

            foreach (array_reverse($lines) as $line) {
                $parsed = $this->parseSecurityLogLine($line);
                if ($parsed === null) {
                    continue;
                }

                $context = $parsed['context'];
                $matchesUser = (int) ($context['user_id'] ?? 0) === (int) $user->id
                    || (int) ($context['target_user_id'] ?? 0) === (int) $user->id
                    || mb_strtolower((string) ($context['email'] ?? ''), 'UTF-8') === mb_strtolower((string) $user->email, 'UTF-8')
                    || mb_strtolower((string) ($context['target_email'] ?? ''), 'UTF-8') === mb_strtolower((string) $user->email, 'UTF-8');

                if (! $matchesUser) {
                    continue;
                }

                $events[] = [
                    'occurred_at' => $parsed['occurred_at'],
                    'label' => $this->securityEventLabel($parsed['message']),
                    'message' => $parsed['message'],
                    'tone' => $this->securityEventTone($parsed['message']),
                    'ip' => $context['ip'] ?? null,
                    'actor_user_id' => $context['actor_user_id'] ?? null,
                ];

                if (count($events) >= $limit) {
                    break 2;
                }
            }
        }

        return $events;
    }

    private function parseSecurityLogLine(string $line): ?array
    {
        if (!preg_match('/^\[(?<date>[^\]]+)\]\s+\S+:\s+(?<message>[^\s]+)\s*(?<context>\{.*\})?\s*$/', $line, $matches)) {
            return null;
        }

        $contextRaw = trim((string) ($matches['context'] ?? ''));
        $context = $contextRaw !== '' ? json_decode($contextRaw, true) : [];

        return [
            'occurred_at' => (string) ($matches['date'] ?? ''),
            'message' => (string) ($matches['message'] ?? 'security.event'),
            'context' => is_array($context) ? $context : [],
        ];
    }

    private function securityEventLabel(string $message): string
    {
        return match ($message) {
            'auth.login.success' => 'Connexion réussie',
            'auth.logout' => 'Déconnexion',
            'security.user.created' => 'Compte créé',
            'security.user.updated' => 'Compte mis à jour',
            'security.user.deleted' => 'Compte supprimé',
            'security.user.status_changed' => 'Statut modifié',
            'security.user.password_reset' => 'Mot de passe réinitialisé',
            default => str_replace('.', ' ', $message),
        };
    }

    private function securityEventTone(string $message): string
    {
        return match ($message) {
            'auth.login.success', 'security.user.created' => 'success',
            'security.user.deleted', 'security.user.status_changed', 'security.user.password_reset' => 'warning',
            default => 'info',
        };
    }
}
