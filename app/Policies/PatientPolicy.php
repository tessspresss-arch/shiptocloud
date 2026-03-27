<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use App\Services\Security\ClinicalAuthorizationService;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->access()->allows($user, 'patients', 'view');
    }

    public function view(User $user, Patient $patient): bool
    {
        return $this->viewAny($user) && $this->access()->canAccessPatient($user, $patient);
    }

    public function create(User $user): bool
    {
        return $this->access()->allows($user, 'patients', 'create');
    }

    public function update(User $user, Patient $patient): bool
    {
        return $this->access()->allows($user, 'patients', 'edit')
            && $this->access()->canAccessPatient($user, $patient);
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $this->access()->allows($user, 'patients', 'delete')
            && $this->access()->canAccessPatient($user, $patient);
    }

    public function export(User $user): bool
    {
        return $this->access()->allows($user, 'patients', 'export');
    }

    private function access(): ClinicalAuthorizationService
    {
        return app(ClinicalAuthorizationService::class);
    }
}
