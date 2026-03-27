<?php

namespace App\Providers;

use App\Models\Consultation;
use App\Models\DocumentMedical;
use App\Models\DossierMedical;
use App\Models\Facture;
use App\Models\Ordonnance;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Policies\ConsultationPolicy;
use App\Policies\DocumentMedicalPolicy;
use App\Policies\DossierMedicalPolicy;
use App\Policies\FacturePolicy;
use App\Policies\OrdonnancePolicy;
use App\Policies\PatientPolicy;
use App\Policies\RendezVousPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Consultation::class => ConsultationPolicy::class,
        DocumentMedical::class => DocumentMedicalPolicy::class,
        DossierMedical::class => DossierMedicalPolicy::class,
        Facture::class => FacturePolicy::class,
        Ordonnance::class => OrdonnancePolicy::class,
        Patient::class => PatientPolicy::class,
        RendezVous::class => RendezVousPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('parametres.view', fn ($user) => $user?->isAdmin() === true);
        Gate::define('parametres.edit', fn ($user) => $user?->isAdmin() === true);
        Gate::define('parametres.export', fn ($user) => $user?->isAdmin() === true);
    }
}
