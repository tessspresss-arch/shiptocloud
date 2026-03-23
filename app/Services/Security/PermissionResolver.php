<?php

namespace App\Services\Security;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class PermissionResolver
{
    public function hasPermission(User $user, string $permissionCode): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $effective = $this->getEffectivePermissions($user);

        return isset($effective[$permissionCode]) && $effective[$permissionCode] === true;
    }

    public function getEffectivePermissions(User $user): array
    {
        $resolved = [];

        $userRoles = $user->roles()->with('permissions', 'parent.permissions', 'parent.parent.permissions')->get();

        foreach ($userRoles as $role) {
            foreach ($this->collectRolePermissions($role) as $code) {
                $resolved[$code] = true;
            }
        }

        $overrides = $user->permissionOverrides()->with('permission')->get();
        foreach ($overrides as $override) {
            $code = $override->permission?->code;
            if (!$code) {
                continue;
            }

            $resolved[$code] = $override->effect === 'allow';
        }

        return $resolved;
    }

    private function collectRolePermissions(Role $role): array
    {
        $codes = [];
        $visited = [];
        $current = $role;

        while ($current && !isset($visited[$current->id])) {
            $visited[$current->id] = true;

            foreach ($current->permissions as $permission) {
                if ($permission instanceof Permission) {
                    $codes[] = $permission->code;
                }
            }

            $current = $current->parent;
            if ($current) {
                $current->loadMissing('permissions', 'parent');
            }
        }

        return array_values(array_unique($codes));
    }
}
