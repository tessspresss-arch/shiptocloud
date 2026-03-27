<?php

namespace App\Policies;

use App\Models\DocumentMedical;
use App\Models\User;
use App\Services\Security\ClinicalAuthorizationService;

class DocumentMedicalPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->access()->allows($user, 'documents', 'view');
    }

    public function view(User $user, DocumentMedical $document): bool
    {
        return $this->viewAny($user) && $this->access()->canAccessDocument($user, $document);
    }

    public function create(User $user): bool
    {
        return $this->access()->allows($user, 'documents', 'create');
    }

    public function delete(User $user, DocumentMedical $document): bool
    {
        return $this->access()->allows($user, 'documents', 'delete')
            && $this->access()->canAccessDocument($user, $document);
    }

    private function access(): ClinicalAuthorizationService
    {
        return app(ClinicalAuthorizationService::class);
    }
}
