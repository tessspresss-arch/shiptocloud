<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;
use App\Services\Security\ClinicalAuthorizationService;

class ConsultationPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->access()->allows($user, 'consultations', 'view');
    }

    public function view(User $user, Consultation $consultation): bool
    {
        return $this->viewAny($user) && $this->access()->canAccessConsultation($user, $consultation);
    }

    public function create(User $user): bool
    {
        return $this->access()->allows($user, 'consultations', 'create');
    }

    public function update(User $user, Consultation $consultation): bool
    {
        return $this->access()->allows($user, 'consultations', 'edit')
            && $this->access()->canAccessConsultation($user, $consultation);
    }

    public function delete(User $user, Consultation $consultation): bool
    {
        return $this->access()->allows($user, 'consultations', 'delete')
            && $this->access()->canAccessConsultation($user, $consultation);
    }

    public function export(User $user): bool
    {
        return $this->access()->allows($user, 'consultations', 'export');
    }

    private function access(): ClinicalAuthorizationService
    {
        return app(ClinicalAuthorizationService::class);
    }
}
