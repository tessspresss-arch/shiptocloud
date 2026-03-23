<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{!! html_entity_decode($__env->yieldContent('title', 'MEDISYS Pro - Gestion medicale du cabinet'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') !!}</title>
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @php
        $layoutRouteName = strtolower((string) optional(request()->route())->getName());
        $layoutViteAssets = [
            'resources/css/app.css',
            'resources/css/sidebar.css',
            'resources/css/sidebar-enhanced.css',
            'resources/js/app.js',
            'resources/js/sidebar.js',
        ];

        if ($layoutRouteName === 'dashboard' || str_starts_with($layoutRouteName, 'dashboard.')) {
            $layoutViteAssets[] = 'resources/css/dashboard.css';
        }

        if ($layoutRouteName === 'agenda.index' || str_starts_with($layoutRouteName, 'agenda.')) {
            $layoutViteAssets[] = 'resources/css/agenda.css';
        }
    @endphp
    @vite($layoutViteAssets)

    <template id="medisys-page-styles-start"></template>
    @stack('styles')
    @vite('resources/css/typography.css')
    <template id="medisys-page-styles-end"></template>
</head>
<body class="{{ session('sidebar_collapsed', false) ? 'sidebar-collapsed' : 'sidebar-expanded' }}">
    <script>
        (function () {
            try {
                var storedTheme = localStorage.getItem('theme');
                var darkMode = localStorage.getItem('darkMode');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var dark = storedTheme ? storedTheme === 'dark' : (darkMode === 'true' || prefersDark);

                if (dark) {
                    document.documentElement.classList.add('dark');
                    document.body.classList.add('theme-dark', 'dark-mode');
                }
            } catch (error) {
                // Ignore client storage failures during initial paint.
            }
        })();
    </script>
    <script>
        (function () {
            function bindCsrfSync(root) {
                var forms = (root || document).querySelectorAll('form');

                forms.forEach(function (form) {
                    if (form.dataset.csrfSyncBound === 'true') {
                        return;
                    }

                    form.addEventListener('submit', function () {
                        var meta = document.querySelector('meta[name="csrf-token"]');
                        var token = meta ? meta.getAttribute('content') : '';
                        var method = (form.getAttribute('method') || 'GET').toUpperCase();
                        var spoofedMethodInput = form.querySelector('input[name="_method"]');
                        var spoofedMethod = spoofedMethodInput ? String(spoofedMethodInput.value || '').toUpperCase() : method;

                        if (method === 'GET' && spoofedMethod === 'GET') {
                            return;
                        }

                        var action = form.getAttribute('action') || window.location.href;
                        var actionUrl;

                        try {
                            actionUrl = new URL(action, window.location.origin);
                        } catch (error) {
                            return;
                        }

                        if (actionUrl.origin !== window.location.origin || !token) {
                            return;
                        }

                        var csrfInput = form.querySelector('input[name="_token"]');
                        if (!csrfInput) {
                            csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            form.appendChild(csrfInput);
                        }

                        csrfInput.value = token;
                    }, true);

                    form.dataset.csrfSyncBound = 'true';
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                bindCsrfSync(document);
            });

            document.addEventListener('medisys:page-loaded', function () {
                bindCsrfSync(document);
            });
        })();
    </script>
    <div class="app-nav-loader" id="appNavLoader" aria-hidden="true">
        <span class="app-nav-loader-bar"></span>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <button id="mobileMenuBtn" class="d-md-none position-fixed" style="top:16px;left:16px;z-index:1100;width:48px;height:48px;background:#ffffff;border:1px solid #d7e3f2;border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 24px -20px rgba(15,23,42,0.22);">
        <i class="fas fa-bars" style="font-size:1.25rem;color:#1f6fa3;"></i>
    </button>

    @include('partials.sidebar')

    <main class="main-content" id="mainContent">
        @php
            $authUser = auth()->user();
            $hideTopbarRaw = strtolower(trim($__env->yieldContent('hide_topbar')));
            $hideGlobalTopbar = in_array($hideTopbarRaw, ['1', 'true', 'yes', 'on'], true);
            $showGlobalTopbar = auth()->check() && !$hideGlobalTopbar;

            $routeName = strtolower((string) (request()->route() ? request()->route()->getName() : ''));
            $findByRoutePrefix = static function (string $currentRoute, array $map, string $fallback = ''): string {
                foreach ($map as $prefix => $label) {
                    if ($currentRoute === $prefix || str_starts_with($currentRoute, $prefix . '.')) {
                        return $label;
                    }
                }

                return $fallback;
            };

            $topbarTitleMap = [
                'dashboard' => __('messages.topbar.dashboard'),
                'patients' => __('messages.topbar.patients'),
                'consultations' => __('messages.topbar.consultations'),
                'planning' => __('messages.topbar.planning'),
                'agenda' => __('messages.topbar.agenda'),
                'rendezvous' => __('messages.topbar.rendezvous'),
                'medecins' => __('messages.topbar.medecins'),
                'medicaments' => __('messages.topbar.medicaments'),
                'ordonnances' => __('messages.topbar.ordonnances'),
                'factures' => __('messages.topbar.factures'),
                'depenses' => __('messages.topbar.depenses'),
                'contacts' => __('messages.topbar.contacts'),
                'sms' => 'Rappels SMS',
                'documents' => __('messages.topbar.documents'),
                'dossiers' => __('messages.topbar.dossiers'),
                'examens' => __('messages.topbar.examens'),
                'certificats' => __('messages.topbar.certificats'),
                'rapports' => __('messages.topbar.rapports'),
                'statistiques' => 'Statistiques',
                'parametres' => __('messages.topbar.parametres'),
                'utilisateurs' => 'Gestion des Utilisateurs',
                'archives' => 'Archives',
                'specialites' => __('messages.topbar.specialites'),
                'salles' => 'Salles et Equipements',
                'admin.settings' => __('messages.topbar.admin_settings'),
            ];

            $topbarTitle = trim($__env->yieldContent('title'));
            $topbarTitleLower = strtolower($topbarTitle);
            $hasLegacyTitle = str_contains($topbarTitleLower, 'scabinet')
                || str_contains($topbarTitleLower, 'cabinet medical');
            if ($topbarTitle === '' || $hasLegacyTitle) {
                $topbarTitle = $findByRoutePrefix(
                    $routeName,
                    $topbarTitleMap,
                    $routeName !== '' ? ucfirst(str_replace(['.', '-'], ' ', $routeName)) : 'Espace clinique'
                );
            }

            $topbarSubtitleMap = [
                'dashboard' => __('messages.topbar.dashboard_subtitle'),
                'patients' => __('messages.topbar.patients_subtitle'),
                'consultations' => __('messages.topbar.consultations_subtitle'),
                'planning' => __('messages.topbar.planning_subtitle'),
                'agenda' => "Coordination du flux patient en salle d'attente et en consultation.",
                'rendezvous' => __('messages.topbar.rendezvous_subtitle'),
                'medecins' => __('messages.topbar.medecins_subtitle'),
                'medicaments' => __('messages.topbar.medicaments_subtitle'),
                'ordonnances' => 'Prescription medicale securisee et suivi des traitements.',
                'factures' => 'Facturation medicale, paiements et tracabilite administrative.',
                'depenses' => 'Suivi budgetaire des charges et depenses du cabinet.',
                'contacts' => 'Relation patient, familles et partenaires de soins.',
                'sms' => __('messages.topbar.sms_subtitle'),
                'documents' => 'Archivage des documents medicaux et administratifs.',
                'dossiers' => 'Dossiers medicaux: antecedents, actes, examens et evolution.',
                'examens' => 'Suivi des examens, resultats biologiques et interpretations.',
                'certificats' => 'Edition et suivi des certificats medicaux.',
                'rapports' => 'Analyses, indicateurs et exports de performance clinique.',
                'statistiques' => 'Indicateurs de pilotage clinique et operationnel.',
                'parametres' => __('messages.topbar.parametres_subtitle'),
                'utilisateurs' => 'Gestion des comptes, roles et habilitations.',
                'archives' => 'Historique medical securise et conforme.',
                'specialites' => 'Referentiel des specialites et parcours de soins.',
                'salles' => 'Organisation des salles de soins et equipements.',
                'admin.settings' => 'Gouvernance, conformite et supervision du systeme.',
            ];

            $topbarSubtitle = trim($__env->yieldContent('topbar_subtitle'));
            if ($topbarSubtitle === '') {
                $topbarSubtitle = $findByRoutePrefix(
                    $routeName,
                    $topbarSubtitleMap,
                    __('messages.topbar.default_subtitle')
                );
            }

            $topbarSearchMap = [
                'patients' => 'Rechercher patient, CIN, dossier medical...',
                'consultations' => 'Rechercher consultation, patient, diagnostic...',
                'planning' => 'Rechercher praticien, plage horaire, disponibilite...',
                'agenda' => 'Rechercher patient, statut, motif de consultation...',
                'rendezvous' => 'Rechercher rendez-vous, patient, medecin...',
                'medecins' => 'Rechercher medecin, specialite, contact...',
                'medicaments' => 'Rechercher medicament, DCI, classe therapeutique...',
                'ordonnances' => 'Rechercher ordonnance, patient, prescripteur...',
                'factures' => 'Rechercher facture, patient, statut, montant...',
                'depenses' => 'Rechercher depense, categorie, fournisseur...',
                'contacts' => 'Rechercher contact, patient, telephone...',
                'sms' => 'Rechercher rappel SMS, patient, telephone...',
                'documents' => 'Rechercher document, categorie, dossier...',
                'dossiers' => 'Rechercher dossier, patient, reference clinique...',
                'examens' => 'Rechercher examen, patient, resultat...',
                'certificats' => 'Rechercher certificat, patient, type...',
                'rapports' => 'Rechercher rapport, periode, format...',
                'statistiques' => 'Rechercher indicateur, periode, module...',
                'parametres' => 'Rechercher option systeme, securite, configuration...',
                'utilisateurs' => 'Rechercher utilisateur, role, statut...',
                'archives' => 'Rechercher archive, patient, dossier...',
            ];
            $topbarSearchPlaceholder = trim($__env->yieldContent('topbar_search_placeholder'));
            if ($topbarSearchPlaceholder === '') {
                $topbarSearchPlaceholder = $findByRoutePrefix(
                    $routeName,
                    $topbarSearchMap,
                    'Rechercher patient, dossier, rendez-vous...'
                );
            }

            $topbarBadgeLabel = trim($__env->yieldContent('topbar_badge'));

            $topbarIcon = trim($__env->yieldContent('topbar_icon'));
            if ($topbarIcon === '') {
                $topbarIcon = 'fa-heart-pulse';
                $topbarIconMap = [
                    'dashboard' => 'fa-heart-pulse',
                    'patients' => 'fa-users',
                    'consultations' => 'fa-stethoscope',
                    'planning' => 'fa-calendar-days',
                    'rendezvous' => 'fa-calendar-check',
                    'medecins' => 'fa-user-doctor',
                    'medicaments' => 'fa-pills',
                    'ordonnances' => 'fa-prescription',
                    'factures' => 'fa-file-invoice-dollar',
                    'depenses' => 'fa-wallet',
                    'contacts' => 'fa-address-book',
                    'sms' => 'fa-comment-sms',
                    'documents' => 'fa-folder-open',
                    'salles' => 'fa-hospital',
                    'dossiers' => 'fa-notes-medical',
                    'rapports' => 'fa-chart-line',
                    'statistiques' => 'fa-chart-column',
                    'parametres' => 'fa-gears',
                    'agenda' => 'fa-calendar-week',
                    'archives' => 'fa-box-archive',
                    'utilisateurs' => 'fa-user-shield',
                ];
                foreach ($topbarIconMap as $prefix => $icon) {
                    if ($routeName !== '' && str_starts_with($routeName, $prefix . '.')) {
                        $topbarIcon = $icon;
                        break;
                    }
                }
            }

            $authName = $authUser->name ?? '';
            $roleValue = $authUser->role ?? 'utilisateur';
            $topbarRole = ucfirst(str_replace('_', ' ', (string) $roleValue));
            $parts = preg_split('/\s+/', trim($authName)) ?: [];
            $initials = '';
            foreach (array_slice(array_filter($parts), 0, 2) as $part) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
            if ($initials === '') {
                $initials = 'U';
            }

            $topbarAvatarUrl = null;
            if (!empty($authUser?->avatar)) {
                $avatarValue = (string) $authUser->avatar;
                if (str_starts_with($avatarValue, 'http://') || str_starts_with($avatarValue, 'https://') || str_starts_with($avatarValue, 'data:')) {
                    $topbarAvatarUrl = $avatarValue;
                } elseif (str_starts_with($avatarValue, 'storage/')) {
                    $topbarAvatarUrl = asset($avatarValue);
                } else {
                    $topbarAvatarUrl = asset('storage/' . ltrim($avatarValue, '/'));
                }
            }

            $profileRoute = Route::has('profile.show') ? route('profile.show') : route('profile.2fa.show');
            $canAccessSettings = (bool) ($authUser?->isAdmin() && Route::has('parametres.index'));
            $settingsRoute = $canAccessSettings ? route('parametres.index') : '#';
            $settingsIcon = 'fa-cog';
        @endphp

        <x-page-header
            :visible="$showGlobalTopbar"
            :title="$topbarTitle"
            :subtitle="$topbarSubtitle"
            :badge-label="$topbarBadgeLabel"
            :search-placeholder="$topbarSearchPlaceholder"
            search-aria-label="Recherche dans le module"
            :icon="$topbarIcon"
            :auth-user="$authUser"
            :avatar-url="$topbarAvatarUrl"
            :initials="$initials"
            :role-label="$topbarRole"
            :profile-route="$profileRoute"
            :settings-route="$settingsRoute"
            :settings-icon="$settingsIcon"
            :show-settings="$canAccessSettings"
        />

        @yield('content')
    </main>
    <template id="medisys-page-scripts-start"></template>
    @stack('scripts')
    <template id="medisys-page-scripts-end"></template>
</body>
</html>


