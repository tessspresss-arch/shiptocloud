<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermissionOverride;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RbacFoundationSeeder extends Seeder
{
    private const ACTIONS = ['view', 'create', 'edit', 'delete', 'export'];

    public function run(): void
    {
        DB::transaction(function () {
            [$rolesByName, $permissionsByCode] = $this->seedRolesAndPermissions();
            $this->seedRolePermissions($rolesByName, $permissionsByCode);
            $this->attachUsersToRoles($rolesByName);
            $this->mapLegacyModulePermissionsToOverrides($permissionsByCode);
        });

        $this->command?->info('RBAC foundation seeded successfully.');
    }

    private function seedRolesAndPermissions(): array
    {
        $admin = Role::updateOrCreate(
            ['name' => 'admin'],
            ['label' => 'Administrateur', 'is_system' => true, 'parent_id' => null]
        );

        $staff = Role::updateOrCreate(
            ['name' => 'staff'],
            ['label' => 'Personnel', 'is_system' => true, 'parent_id' => null]
        );

        $medecin = Role::updateOrCreate(
            ['name' => 'medecin'],
            ['label' => 'Médecin', 'is_system' => true, 'parent_id' => $staff->id]
        );

        $secretaire = Role::updateOrCreate(
            ['name' => 'secretaire'],
            ['label' => 'Secrétaire', 'is_system' => true, 'parent_id' => $staff->id]
        );

        $resources = collect(User::managedModules())
            ->pluck('id')
            ->merge(['parametres', 'utilisateurs', 'audit', 'integrations'])
            ->unique()
            ->values();

        $permissionsByCode = [];
        foreach ($resources as $resource) {
            foreach (self::ACTIONS as $action) {
                $code = sprintf('%s.%s', $resource, $action);
                $permissionsByCode[$code] = Permission::updateOrCreate(
                    ['code' => $code],
                    ['resource' => $resource, 'action' => $action]
                );
            }
        }

        return [
            [
                'admin' => $admin,
                'staff' => $staff,
                'medecin' => $medecin,
                'secretaire' => $secretaire,
            ],
            $permissionsByCode,
        ];
    }

    private function seedRolePermissions(array $rolesByName, array $permissionsByCode): void
    {
        $allPermissionIds = collect($permissionsByCode)->pluck('id')->all();
        $rolesByName['admin']->permissions()->sync($allPermissionIds);

        $staffResources = ['dashboard', 'patients', 'planning', 'documents'];
        $staffPermissionIds = $this->permissionIdsForResources($permissionsByCode, $staffResources, ['view']);
        $rolesByName['staff']->permissions()->sync($staffPermissionIds);

        $medecinResources = ['consultations', 'examens', 'pharmacie', 'rapports', 'statistiques'];
        $medecinPermissionIds = $this->permissionIdsForResources($permissionsByCode, $medecinResources, ['view', 'create', 'edit', 'export']);
        $rolesByName['medecin']->permissions()->sync($medecinPermissionIds);

        $secretaireResources = ['facturation', 'contacts', 'sms'];
        $secretairePermissionIds = $this->permissionIdsForResources($permissionsByCode, $secretaireResources, ['view', 'create', 'edit', 'export']);
        $rolesByName['secretaire']->permissions()->sync($secretairePermissionIds);
    }

    private function attachUsersToRoles(array $rolesByName): void
    {
        $users = User::query()->select(['id', 'role'])->get();

        foreach ($users as $user) {
            $roleName = mb_strtolower(trim((string) $user->role), 'UTF-8');
            $role = $rolesByName[$roleName] ?? $rolesByName['staff'];

            $user->roles()->syncWithoutDetaching([$role->id]);
        }
    }

    private function mapLegacyModulePermissionsToOverrides(array $permissionsByCode): void
    {
        $users = User::query()->select(['id', 'module_permissions'])->get();

        foreach ($users as $user) {
            if ($user->isAdmin()) {
                continue;
            }

            $allowedModules = $this->extractAllowedModules($user->module_permissions);
            foreach ($allowedModules as $moduleId) {
                foreach (self::ACTIONS as $action) {
                    $code = $moduleId . '.' . $action;
                    $permission = $permissionsByCode[$code] ?? null;

                    if (!$permission) {
                        continue;
                    }

                    UserPermissionOverride::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'permission_id' => $permission->id,
                        ],
                        [
                            'effect' => 'allow',
                            'created_by' => null,
                        ]
                    );
                }
            }
        }
    }

    private function extractAllowedModules(mixed $modulePermissions): array
    {
        if (!is_array($modulePermissions) || $modulePermissions === []) {
            return [];
        }

        if (array_is_list($modulePermissions)) {
            return array_values(array_unique(array_filter(array_map('strval', $modulePermissions))));
        }

        $allowed = [];
        foreach ($modulePermissions as $module => $isAllowed) {
            if ((bool) $isAllowed) {
                $allowed[] = (string) $module;
            }
        }

        return array_values(array_unique($allowed));
    }

    private function permissionIdsForResources(array $permissionsByCode, array $resources, array $actions): array
    {
        $ids = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $code = $resource . '.' . $action;
                $permission = $permissionsByCode[$code] ?? null;
                if ($permission) {
                    $ids[] = $permission->id;
                }
            }
        }

        return array_values(array_unique($ids));
    }
}
