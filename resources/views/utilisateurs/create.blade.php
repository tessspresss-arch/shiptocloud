@extends('layouts.app')

@section('title', 'Nouvel utilisateur')

@section('content')
@php
    $roleMap = ['admin' => 'Admin', 'medecin' => 'Médecin', 'secretaire' => 'Secrétaire'];
    $moduleGroups = [
        'Cœur médical' => ['dashboard', 'patients', 'consultations', 'planning', 'medecins'],
        'Opérations' => ['pharmacie', 'facturation', 'examens', 'depenses'],
        'Communication' => ['contacts', 'sms', 'documents'],
        'Pilotage' => ['statistiques', 'rapports'],
    ];
    $managedModulesById = collect($managedModules ?? [])->keyBy('id');
    $selectedModuleIds = old('module_permissions', []);
    $initialLastName = old('name', '');
    $initialFirstName = old('first_name', '');
@endphp

<style>
    .user-create-page{padding:14px 16px 24px;background:linear-gradient(180deg,#f3f8ff 0%,#f8fbff 100%);border:1px solid #dfeaf7;border-radius:18px;box-shadow:0 20px 30px -32px rgba(16,57,104,.9)}
    .uc-head{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:14px;align-items:start;margin-bottom:16px}
    .uc-card,.uc-panel,.uc-summary{background:#fff;border:1px solid #d9e6f5;border-radius:16px;box-shadow:0 18px 26px -30px rgba(16,42,78,.8)}
    .uc-card{padding:16px 18px}
    .uc-title-wrap{display:flex;gap:14px;align-items:flex-start}
    .uc-icon{width:50px;height:50px;border-radius:15px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(180deg,#edf4ff 0%,#deedff 100%);color:#2f6fe0;border:1px solid #bfd5ee;font-size:1.2rem}
    .uc-tag{display:inline-flex;align-items:center;border-radius:999px;padding:4px 10px;background:linear-gradient(135deg,#4f8ef7 0%,#2f6fe0 100%);color:#fff;font-size:.73rem;font-weight:900;letter-spacing:.04em;text-transform:uppercase;margin-bottom:7px}
    .uc-title{margin:0;color:#1d4586;font-size:clamp(1.5rem,2.2vw,1.95rem);font-weight:900;line-height:1.08}
    .uc-subtitle{margin:6px 0 0;color:#5f7898;font-size:.97rem}
    .uc-toolbar{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:10px}
    .uc-btn,.uc-toolbar form{display:inline-flex}
    .uc-btn{min-height:42px;border-radius:11px;border:1px solid #cedef0;background:#f6faff;color:#355274;font-weight:800;padding:.58rem .95rem;align-items:center;gap:.5rem;text-decoration:none}
    .uc-btn:hover{color:#173657;background:#eef5fd}
    .uc-btn-primary{background:linear-gradient(135deg,#1d6fdc 0%,#4288ee 100%);border-color:#1c6fd8;color:#fff}
    .uc-btn-primary:hover{color:#fff;background:linear-gradient(135deg,#165fbe 0%,#3c7fdc 100%)}
    .uc-btn-secondary{background:#fff;border-color:#cedef0}
    .uc-alert{border-radius:14px;margin-bottom:14px}
    .uc-layout{display:grid;grid-template-columns:320px minmax(0,1fr);gap:16px}
    .uc-summary-hero{padding:18px;background:linear-gradient(145deg,#0e66b2 0%,#1b7bcf 55%,#39a0f3 100%);color:#fff;border-radius:16px 16px 0 0}
    .uc-avatar{width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,#1d6fdc,#4d91f0);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:1.45rem;font-weight:900;overflow:hidden;box-shadow:0 16px 24px -18px rgba(8,31,66,.8);border:3px solid rgba(255,255,255,.28)}
    .uc-avatar img,.uc-avatar-preview img{width:100%;height:100%;object-fit:cover}
    .uc-summary-name{margin:14px 0 4px;font-size:1.16rem;font-weight:900}
    .uc-summary-email{margin:0;color:rgba(240,247,255,.88);word-break:break-word}
    .uc-pill-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}
    .uc-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:8px 10px;font-size:.74rem;font-weight:900;line-height:1;background:rgba(255,255,255,.16);color:#fff}
    .uc-block{padding:16px 18px;border-top:1px solid #e1ebf5}.uc-block:first-child{border-top:0}
    .uc-block h3,.uc-section-head h3{margin:0;color:#14365e;font-size:1rem;font-weight:900}
    .uc-meta{display:grid;gap:10px;margin-top:12px}.uc-meta-item{display:grid;gap:4px}.uc-meta-label{color:#6f86a4;font-size:.75rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase}.uc-meta-value{color:#173657;font-weight:700}
    .uc-main{display:grid;gap:14px}
    .uc-section-head{padding:14px 16px;border-bottom:1px solid #d9e6f5;background:linear-gradient(180deg,#f6faff 0%,#eef6ff 100%);border-radius:16px 16px 0 0}
    .uc-section-head p{margin:4px 0 0;color:#6d84a2;font-size:.9rem}
    .uc-section-body{padding:16px}
    .uc-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .uc-form-grid-compact{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:14px}
    .uc-span-2{grid-column:span 2}.uc-span-3{grid-column:span 3}.uc-span-5{grid-column:span 5}
    .user-create-page .form-label{color:#375273;font-size:.78rem;letter-spacing:.03em;text-transform:uppercase;font-weight:800;margin-bottom:.35rem}
    .user-create-page .form-control,.user-create-page .form-select{border-radius:10px;border-color:#bfd1e7;min-height:42px;color:#1f3656;background:#fff}
    .user-create-page .form-control:focus,.user-create-page .form-select:focus{border-color:#5da6e4;box-shadow:0 0 0 3px rgba(11,122,199,.12)}
    .user-create-page .form-check-input{border-color:#aac6e3}.user-create-page .form-check-input:checked{background-color:#1d6fdc;border-color:#1d6fdc}
    .uc-helper{margin-top:6px;color:#6f86a4;font-size:.84rem}
    .uc-avatar-upload{display:grid;grid-template-columns:108px minmax(0,1fr);gap:14px;align-items:start}
    .uc-avatar-preview{width:108px;height:108px;border-radius:22px;border:1px dashed #b7d0ea;background:linear-gradient(180deg,#f6faff 0%,#eef5ff 100%);display:flex;align-items:center;justify-content:center;overflow:hidden;color:#2f6fe0;font-size:1.45rem;font-weight:900}
    .uc-inline-actions,.uc-actions-bottom,.uc-profile-presets{display:flex;flex-wrap:wrap;gap:10px}
    .uc-password-tools{display:flex;gap:10px;flex-wrap:wrap}
    .uc-password-strength{display:flex;align-items:center;gap:10px;margin-top:8px}.uc-strength-bar{flex:1;height:8px;border-radius:999px;background:#e7edf5;overflow:hidden}.uc-strength-fill{height:100%;width:0;background:#d9534f;transition:.2s ease}.uc-strength-label{font-size:.84rem;font-weight:800;color:#6f86a4}
    .uc-profile-chip{display:inline-flex;align-items:center;gap:8px;border:1px solid #d9e7f5;border-radius:12px;background:#fbfdff;padding:10px 12px;color:#18385e;font-weight:800;cursor:pointer}
    .uc-profile-chip.active{border-color:#69a8e9;background:#eef6ff;box-shadow:0 0 0 3px rgba(29,111,220,.08)}
    .uc-module-layout{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .uc-module-group{border:1px solid #d9e7f5;border-radius:14px;background:#fbfdff;padding:14px}
    .uc-module-group h4{margin:0 0 10px;color:#18385e;font-size:.95rem;font-weight:900}
    .uc-module-list{display:grid;gap:10px}
    .uc-module-option{display:flex;align-items:flex-start;gap:10px;padding:10px 11px;border-radius:12px;border:1px solid #e3edf8;background:#fff}
    .uc-module-option.disabled{opacity:.6}
    .uc-module-text strong{display:block;color:#18385e;font-size:.92rem}
    .uc-module-text span{display:block;color:#7088a8;font-size:.82rem;margin-top:2px}
    .uc-toggle-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
    .uc-toggle-card{border:1px solid #d9e7f5;border-radius:14px;background:#fbfdff;padding:14px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
    .uc-toggle-card p{margin:5px 0 0;color:#6f86a4;font-size:.85rem}
    .uc-details{border:1px solid #d9e6f5;border-radius:16px;background:#fff}
    .uc-details summary{list-style:none;cursor:pointer;padding:16px 18px;font-weight:900;color:#14365e;display:flex;align-items:center;justify-content:space-between;gap:12px}
    .uc-details summary::-webkit-details-marker{display:none}
    .uc-details[open] summary{border-bottom:1px solid #d9e6f5;background:linear-gradient(180deg,#f6faff 0%,#eef6ff 100%)}
    @media (max-width:1199px){.uc-layout{grid-template-columns:1fr}}
    @media (max-width:991px){.uc-head{grid-template-columns:1fr}.uc-toolbar{justify-content:flex-start}.uc-form-grid,.uc-form-grid-compact,.uc-toggle-grid,.uc-module-layout,.uc-avatar-upload{grid-template-columns:1fr}.uc-span-2,.uc-span-3,.uc-span-5{grid-column:auto}}
    @media (max-width:576px){.user-create-page{padding:10px 10px 18px;border-radius:14px}.uc-card,.uc-section-head,.uc-section-body,.uc-summary-hero,.uc-block,.uc-details summary{padding-left:14px;padding-right:14px}.uc-toolbar,.uc-inline-actions,.uc-actions-bottom,.uc-password-tools,.uc-profile-presets{flex-direction:column}.uc-btn,.uc-toolbar form,.uc-toolbar .uc-btn,.uc-profile-chip{width:100%;justify-content:center}}
</style>

<div class="user-create-page">
    <div class="uc-head">
        <div class="uc-card">
            <div class="uc-title-wrap">
                <div class="uc-icon"><i class="fas fa-user-plus"></i></div>
                <div>
                    <span class="uc-tag">Gestion des accès</span>
                    <h1 class="uc-title">Nouvel utilisateur</h1>
                    <p class="uc-subtitle">Créez un compte rapidement avec les champs essentiels, puis ouvrez les options avancées si nécessaire.</p>
                </div>
            </div>
        </div>

        <div class="uc-toolbar">
            <button type="submit" form="userCreateForm" class="uc-btn uc-btn-primary"><i class="fas fa-user-plus"></i><span>Créer l'utilisateur</span></button>
            <a href="{{ route('utilisateurs.index') }}" class="uc-btn uc-btn-secondary"><i class="fas fa-arrow-left"></i><span>Retour</span></a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger uc-alert">
            <strong>Des corrections sont nécessaires.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="uc-layout">
        <aside class="uc-summary">
            <div class="uc-summary-hero">
                <div class="uc-avatar" id="createSidebarAvatar" data-default-initials="NU"><span>NU</span></div>
                <div class="uc-summary-name" id="createSummaryName">{{ trim($initialLastName . ' ' . $initialFirstName) !== '' ? trim($initialLastName . ' ' . $initialFirstName) : 'Nouvel utilisateur' }}</div>
                <p class="uc-summary-email" id="createSummaryEmail">{{ old('email', 'email@cabinet.test') }}</p>
                <div class="uc-pill-row">
                    <span class="uc-pill"><i class="fas fa-user-tag"></i><span id="createSummaryRole">{{ $roleMap[old('role', 'secretaire')] ?? 'Secrétaire' }}</span></span>
                    <span class="uc-pill"><i class="fas fa-shield-alt"></i><span id="createSummaryStatus">{{ $accountStatusOptions[old('account_status', 'actif')] ?? 'Actif' }}</span></span>
                </div>
            </div>

            <div class="uc-block">
                <h3>Création rapide</h3>
                <div class="uc-meta">
                    <div class="uc-meta-item"><span class="uc-meta-label">Obligatoire</span><span class="uc-meta-value">Nom, prénom, email, rôle et mot de passe.</span></div>
                    <div class="uc-meta-item"><span class="uc-meta-label">Avancé</span><span class="uc-meta-value">Modules, sécurité, profil professionnel et préférences.</span></div>
                    <div class="uc-meta-item"><span class="uc-meta-label">Mot de passe</span><span class="uc-meta-value">Génération sécurisée et envoi par email disponibles.</span></div>
                </div>
            </div>

            <div class="uc-block">
                <h3>Profils rapides</h3>
                <div class="uc-profile-presets mt-3" id="profilePresets">
                    <button type="button" class="uc-profile-chip" data-role-profile="admin"><i class="fas fa-user-cog"></i><span>Admin</span></button>
                    <button type="button" class="uc-profile-chip" data-role-profile="medecin"><i class="fas fa-user-md"></i><span>Médecin</span></button>
                    <button type="button" class="uc-profile-chip" data-role-profile="secretaire"><i class="fas fa-user-tie"></i><span>Secrétaire</span></button>
                </div>
            </div>
        </aside>

        <div class="uc-main">
            <form action="{{ route('utilisateurs.store') }}" method="POST" enctype="multipart/form-data" id="userCreateForm">
                @csrf

                <section class="uc-panel">
                    <div class="uc-section-head">
                        <h3>Informations du compte</h3>
                        <p>Parcours principal pour créer un utilisateur en quelques secondes.</p>
                    </div>
                    <div class="uc-section-body">
                        <div class="uc-form-grid-compact">
                            <div class="uc-span-2">
                                <label class="form-label" for="name">Nom</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="uc-span-2">
                                <label class="form-label" for="first_name">Prénom</label>
                                <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="uc-span-2">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div>
                                <label class="form-label" for="role">Rôle</label>
                                <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    @foreach($roleMap as $value => $label)
                                        <option value="{{ $value }}" @selected(old('role', 'secretaire') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="uc-span-2">
                                <label class="form-label" for="password">Mot de passe</label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="uc-password-strength">
                                    <div class="uc-strength-bar"><div class="uc-strength-fill" id="passwordStrengthFill"></div></div>
                                    <span class="uc-strength-label" id="passwordStrengthLabel">À évaluer</span>
                                </div>
                            </div>

                            <div class="uc-span-2">
                                <label class="form-label" for="password_confirmation">Confirmation mot de passe</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password">
                                <div class="uc-helper" id="passwordMatchHint">Les mots de passe doivent correspondre.</div>
                            </div>

                            <div class="uc-span-5">
                                <div class="uc-password-tools">
                                    <button type="button" class="uc-btn uc-btn-secondary" id="generatePasswordBtn"><i class="fas fa-wand-magic-sparkles"></i><span>Générer un mot de passe sécurisé</span></button>
                                    <label class="uc-btn uc-btn-secondary mb-0">
                                        <input type="checkbox" class="form-check-input me-2" name="send_password_email" value="1" {{ old('send_password_email') ? 'checked' : '' }}>
                                        <span>Envoyer le mot de passe par email</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <details class="uc-details" {{ $errors->has('professional_phone') || $errors->has('job_title') || $errors->has('department') || $errors->has('speciality') || $errors->has('order_number') || $errors->has('account_status') || $errors->has('avatar') || $errors->has('module_permissions') || $errors->has('module_permissions.*') ? 'open' : '' }}>
                    <summary>
                        <span>Options avancées</span>
                        <i class="fas fa-chevron-down"></i>
                    </summary>
                    <div class="uc-main">
                        <section class="uc-panel">
                            <div class="uc-section-head">
                                <h3>Accès et permissions</h3>
                                <p>Choisissez les modules autorisés, le statut du compte et les options de sécurité.</p>
                            </div>
                            <div class="uc-section-body">
                                <div class="uc-form-grid mb-3">
                                    <div>
                                        <label class="form-label" for="account_status">Statut du compte</label>
                                        <select id="account_status" name="account_status" class="form-select @error('account_status') is-invalid @enderror" required>
                                            @foreach($accountStatusOptions as $value => $label)
                                                <option value="{{ $value }}" @selected(old('account_status', 'actif') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('account_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="uc-span-2">
                                        <label class="form-label">Profils de modules</label>
                                        <div class="uc-helper mb-3">Appliquez un profil par rôle ou cochez manuellement les modules nécessaires.</div>
                                        <div class="uc-profile-presets" id="inlineProfilePresets">
                                            <button type="button" class="uc-profile-chip" data-role-profile="admin"><i class="fas fa-user-cog"></i><span>Admin</span></button>
                                            <button type="button" class="uc-profile-chip" data-role-profile="medecin"><i class="fas fa-user-md"></i><span>Médecin</span></button>
                                            <button type="button" class="uc-profile-chip" data-role-profile="secretaire"><i class="fas fa-user-tie"></i><span>Secrétaire</span></button>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Modules autorisés</label>
                                    <div class="uc-module-layout">
                                        @foreach($moduleGroups as $groupTitle => $moduleIds)
                                            <div class="uc-module-group">
                                                <h4>{{ $groupTitle }}</h4>
                                                <div class="uc-module-list">
                                                    @foreach($moduleIds as $moduleId)
                                                        @php
                                                            $module = $managedModulesById->get($moduleId);
                                                            $label = $module['label'] ?? ucfirst($moduleId);
                                                        @endphp
                                                        <label class="uc-module-option" data-module-card>
                                                            <input type="checkbox" class="form-check-input mt-1" name="module_permissions[]" value="{{ $moduleId }}" data-module-option @checked(in_array($moduleId, $selectedModuleIds, true))>
                                                            <span class="uc-module-text">
                                                                <strong>{{ $label }}</strong>
                                                                <span>Accès rapide au module {{ strtolower($label) }}.</span>
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('module_permissions')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                                    @error('module_permissions.*')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                                </div>

                                <div class="uc-toggle-grid mt-3">
                                    <div class="uc-toggle-card">
                                        <div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="two_factor_enabled" name="two_factor_enabled" value="1" @checked(old('two_factor_enabled'))>
                                                <label class="form-check-label" for="two_factor_enabled">2FA active</label>
                                            </div>
                                            <p>Prépare le compte pour une activation de double authentification.</p>
                                        </div>
                                        <i class="fas fa-shield-alt text-primary mt-1"></i>
                                    </div>

                                    <div class="uc-toggle-card">
                                        <div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="force_password_change" name="force_password_change" value="1" @checked(old('force_password_change'))>
                                                <label class="form-check-label" for="force_password_change">Changement de mot de passe à la première connexion</label>
                                            </div>
                                            <p>Oblige l'utilisateur à définir un mot de passe personnel à sa première connexion.</p>
                                        </div>
                                        <i class="fas fa-key text-warning mt-1"></i>
                                    </div>

                                    <div class="uc-toggle-card">
                                        <div>
                                            <label class="form-label mb-2" for="account_expires_at">Expiration du compte</label>
                                            <input type="date" id="account_expires_at" name="account_expires_at" class="form-control @error('account_expires_at') is-invalid @enderror" value="{{ old('account_expires_at') }}">
                                            @error('account_expires_at')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </div>
                                        <i class="fas fa-calendar-alt text-info mt-1"></i>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="uc-panel">
                            <div class="uc-section-head">
                                <h3>Informations professionnelles</h3>
                                <p>Renseignez le poste, le service et les identifiants médicaux si besoin.</p>
                            </div>
                            <div class="uc-section-body">
                                <div class="uc-form-grid">
                                    <div>
                                        <label class="form-label" for="professional_phone">Téléphone professionnel</label>
                                        <input type="text" id="professional_phone" name="professional_phone" class="form-control @error('professional_phone') is-invalid @enderror" value="{{ old('professional_phone') }}">
                                        @error('professional_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div>
                                        <label class="form-label" for="job_title">Fonction</label>
                                        <select id="job_title" name="job_title" class="form-select @error('job_title') is-invalid @enderror">
                                            <option value="">Sélectionner</option>
                                            @foreach($jobTitleOptions as $value => $label)
                                                <option value="{{ $value }}" @selected(old('job_title') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="form-label" for="department">Service</label>
                                        <select id="department" name="department" class="form-select @error('department') is-invalid @enderror">
                                            <option value="">Sélectionner</option>
                                            @foreach($departmentOptions as $value => $label)
                                                <option value="{{ $value }}" @selected(old('department') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="form-label" for="speciality">Spécialité</label>
                                        <input type="text" id="speciality" name="speciality" class="form-control @error('speciality') is-invalid @enderror" value="{{ old('speciality') }}">
                                    </div>

                                    <div>
                                        <label class="form-label" for="order_number">Numéro d'ordre</label>
                                        <input type="text" id="order_number" name="order_number" class="form-control @error('order_number') is-invalid @enderror" value="{{ old('order_number') }}">
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="uc-panel">
                            <div class="uc-section-head">
                                <h3>Préférences utilisateur</h3>
                                <p>Avatar, langue, fuseau horaire et notifications du compte.</p>
                            </div>
                            <div class="uc-section-body">
                                <div class="uc-form-grid">
                                    <div class="uc-span-2">
                                        <label class="form-label" for="avatar">Avatar utilisateur</label>
                                        <div class="uc-avatar-upload">
                                            <div class="uc-avatar-preview" id="formAvatarPreview" data-default-initials="NU"><span>NU</span></div>
                                            <div>
                                                <input class="form-control @error('avatar') is-invalid @enderror" type="file" id="avatar" name="avatar" accept=".jpg,.jpeg,.png,.webp">
                                                @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                <div class="uc-helper">Formats acceptés : JPG, PNG, WEBP. Taille maximale 2 Mo. Un aperçu est affiché avant enregistrement.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label" for="ui_language">Langue</label>
                                        <select id="ui_language" name="ui_language" class="form-select @error('ui_language') is-invalid @enderror" required>
                                            @foreach($languageOptions as $value => $label)
                                                <option value="{{ $value }}" @selected(old('ui_language', 'fr') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="form-label" for="timezone">Fuseau horaire</label>
                                        <select id="timezone" name="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                                            @foreach($timezoneOptions as $value => $label)
                                                <option value="{{ $value }}" @selected(old('timezone', 'Africa/Casablanca') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="uc-span-2">
                                        <label class="form-label" for="notification_channel">Notifications</label>
                                        <select id="notification_channel" name="notification_channel" class="form-select @error('notification_channel') is-invalid @enderror" required>
                                            @foreach($notificationChannelOptions as $value => $label)
                                                <option value="{{ $value }}" @selected(old('notification_channel', 'email') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </details>

                <div class="uc-actions-bottom mt-3">
                    <a href="{{ route('utilisateurs.index') }}" class="uc-btn uc-btn-secondary"><i class="fas fa-times"></i><span>Annuler</span></a>
                    <button type="submit" class="uc-btn uc-btn-primary"><i class="fas fa-user-plus"></i><span>Créer l'utilisateur</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('name');
    const firstNameInput = document.getElementById('first_name');
    const emailInput = document.getElementById('email');
    const roleSelect = document.getElementById('role');
    const statusSelect = document.getElementById('account_status');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const strengthFill = document.getElementById('passwordStrengthFill');
    const strengthLabel = document.getElementById('passwordStrengthLabel');
    const passwordMatchHint = document.getElementById('passwordMatchHint');
    const generatePasswordBtn = document.getElementById('generatePasswordBtn');
    const avatarInput = document.getElementById('avatar');
    const sidebarAvatar = document.getElementById('createSidebarAvatar');
    const formAvatar = document.getElementById('formAvatarPreview');
    const summaryName = document.getElementById('createSummaryName');
    const summaryEmail = document.getElementById('createSummaryEmail');
    const summaryRole = document.getElementById('createSummaryRole');
    const summaryStatus = document.getElementById('createSummaryStatus');
    const profileButtons = Array.from(document.querySelectorAll('[data-role-profile]'));
    const moduleOptions = Array.from(document.querySelectorAll('[data-module-option]'));
    const moduleCards = Array.from(document.querySelectorAll('[data-module-card]'));

    const moduleProfiles = {
        admin: [],
        medecin: ['dashboard', 'patients', 'consultations', 'planning', 'documents', 'statistiques'],
        secretaire: ['dashboard', 'patients', 'planning', 'contacts', 'sms', 'documents']
    };

    const roleLabels = {
        admin: 'Admin',
        medecin: 'Médecin',
        secretaire: 'Secrétaire'
    };

    const statusLabels = {
        actif: 'Actif',
        desactive: 'Désactivé',
        en_attente: 'En attente'
    };

    function initialsFromName(value) {
        const source = (value || '').trim();
        if (!source) return 'NU';
        return source.split(/\s+/).slice(0, 2).map(function (part) {
            return part.charAt(0).toUpperCase();
        }).join('');
    }

    function setAvatarPreview(container, src, initials) {
        if (!container) return;
        container.innerHTML = src ? '<img src="' + src + '" alt="Avatar">' : '<span>' + initials + '</span>';
    }

    function updateSummary() {
        const lastName = nameInput && nameInput.value.trim() ? nameInput.value.trim() : '';
        const firstName = firstNameInput && firstNameInput.value.trim() ? firstNameInput.value.trim() : '';
        const name = (lastName + ' ' + firstName).trim() || 'Nouvel utilisateur';
        const email = emailInput && emailInput.value.trim() ? emailInput.value.trim() : 'email@cabinet.test';
        const initials = initialsFromName((nameInput ? nameInput.value : '') + ' ' + (firstNameInput ? firstNameInput.value : ''));
        if (summaryName) summaryName.textContent = name;
        if (summaryEmail) summaryEmail.textContent = email;
        if (summaryRole && roleSelect) summaryRole.textContent = roleLabels[roleSelect.value] || 'Secretaire';
        if (summaryStatus && statusSelect) summaryStatus.textContent = statusLabels[statusSelect.value] || 'Actif';
        if (sidebarAvatar && !sidebarAvatar.querySelector('img')) setAvatarPreview(sidebarAvatar, '', initials);
        if (formAvatar && !formAvatar.querySelector('img')) setAvatarPreview(formAvatar, '', initials);
    }

    function applyRoleProfile(role, markActive) {
        const isAdmin = role === 'admin';
        const allowed = moduleProfiles[role] || [];

        moduleOptions.forEach(function (input) {
            if (isAdmin) {
                input.checked = false;
                input.disabled = true;
            } else {
                input.disabled = false;
                input.checked = allowed.includes(input.value);
            }
        });

        moduleCards.forEach(function (card) {
            card.classList.toggle('disabled', isAdmin);
        });

        if (markActive) {
            profileButtons.forEach(function (button) {
                button.classList.toggle('active', button.dataset.roleProfile === role);
            });
        }
    }

    function syncRoleStateOnly(role) {
        const isAdmin = role === 'admin';
        moduleOptions.forEach(function (input) {
            if (isAdmin) {
                input.checked = false;
                input.disabled = true;
            } else {
                input.disabled = false;
            }
        });

        moduleCards.forEach(function (card) {
            card.classList.toggle('disabled', isAdmin);
        });

        profileButtons.forEach(function (button) {
            button.classList.toggle('active', button.dataset.roleProfile === role);
        });
    }

    function computeStrength(password) {
        let score = 0;
        if (password.length >= 12) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[a-z]/.test(password)) score += 1;
        if (/\d/.test(password)) score += 1;
        if (/[^A-Za-z0-9]/.test(password)) score += 1;
        return score;
    }

    function updatePasswordStrength() {
        if (!passwordInput || !strengthFill || !strengthLabel) return;
        const score = computeStrength(passwordInput.value);
        const labels = ['Tres faible', 'Faible', 'Correct', 'Bon', 'Fort', 'Excellent'];
        const colors = ['#d9534f', '#ea7a3c', '#f0ad4e', '#1f8dd6', '#18a65f', '#0f8f4b'];
        const width = Math.max(score, 0) * 20;
        strengthFill.style.width = width + '%';
        strengthFill.style.background = colors[score] || colors[0];
        strengthLabel.textContent = passwordInput.value ? labels[score] : 'A evaluer';
    }

    function updatePasswordMatch() {
        if (!passwordInput || !passwordConfirmInput || !passwordMatchHint) return;
        if (!passwordConfirmInput.value) {
            passwordMatchHint.textContent = 'Les mots de passe doivent correspondre.';
            passwordMatchHint.style.color = '#6f86a4';
            return;
        }
        const matches = passwordInput.value === passwordConfirmInput.value;
        passwordMatchHint.textContent = matches ? 'Les mots de passe correspondent.' : 'La confirmation ne correspond pas.';
        passwordMatchHint.style.color = matches ? '#117a44' : '#b42323';
    }

    function generatePassword() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*_-+=';
        let generated = '';
        for (let i = 0; i < 18; i += 1) {
            generated += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        if (passwordInput) passwordInput.value = generated;
        if (passwordConfirmInput) passwordConfirmInput.value = generated;
        updatePasswordStrength();
        updatePasswordMatch();
    }

    if (nameInput) nameInput.addEventListener('input', updateSummary);
    if (firstNameInput) firstNameInput.addEventListener('input', updateSummary);
    if (emailInput) emailInput.addEventListener('input', updateSummary);
    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            applyRoleProfile(roleSelect.value, true);
            updateSummary();
        });
    }
    if (statusSelect) statusSelect.addEventListener('change', updateSummary);
    if (passwordInput) passwordInput.addEventListener('input', function () { updatePasswordStrength(); updatePasswordMatch(); });
    if (passwordConfirmInput) passwordConfirmInput.addEventListener('input', updatePasswordMatch);
    if (generatePasswordBtn) generatePasswordBtn.addEventListener('click', generatePassword);

    profileButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const role = button.dataset.roleProfile || 'secretaire';
            if (roleSelect) roleSelect.value = role;
            applyRoleProfile(role, true);
            updateSummary();
        });
    });

    if (avatarInput) {
        avatarInput.addEventListener('change', function () {
            if (!avatarInput.files || !avatarInput.files[0]) {
                updateSummary();
                return;
            }

            const reader = new FileReader();
            const initials = initialsFromName((nameInput ? nameInput.value : '') + ' ' + (firstNameInput ? firstNameInput.value : ''));
            reader.onload = function (event) {
                const src = event.target && event.target.result ? event.target.result : '';
                setAvatarPreview(sidebarAvatar, src, initials);
                setAvatarPreview(formAvatar, src, initials);
            };
            reader.readAsDataURL(avatarInput.files[0]);
        });
    }

    updateSummary();
    updatePasswordStrength();
    updatePasswordMatch();
    syncRoleStateOnly(roleSelect ? roleSelect.value : 'secretaire');
});
</script>
@endpush
