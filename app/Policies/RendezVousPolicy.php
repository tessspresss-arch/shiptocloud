<?php

namespace App\Policies;

use App\Models\RendezVous;
use App\Models\User;
use App\Services\Security\ClinicalAuthorizationService;

class RendezVousPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->access()->allows($user, 'planning', 'view');
    }

    public function view(User $user, RendezVous $rendezVous): bool
    {
        return $this->viewAny($user) && $this->access()->canAccessRendezVous($user, $rendezVous);
    }

    public function create(User $user): bool
    {
        return $this->access()->allows($user, 'planning', 'create');
    }

    public function update(User $user, RendezVous $rendezVous): bool
    {
        return $this->access()->allows($user, 'planning', 'edit')
            && $this->access()->canAccessRendezVous($user, $rendezVous);
    }

    public function delete(User $user, RendezVous $rendezVous): bool
    {
        return $this->access()->allows($user, 'planning', 'delete')
            && $this->access()->canAccessRendezVous($user, $rendezVous);
    }

    public function export(User $user): bool
    {
        return $this->access()->allows($user, 'planning', 'export');
    }

    private function access(): ClinicalAuthorizationService
    {
        return app(ClinicalAuthorizationService::class);
    }
}
