<?php

namespace App\Policies;

use App\Models\DossierMedical;
use App\Models\User;
use App\Services\Security\ClinicalAuthorizationService;

class DossierMedicalPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->access()->allows($user, 'patients', 'view');
    }

    public function view(User $user, DossierMedical $dossier): bool
    {
        return $this->viewAny($user) && $this->access()->canAccessDossier($user, $dossier);
    }

    public function create(User $user): bool
    {
        return $this->access()->allows($user, 'patients', 'create');
    }

    public function update(User $user, DossierMedical $dossier): bool
    {
        return $this->access()->allows($user, 'patients', 'edit')
            && $this->access()->canAccessDossier($user, $dossier);
    }

    public function delete(User $user, DossierMedical $dossier): bool
    {
        return $this->access()->allows($user, 'patients', 'delete')
            && $this->access()->canAccessDossier($user, $dossier);
    }

    private function access(): ClinicalAuthorizationService
    {
        return app(ClinicalAuthorizationService::class);
    }
}
