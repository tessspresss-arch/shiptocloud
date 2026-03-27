<?php

namespace App\Policies;

use App\Models\Ordonnance;
use App\Models\User;
use App\Services\Security\ClinicalAuthorizationService;

class OrdonnancePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->access()->allows($user, 'pharmacie', 'view');
    }

    public function view(User $user, Ordonnance $ordonnance): bool
    {
        return $this->viewAny($user) && $this->access()->canAccessOrdonnance($user, $ordonnance);
    }

    public function create(User $user): bool
    {
        return $this->access()->allows($user, 'pharmacie', 'create');
    }

    public function update(User $user, Ordonnance $ordonnance): bool
    {
        return $this->access()->allows($user, 'pharmacie', 'edit')
            && $this->access()->canAccessOrdonnance($user, $ordonnance);
    }

    public function delete(User $user, Ordonnance $ordonnance): bool
    {
        return $this->access()->allows($user, 'pharmacie', 'delete')
            && $this->access()->canAccessOrdonnance($user, $ordonnance);
    }

    public function export(User $user): bool
    {
        return $this->access()->allows($user, 'pharmacie', 'export');
    }

    private function access(): ClinicalAuthorizationService
    {
        return app(ClinicalAuthorizationService::class);
    }
}
