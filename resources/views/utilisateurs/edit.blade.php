@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur')

@section('content')
@php
    $avatarUrl = $user->avatar_url;
    $nameParts = preg_split('/\s+/', trim((string) $user->name), 2) ?: [];
    $lastNameValue = old('name', $nameParts[0] ?? '');
    $firstNameValue = old('first_name', $nameParts[1] ?? '');
    $roleMap = ['admin' => 'Admin', 'medecin' => 'Médecin', 'secretaire' => 'Secrétaire'];
    $roleClass = match($user->role) {
        'admin' => 'ue-role-admin',
        'medecin' => 'ue-role-medecin',
        default => 'ue-role-secretaire',
    };
    $statusClass = 'ue-status-' . $user->account_status_key;
    $selectedModuleIds = old('module_permissions', $selectedModules ?? []);
    $managedModulesById = collect($managedModules ?? [])->keyBy('id');
    $moduleGroups = [
        'Coeur médical' => ['dashboard', 'patients', 'consultations', 'planning', 'medecins'],
        'Opérations' => ['pharmacie', 'facturation', 'examens', 'depenses'],
        'Communication' => ['contacts', 'sms', 'documents'],
        'Pilotage' => ['statistiques', 'rapports'],
    ];
    $lastLoginText = $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais connecté';
    $lastActivityText = $user->last_activity_at ? $user->last_activity_at->format('d/m/Y H:i') : 'Aucune activité récente';
    $toggleTargetStatus = $user->account_status_key === 'actif' ? 'desactive' : 'actif';
@endphp

<style>
    .user-edit-page{padding:18px 20px 28px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:22px;box-shadow:0 20px 40px -36px rgba(15,23,42,.45)}
    .ue-head{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:16px;align-items:center;margin-bottom:18px}
    .ue-card,.ue-panel{background:#fff;border:1px solid #e2e8f0;border-radius:18px;box-shadow:0 14px 28px -34px rgba(15,23,42,.4)}
    .ue-card{padding:20px 22px}
    .ue-title-wrap{display:flex;gap:14px;align-items:flex-start}
    .ue-icon{width:52px;height:52px;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;background:#eff6ff;color:#2563eb;border:1px solid #cfe0fb;font-size:1.15rem;flex-shrink:0}
    .ue-tag{display:inline-flex;align-items:center;border-radius:999px;padding:5px 10px;background:#eff6ff;color:#1d4ed8;font-size:.73rem;font-weight:900;letter-spacing:.04em;text-transform:uppercase;margin-bottom:10px;border:1px solid #cfe0fb}
    .ue-title{margin:0;color:#0f172a;font-size:clamp(1.45rem,2vw,1.88rem);font-weight:900;line-height:1.05}
    .ue-subtitle{margin:6px 0 0;color:#64748b;font-size:.95rem;max-width:760px}
    .ue-head-meta{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
    .ue-head-chip{display:inline-flex;align-items:center;gap:6px;padding:8px 11px;border-radius:999px;border:1px solid #dbe5f0;background:#f8fafc;color:#475569;font-size:.78rem;font-weight:800}
    .ue-toolbar{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:10px}
    .ue-btn,.ue-toolbar form{display:inline-flex}
    .ue-btn{min-height:42px;border-radius:12px;border:1px solid #d5deea;background:#fff;color:#334155;font-weight:800;padding:.62rem .98rem;align-items:center;gap:.5rem;text-decoration:none;box-shadow:0 1px 2px rgba(15,23,42,.04)}
    .ue-btn:hover{color:#0f172a;background:#f8fafc;border-color:#cbd5e1}
    .ue-btn-primary{background:#2563eb;border-color:#2563eb;color:#fff}
    .ue-btn-primary:hover{color:#fff;background:#1d4ed8;border-color:#1d4ed8}
    .ue-btn-warning{background:#fff7ed;border-color:#fed7aa;color:#c2410c}
    .ue-btn-danger{background:#fef2f2;border-color:#fecaca;color:#b91c1c}
    .ue-btn-success{background:#ecfdf5;border-color:#bbf7d0;color:#15803d}
    .ue-alert{border-radius:14px;margin-bottom:14px}
    .ue-grid{display:grid;grid-template-columns:290px minmax(0,1fr);gap:18px}
    .ue-profile-stack{display:grid;gap:14px}
    .ue-profile-hero{padding:18px;background:#fff;color:#0f172a;border-radius:18px 18px 0 0;border-bottom:1px solid #e2e8f0}
    .ue-avatar{width:76px;height:76px;border-radius:20px;background:#2563eb;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:1.25rem;font-weight:900;overflow:hidden;box-shadow:0 12px 24px -20px rgba(37,99,235,.65)}
    .ue-avatar img,.ue-avatar-preview img{width:100%;height:100%;object-fit:cover}
    .ue-profile-name{margin:14px 0 4px;font-size:1.08rem;font-weight:900;color:#0f172a}
    .ue-profile-email{margin:0;color:#64748b;word-break:break-word}
    .ue-pill-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}
    .ue-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:8px 10px;font-size:.74rem;font-weight:900;line-height:1}
    .ue-role-admin{background:#fee2e2;color:#b91c1c}.ue-role-medecin{background:#dbeafe;color:#1d4ed8}.ue-role-secretaire{background:#e2e8f0;color:#475569}.ue-status-actif{background:#dcfce7;color:#15803d}.ue-status-desactive{background:#e2e8f0;color:#475569}.ue-status-en_attente{background:#fef3c7;color:#b45309}
    .ue-block{padding:16px 18px;border-top:1px solid #e2e8f0}.ue-block:first-child{border-top:0}
    .ue-block h3,.ue-section-head h3{margin:0;color:#0f172a;font-size:1rem;font-weight:900}
    .ue-block p{margin:6px 0 0;color:#64748b;font-size:.88rem}
    .ue-meta{display:grid;gap:10px;margin-top:12px}
    .ue-meta-item{display:grid;gap:4px}.ue-meta-label{color:#64748b;font-size:.75rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase}.ue-meta-value{color:#0f172a;font-weight:700}
    .ue-main{display:grid;gap:14px}
    .ue-section-head{padding:15px 18px;border-bottom:1px solid #e2e8f0;background:#f8fafc;border-radius:18px 18px 0 0}
    .ue-section-head p{margin:4px 0 0;color:#64748b;font-size:.9rem}
    .ue-section-body{padding:18px}
    .ue-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .ue-span-2{grid-column:span 2}
    .user-edit-page .form-label{color:#334155;font-size:.78rem;letter-spacing:.03em;text-transform:uppercase;font-weight:800;margin-bottom:.35rem}
    .user-edit-page .form-control,.user-edit-page .form-select{border-radius:12px;border-color:#cbd5e1;min-height:44px;color:#0f172a;background:#fff}
    .user-edit-page .form-control:focus,.user-edit-page .form-select:focus{border-color:#60a5fa;box-shadow:0 0 0 3px rgba(37,99,235,.12)}
    .user-edit-page .form-check-input{border-color:#aac6e3}.user-edit-page .form-check-input:checked{background-color:#2563eb;border-color:#2563eb}
    .ue-avatar-upload{display:grid;grid-template-columns:108px minmax(0,1fr);gap:14px;align-items:start}
    .ue-avatar-preview{width:108px;height:108px;border-radius:22px;border:1px dashed #cbd5e1;background:#f8fafc;display:flex;align-items:center;justify-content:center;overflow:hidden;color:#2563eb;font-size:1.45rem;font-weight:900}
    .ue-avatar-actions,.ue-actions-bottom{display:flex;flex-wrap:wrap;gap:10px}
    .ue-helper{margin-top:6px;color:#64748b;font-size:.84rem}
    .ue-module-layout{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .ue-module-group{border:1px solid #e2e8f0;border-radius:14px;background:#fff;padding:14px}
    .ue-module-group h4{margin:0 0 10px;color:#0f172a;font-size:.95rem;font-weight:900}
    .ue-module-list{display:grid;gap:10px}
    .ue-module-option{display:flex;align-items:flex-start;gap:10px;padding:10px 11px;border-radius:12px;border:1px solid #e2e8f0;background:#f8fafc}
    .ue-module-option.disabled{opacity:.6}
    .ue-module-text strong{display:block;color:#0f172a;font-size:.92rem}
    .ue-module-text span{display:block;color:#64748b;font-size:.82rem;margin-top:2px}
    .ue-security-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .ue-toggle-card{border:1px solid #e2e8f0;border-radius:14px;background:#fff;padding:14px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
    .ue-toggle-card p{margin:5px 0 0;color:#64748b;font-size:.85rem}
    .ue-danger-zone{background:#fff;border:1px solid #fecaca}
    .ue-danger-zone h3{color:#991b1b}
    .ue-danger-zone p{margin:6px 0 0;color:#7f1d1d;font-size:.87rem}
    .ue-danger-actions{display:grid;gap:10px;margin-top:14px}
    .ue-danger-actions form,.ue-danger-actions .ue-btn{width:100%}
    .ue-secondary-link{width:100%;justify-content:center}
    @media (max-width:1199px){.ue-grid{grid-template-columns:1fr}}
    @media (max-width:991px){.ue-head{grid-template-columns:1fr}.ue-toolbar{justify-content:flex-start}.ue-form-grid,.ue-security-grid,.ue-module-layout,.ue-avatar-upload{grid-template-columns:1fr}.ue-span-2{grid-column:auto}}
    @media (max-width:576px){.user-edit-page{padding:10px 10px 18px;border-radius:14px}.ue-card,.ue-section-head,.ue-section-body,.ue-profile-hero,.ue-block{padding-left:14px;padding-right:14px}.ue-toolbar,.ue-avatar-actions,.ue-actions-bottom{flex-direction:column}.ue-btn,.ue-toolbar form,.ue-toolbar .ue-btn{width:100%;justify-content:center}.ue-head-meta{flex-direction:column}}
</style>

<div class="user-edit-page">
    <div class="ue-head">
        <div class="ue-card">
            <div class="ue-title-wrap">
                <div class="ue-icon"><i class="fas fa-user-shield"></i></div>
                <div>
                    <span class="ue-tag">Gestion des accès</span>
                    <h1 class="ue-title">Modifier utilisateur</h1>
                    <p class="ue-subtitle">Pilotez le profil, les accès et les préférences depuis un écran d'édition plus compact et plus net.</p>
                    <div class="ue-head-meta">
                        <span class="ue-head-chip"><i class="fas fa-user-tag"></i><span>{{ $user->role_label }}</span></span>
                        <span class="ue-head-chip"><i class="fas fa-shield-alt"></i><span>{{ $user->account_status_label }}</span></span>
                        <span class="ue-head-chip"><i class="fas fa-clock"></i><span>{{ $lastLoginText }}</span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="ue-toolbar">
            <button type="submit" form="userEditForm" class="ue-btn ue-btn-primary"><i class="fas fa-save"></i><span>Enregistrer</span></button>
            <a href="{{ route('utilisateurs.activity', $user) }}" class="ue-btn"><i class="fas fa-chart-line"></i><span>Activité</span></a>
            <a href="{{ route('utilisateurs.index') }}" class="ue-btn"><i class="fas fa-arrow-left"></i><span>Retour</span></a>
        </div>
    </div>

    @if(session('generated_password'))
        <div class="alert alert-warning ue-alert"><strong>Mot de passe provisoire :</strong> {{ session('generated_password') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger ue-alert">
            <strong>Des corrections sont nécessaires.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ue-grid">
        <aside class="ue-profile-stack">
            <div class="ue-panel">
                <div class="ue-profile-hero">
                    <div class="ue-avatar" id="sidebarAvatarPreview" data-initials="{{ $user->initials }}">
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="Avatar {{ $user->name }}">
                        @else
                            <span>{{ $user->initials }}</span>
                        @endif
                    </div>
                    <div class="ue-profile-name">{{ trim($lastNameValue . ' ' . $firstNameValue) }}</div>
                    <p class="ue-profile-email">{{ old('email', $user->email) }}</p>
                    <div class="ue-pill-row">
                        <span class="ue-pill {{ $roleClass }}"><i class="fas fa-user-tag"></i><span>{{ $user->role_label }}</span></span>
                        <span class="ue-pill {{ $statusClass }}"><i class="fas fa-shield-alt"></i><span>{{ $user->account_status_label }}</span></span>
                    </div>
                </div>

                <div class="ue-block">
                    <h3>Résumé du compte</h3>
                    <div class="ue-meta">
                        <div class="ue-meta-item"><span class="ue-meta-label">Dernière connexion</span><span class="ue-meta-value">{{ $lastLoginText }}</span></div>
                        <div class="ue-meta-item"><span class="ue-meta-label">Dernière activité</span><span class="ue-meta-value">{{ $lastActivityText }}</span></div>
                        <div class="ue-meta-item"><span class="ue-meta-label">Téléphone professionnel</span><span class="ue-meta-value">{{ $user->professional_phone ?: 'Non renseigné' }}</span></div>
                        <div class="ue-meta-item"><span class="ue-meta-label">Service</span><span class="ue-meta-value">{{ $user->department ? ucfirst($user->department) : 'Non renseigné' }}</span></div>
                        <div class="ue-meta-item"><span class="ue-meta-label">Compte créé le</span><span class="ue-meta-value">{{ optional($user->created_at)->format('d/m/Y H:i') }}</span></div>
                    </div>
                </div>
            </div>

            <div class="ue-panel ue-danger-zone">
                <div class="ue-block">
                    <h3>Actions sensibles</h3>
                    <p>Regroupez ici les opérations de sécurité et d'administration à fort impact sur le compte.</p>
                    <div class="ue-danger-actions">
                        <form method="POST" action="{{ route('utilisateurs.reset-password', $user) }}">
                            @csrf
                            <button type="submit" class="ue-btn ue-btn-warning"><i class="fas fa-key"></i><span>Réinitialiser le mot de passe</span></button>
                        </form>
                        @if((int) $user->id !== (int) auth()->id())
                            <form method="POST" action="{{ route('utilisateurs.toggle-status', $user) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ $toggleTargetStatus }}">
                                <button type="submit" class="ue-btn {{ $toggleTargetStatus === 'actif' ? 'ue-btn-success' : 'ue-btn-warning' }}">
                                    <i class="fas {{ $toggleTargetStatus === 'actif' ? 'fa-user-check' : 'fa-user-slash' }}"></i>
                                    <span>{{ $toggleTargetStatus === 'actif' ? 'Réactiver le compte' : 'Désactiver le compte' }}</span>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('utilisateurs.destroy', $user) }}" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ue-btn ue-btn-danger"><i class="fas fa-trash-alt"></i><span>Supprimer</span></button>
                            </form>
                        @endif
                        <a href="{{ route('utilisateurs.activity', $user) }}" class="ue-btn ue-secondary-link"><i class="fas fa-history"></i><span>Consulter l'activité</span></a>
                    </div>
                </div>
            </div>
        </aside>

        <div class="ue-main">
            <form action="{{ route('utilisateurs.update', $user) }}" method="POST" enctype="multipart/form-data" id="userEditForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="remove_avatar" id="removeAvatarInput" value="0">

                <section class="ue-panel">
                    <div class="ue-section-head">
                        <h3>Informations générales</h3>
                        <p>Identité, coordonnées et photo de profil du compte.</p>
                    </div>
                    <div class="ue-section-body">
                        <div class="ue-form-grid">
                            <div class="ue-span-2">
                                <label class="form-label" for="avatar">Avatar utilisateur</label>
                                <div class="ue-avatar-upload">
                                    <div class="ue-avatar-preview" id="formAvatarPreview" data-initials="{{ $user->initials }}">
                                        @if($avatarUrl)
                                            <img src="{{ $avatarUrl }}" alt="Aperçu avatar {{ $user->name }}">
                                        @else
                                            <span>{{ $user->initials }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <input class="form-control @error('avatar') is-invalid @enderror" type="file" id="avatar" name="avatar" accept=".jpg,.jpeg,.png,.webp">
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="ue-helper">Formats acceptés : JPG, PNG, WEBP. Taille maximale 2 Mo. Un aperçu est affiché avant enregistrement.</div>
                                        <div class="ue-avatar-actions mt-3">
                                            <button type="button" class="ue-btn" id="removeAvatarBtn"><i class="fas fa-image"></i><span>Retirer la photo</span></button>
                                            <a href="{{ route('utilisateurs.activity', $user) }}" class="ue-btn"><i class="fas fa-history"></i><span>Historique utilisateur</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="form-label" for="name">Nom</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $lastNameValue }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="first_name">Prénom</label>
                                <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ $firstNameValue }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="ue-span-2">
                                <label class="form-label" for="professional_phone">Téléphone professionnel</label>
                                <input type="text" id="professional_phone" name="professional_phone" class="form-control @error('professional_phone') is-invalid @enderror" value="{{ old('professional_phone', $user->professional_phone) }}" placeholder="+212 ...">
                                @error('professional_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="ue-span-2">
                                <label class="form-label" for="account_expires_at">Expiration du compte</label>
                                <input type="date" id="account_expires_at" name="account_expires_at" class="form-control @error('account_expires_at') is-invalid @enderror" value="{{ old('account_expires_at', optional($user->account_expires_at)->format('Y-m-d')) }}">
                                @error('account_expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ue-panel">
                    <div class="ue-section-head">
                        <h3>Accès et sécurité</h3>
                        <p>Rôle, statut du compte, modules autorisés et options de protection.</p>
                    </div>
                    <div class="ue-section-body">
                        <div class="ue-form-grid mb-3">
                            <div>
                                <label class="form-label" for="role">Rôle</label>
                                <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    @foreach($roleMap as $value => $label)
                                        <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="account_status">Statut du compte</label>
                                <select id="account_status" name="account_status" class="form-select @error('account_status') is-invalid @enderror" required>
                                    @foreach($accountStatusOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('account_status', $user->account_status_key) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('account_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="ue-security-grid mb-3">
                            <div class="ue-toggle-card">
                                <div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="two_factor_enabled" name="two_factor_enabled" value="1" @checked(old('two_factor_enabled', $user->two_factor_enabled))>
                                        <label class="form-check-label" for="two_factor_enabled">Authentification à deux facteurs</label>
                                    </div>
                                    <p>Prépare le compte pour un second facteur de vérification à la connexion.</p>
                                </div>
                                <i class="fas fa-shield-alt text-primary mt-1"></i>
                            </div>

                            <div class="ue-toggle-card">
                                <div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="force_password_change" name="force_password_change" value="1" @checked(old('force_password_change', $user->force_password_change))>
                                        <label class="form-check-label" for="force_password_change">Forcer le changement de mot de passe</label>
                                    </div>
                                    <p>L'utilisateur devra définir un nouveau mot de passe à sa prochaine connexion.</p>
                                </div>
                                <i class="fas fa-key text-warning mt-1"></i>
                            </div>
                        </div>

                        <div class="ue-form-grid mb-3">
                            <div>
                                <label class="form-label" for="password">Nouveau mot de passe</label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" placeholder="Laisser vide pour conserver l'actuel">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="ue-helper">Minimum 12 caractères avec majuscule, minuscule, chiffre et symbole.</div>
                            </div>

                            <div>
                                <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Modules autorisés</label>
                            <div class="ue-helper mb-3">Choisissez les modules accessibles à cet utilisateur. Les administrateurs conservent un accès global.</div>
                            <div class="ue-module-layout">
                                @foreach($moduleGroups as $groupTitle => $moduleIds)
                                    <div class="ue-module-group">
                                        <h4>{{ $groupTitle }}</h4>
                                        <div class="ue-module-list">
                                            @foreach($moduleIds as $moduleId)
                                                @php
                                                    $module = $managedModulesById->get($moduleId);
                                                    $label = $module['label'] ?? ucfirst($moduleId);
                                                @endphp
                                                <label class="ue-module-option" data-module-card>
                                                    <input type="checkbox" class="form-check-input mt-1" name="module_permissions[]" value="{{ $moduleId }}" data-module-option @checked(in_array($moduleId, $selectedModuleIds, true))>
                                                    <span class="ue-module-text">
                                                        <strong>{{ $label }}</strong>
                                                        <span>Accès rapide au module {{ strtolower($label) }}.</span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ue-panel">
                    <div class="ue-section-head">
                        <h3>Informations professionnelles</h3>
                        <p>Cadrez le poste, le service et les identifiants professionnels du compte.</p>
                    </div>
                    <div class="ue-section-body">
                        <div class="ue-form-grid">
                            <div>
                                <label class="form-label" for="job_title">Fonction</label>
                                <select id="job_title" name="job_title" class="form-select @error('job_title') is-invalid @enderror">
                                    <option value="">Sélectionner</option>
                                    @foreach($jobTitleOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('job_title', $user->job_title) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label" for="department">Service</label>
                                <select id="department" name="department" class="form-select @error('department') is-invalid @enderror">
                                    <option value="">Sélectionner</option>
                                    @foreach($departmentOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('department', $user->department) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label" for="speciality">Spécialité</label>
                                <input type="text" id="speciality" name="speciality" class="form-control @error('speciality') is-invalid @enderror" value="{{ old('speciality', $user->speciality) }}" placeholder="Cardiologie, accueil, comptabilité...">
                            </div>

                            <div>
                                <label class="form-label" for="order_number">Numéro d'ordre</label>
                                <input type="text" id="order_number" name="order_number" class="form-control @error('order_number') is-invalid @enderror" value="{{ old('order_number', $user->order_number) }}" placeholder="Référence professionnelle">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ue-panel">
                    <div class="ue-section-head">
                        <h3>Préférences utilisateur</h3>
                        <p>Langue, fuseau horaire et canaux de notification adaptés au poste.</p>
                    </div>
                    <div class="ue-section-body">
                        <div class="ue-form-grid">
                            <div>
                                <label class="form-label" for="ui_language">Langue</label>
                                <select id="ui_language" name="ui_language" class="form-select @error('ui_language') is-invalid @enderror" required>
                                    @foreach($languageOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('ui_language', $user->ui_language) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label" for="timezone">Fuseau horaire</label>
                                <select id="timezone" name="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                                    @foreach($timezoneOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('timezone', $user->timezone) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="ue-span-2">
                                <label class="form-label" for="notification_channel">Notifications</label>
                                <select id="notification_channel" name="notification_channel" class="form-select @error('notification_channel') is-invalid @enderror" required>
                                    @foreach($notificationChannelOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('notification_channel', $user->notification_channel) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="ue-helper">La configuration choisie servira pour les alertes internes et les notifications de sécurité.</div>
                            </div>
                        </div>

                        <div class="ue-actions-bottom mt-4">
                            <a href="{{ route('utilisateurs.index') }}" class="ue-btn"><i class="fas fa-times"></i><span>Annuler</span></a>
                            <button type="submit" class="ue-btn ue-btn-primary"><i class="fas fa-save"></i><span>Enregistrer les modifications</span></button>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleField = document.getElementById('role');
    const nameInput = document.getElementById('name');
    const firstNameInput = document.getElementById('first_name');
    const emailInput = document.getElementById('email');
    const moduleOptions = Array.from(document.querySelectorAll('[data-module-option]'));
    const moduleCards = Array.from(document.querySelectorAll('[data-module-card]'));
    const avatarInput = document.getElementById('avatar');
    const removeAvatarInput = document.getElementById('removeAvatarInput');
    const removeAvatarBtn = document.getElementById('removeAvatarBtn');
    const sidebarPreview = document.getElementById('sidebarAvatarPreview');
    const formPreview = document.getElementById('formAvatarPreview');

    function setAvatarPreview(container, src, initials) {
        if (!container) return;
        container.innerHTML = src ? '<img src="' + src + '" alt="Avatar">' : '<span>' + initials + '</span>';
    }

    function initialsFromName(value) {
        const source = (value || '').trim();
        if (!source) return 'US';
        return source.split(/\s+/).slice(0, 2).map(function (part) {
            return part.charAt(0).toUpperCase();
        }).join('');
    }

    function updateSummaryIdentity() {
        const title = document.querySelector('.ue-profile-name');
        const email = document.querySelector('.ue-profile-email');
        const fullName = ((nameInput ? nameInput.value : '') + ' ' + (firstNameInput ? firstNameInput.value : '')).trim();
        const initials = initialsFromName(fullName);

        if (title) {
            title.textContent = fullName || 'Utilisateur';
        }

        if (email && emailInput) {
            email.textContent = emailInput.value.trim() || 'email@cabinet.test';
        }

        if (sidebarPreview && !sidebarPreview.querySelector('img')) {
            setAvatarPreview(sidebarPreview, '', initials);
        }

        if (formPreview && !formPreview.querySelector('img')) {
            setAvatarPreview(formPreview, '', initials);
        }
    }

    function applyRoleState() {
        if (!roleField) return;
        const isAdmin = roleField.value === 'admin';
        moduleOptions.forEach(function (input) { input.disabled = isAdmin; });
        moduleCards.forEach(function (card) { card.classList.toggle('disabled', isAdmin); });
    }

    if (roleField) {
        roleField.addEventListener('change', applyRoleState);
        applyRoleState();
    }

    if (nameInput) nameInput.addEventListener('input', updateSummaryIdentity);
    if (firstNameInput) firstNameInput.addEventListener('input', updateSummaryIdentity);
    if (emailInput) emailInput.addEventListener('input', updateSummaryIdentity);

    if (avatarInput) {
        avatarInput.addEventListener('change', function () {
            if (!avatarInput.files || !avatarInput.files[0]) return;
            const reader = new FileReader();
            const initials = initialsFromName(((nameInput ? nameInput.value : '') + ' ' + (firstNameInput ? firstNameInput.value : '')).trim());
            reader.onload = function (event) {
                const src = event.target && event.target.result ? event.target.result : '';
                setAvatarPreview(sidebarPreview, src, initials);
                setAvatarPreview(formPreview, src, initials);
                if (removeAvatarInput) removeAvatarInput.value = '0';
            };
            reader.readAsDataURL(avatarInput.files[0]);
        });
    }

    if (removeAvatarBtn) {
        removeAvatarBtn.addEventListener('click', function () {
            const initials = initialsFromName(((nameInput ? nameInput.value : '') + ' ' + (firstNameInput ? firstNameInput.value : '')).trim());
            if (avatarInput) avatarInput.value = '';
            if (removeAvatarInput) removeAvatarInput.value = '1';
            setAvatarPreview(sidebarPreview, '', initials);
            setAvatarPreview(formPreview, '', initials);
        });
    }

    updateSummaryIdentity();
});
</script>
@endpush
