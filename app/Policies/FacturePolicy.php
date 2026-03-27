<?php

namespace App\Policies;

use App\Models\Facture;
use App\Models\User;
use App\Services\Security\ClinicalAuthorizationService;

class FacturePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->access()->allows($user, 'facturation', 'view');
    }

    public function view(User $user, Facture $facture): bool
    {
        return $this->viewAny($user) && $this->access()->canAccessFacture($user, $facture);
    }

    public function create(User $user): bool
    {
        return $this->access()->allows($user, 'facturation', 'create');
    }

    public function update(User $user, Facture $facture): bool
    {
        return $this->access()->allows($user, 'facturation', 'edit')
            && $this->access()->canAccessFacture($user, $facture);
    }

    public function delete(User $user, Facture $facture): bool
    {
        return $this->access()->allows($user, 'facturation', 'delete')
            && $this->access()->canAccessFacture($user, $facture);
    }

    public function export(User $user): bool
    {
        return $this->access()->allows($user, 'facturation', 'export');
    }

    private function access(): ClinicalAuthorizationService
    {
        return app(ClinicalAuthorizationService::class);
    }
}
