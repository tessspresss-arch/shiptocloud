<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'professional_phone',
        'job_title',
        'speciality',
        'order_number',
        'department',
        'account_status',
        'account_expires_at',
        'avatar',
        'ui_language',
        'timezone',
        'notification_channel',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'two_factor_recovery_codes',
        'force_password_change',
        'last_login_at',
        'last_activity_at',
        'sidebar_collapsed',
        'sidebar_preferences_updated_at',
        'module_permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'module_permissions' => 'array',
        'account_expires_at' => 'date',
        'two_factor_enabled' => 'boolean',
        'two_factor_secret' => 'encrypted',
        'two_factor_confirmed_at' => 'datetime',
        'two_factor_recovery_codes' => 'array',
        'force_password_change' => 'boolean',
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isMedecin()
    {
        return $this->hasRole('medecin');
    }

    public function isSecretaire()
    {
        return $this->hasRole('secretaire');
    }

    public function hasRole(string $role): bool
    {
        return $this->normalizeRole($this->role) === $this->normalizeRole($role);
    }

    public function hasAnyRole(array $roles): bool
    {
        $currentRole = $this->normalizeRole($this->role);

        foreach ($roles as $role) {
            if ($currentRole === $this->normalizeRole((string) $role)) {
                return true;
            }
        }

        return false;
    }

    public function hasModuleAccess(string $moduleId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $permissions = $this->module_permissions;

        // Security: fail closed when no permissions are configured.
        if (empty($permissions)) {
            return false;
        }

        if (array_is_list($permissions)) {
            return in_array($moduleId, $permissions, true);
        }

        // Backward compatibility for users saved before "dashboard" permission existed.
        if ($moduleId === 'dashboard' && !array_key_exists('dashboard', $permissions)) {
            return true;
        }

        return (bool) ($permissions[$moduleId] ?? false);
    }

    private function normalizeRole(?string $role): string
    {
        return mb_strtolower(trim((string) $role), 'UTF-8');
    }

    public static function managedModules(): array
    {
        return [
            ['id' => 'dashboard', 'label' => 'Tableau de bord'],
            ['id' => 'patients', 'label' => 'Patients'],
            ['id' => 'consultations', 'label' => 'Consultations'],
            ['id' => 'planning', 'label' => 'Planning'],
            ['id' => 'medecins', 'label' => 'Médecins'],
            ['id' => 'pharmacie', 'label' => 'Pharmacie'],
            ['id' => 'facturation', 'label' => 'Facturation'],
            ['id' => 'examens', 'label' => 'Bilans complémentaires'],
            ['id' => 'depenses', 'label' => 'Dépenses'],
            ['id' => 'contacts', 'label' => 'Contacts'],
            ['id' => 'sms', 'label' => 'Rappels SMS'],
            ['id' => 'documents', 'label' => 'Documents'],
            ['id' => 'statistiques', 'label' => 'Statistiques'],
            ['id' => 'rapports', 'label' => 'Rapports'],
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function permissionOverrides(): HasMany
    {
        return $this->hasMany(UserPermissionOverride::class);
    }

    public function consultationAiGenerations(): HasMany
    {
        return $this->hasMany(ConsultationAiGeneration::class);
    }

    public function hasPermissionCode(string $permissionCode): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return app(\App\Services\Security\PermissionResolver::class)
            ->hasPermission($this, $permissionCode);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        $avatar = trim((string) ($this->avatar ?? ''));
        if ($avatar === '') {
            return null;
        }

        if (
            str_starts_with($avatar, 'http://')
            || str_starts_with($avatar, 'https://')
            || str_starts_with($avatar, 'data:')
        ) {
            return $avatar;
        }

        if (str_starts_with($avatar, 'storage/')) {
            return asset($avatar);
        }

        return asset('storage/' . ltrim($avatar, '/'));
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $initials = '';

        foreach (array_slice(array_filter($parts), 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
        }

        return $initials !== '' ? $initials : 'US';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->normalizeRole($this->role)) {
            'admin' => 'Admin',
            'medecin' => 'Medecin',
            'secretaire' => 'Secretaire',
            default => ucfirst((string) $this->role),
        };
    }

    public function getAccountStatusKeyAttribute(): string
    {
        $status = mb_strtolower(trim((string) ($this->account_status ?? 'actif')), 'UTF-8');

        return match ($status) {
            'suspendu' => 'desactive',
            'en_attente' => 'en_attente',
            'desactive' => 'desactive',
            default => 'actif',
        };
    }

    public function getAccountStatusLabelAttribute(): string
    {
        return match ($this->account_status_key) {
            'desactive' => 'Desactive',
            'en_attente' => 'En attente',
            default => 'Actif',
        };
    }
}
