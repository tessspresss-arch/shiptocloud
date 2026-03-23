@extends('layouts.app')

@section('title', 'Utilisateurs')
@section('topbar_subtitle', 'Administration des comptes et des acces dans une interface plus compacte et plus lisible.')

@push('styles')
<style>
.users-page{display:grid;gap:16px;padding:8px 8px 24px}.users-header,.users-filters,.users-table-card,.users-mobile-card{background:var(--card);border:1px solid var(--border);border-radius:18px;box-shadow:0 14px 30px -28px rgba(15,23,42,.18)}.users-header{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;padding:1rem 1.05rem}.users-head-copy{display:grid;gap:.35rem;min-width:0}.users-eyebrow{width:fit-content;display:inline-flex;align-items:center;padding:.28rem .65rem;border-radius:999px;border:1px solid color-mix(in srgb,var(--border) 82%,var(--primary));background:color-mix(in srgb,#fff 86%,var(--primary-soft));color:var(--color-sidebar);font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em}.users-title{margin:0;color:var(--text);font-size:clamp(1.4rem,1.7vw,1.95rem);font-weight:800;line-height:1.1;letter-spacing:-.02em}.users-subtitle{margin:0;color:var(--muted);font-size:.94rem;line-height:1.5;max-width:64ch}.users-meta{display:flex;align-items:center;flex-wrap:wrap;gap:.45rem}.users-chip{display:inline-flex;align-items:center;gap:.35rem;padding:.34rem .68rem;border-radius:999px;border:1px solid var(--border);background:color-mix(in srgb,#fff 90%,var(--primary-soft));color:var(--color-sidebar);font-size:.77rem;font-weight:700}.users-new-btn{min-height:42px;padding:.65rem 1rem;border-radius:12px;font-weight:800;display:inline-flex;align-items:center;justify-content:center;gap:.5rem}.users-filters{padding:.85rem}.users-filter-form{display:grid;grid-template-columns:minmax(260px,2fr) repeat(4,minmax(145px,1fr)) auto;gap:.75rem;align-items:center}.users-filter-field label{display:none}.users-filter-field .form-control,.users-filter-field .form-select{min-height:42px;border-radius:12px;border-color:var(--border);background:#fff;color:var(--text);box-shadow:none}.users-filter-actions{display:inline-flex;align-items:center;gap:.6rem;justify-content:flex-end}.users-filter-submit,.users-filter-reset{min-height:42px;border-radius:12px;padding:.6rem .95rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center;gap:.45rem;white-space:nowrap}.users-filter-reset{border:1px solid var(--border);background:#fff;color:var(--muted);text-decoration:none}.users-table-card{overflow:hidden}.users-table-head{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.95rem 1rem;border-bottom:1px solid var(--border);background:color-mix(in srgb,var(--card) 90%,var(--primary-soft))}.users-table-head h2{margin:0;color:var(--text);font-size:1rem;font-weight:800}.users-table-head p,.users-table-head span{margin:0;color:var(--muted);font-size:.84rem;font-weight:600}.users-table{min-width:1040px;margin-bottom:0}.users-table thead th{background:color-mix(in srgb,var(--card) 94%,var(--primary-soft));color:var(--muted);font-size:.76rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--border);padding:.85rem 1rem;white-space:nowrap}.users-table tbody td{color:var(--text);border-bottom:1px solid var(--border);padding:.95rem 1rem;vertical-align:middle}.users-table tbody tr:hover{background:color-mix(in srgb,var(--card) 84%,var(--primary-soft))}.users-sort-link{text-decoration:none;color:inherit}.users-sort-link:hover{color:var(--primary)}.users-user{display:flex;align-items:center;gap:.8rem;min-width:0}.users-avatar{width:42px;height:42px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto;background:color-mix(in srgb,var(--primary-soft) 55%,#fff);border:1px solid color-mix(in srgb,var(--border) 82%,var(--primary));color:var(--color-sidebar);font-size:.82rem;font-weight:800;overflow:hidden}.users-avatar img{width:100%;height:100%;object-fit:cover}.users-user-copy{min-width:0;display:grid;gap:.18rem}.users-user-name{color:var(--text);font-weight:800;line-height:1.2;overflow-wrap:anywhere}.users-user-meta,.users-access-note,.users-last-login-sub{color:var(--muted);font-size:.82rem;line-height:1.35;overflow-wrap:anywhere}.users-role-badge,.users-status-badge{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;border:1px solid transparent;padding:.35rem .7rem;font-size:.75rem;font-weight:800;white-space:nowrap}.users-role-admin{background:rgba(229,83,61,.08);color:#b13f2d;border-color:rgba(229,83,61,.16)}.users-role-medecin{background:rgba(44,123,229,.1);color:#2b5fb8;border-color:rgba(44,123,229,.18)}.users-role-secretaire{background:rgba(95,115,138,.1);color:#4f647d;border-color:rgba(95,115,138,.18)}.users-status-actif{background:rgba(0,163,137,.1);color:#0d7a66;border-color:rgba(0,163,137,.18)}.users-status-desactive,.users-status-suspendu{background:rgba(95,115,138,.1);color:#566b83;border-color:rgba(95,115,138,.18)}.users-status-en_attente{background:rgba(240,173,78,.12);color:#a56716;border-color:rgba(240,173,78,.2)}.users-last-login-main{color:var(--text);font-weight:700}.users-empty-row{text-align:center;color:var(--muted);padding:1.5rem 1rem!important}.users-mobile-list{display:none;padding:1rem;gap:.8rem}.users-mobile-card{padding:.95rem;display:grid;gap:.85rem}.users-mobile-top{display:flex;align-items:flex-start;justify-content:space-between;gap:.8rem}.users-mobile-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.8rem}.users-mobile-item label{display:block;margin-bottom:.24rem;color:var(--muted);font-size:.74rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em}.users-mobile-item div{color:var(--text);font-weight:700;line-height:1.35;overflow-wrap:anywhere}.users-pagination{display:flex;align-items:center;justify-content:space-between;gap:.8rem;flex-wrap:wrap}.users-alerts{display:grid;gap:.75rem}@media (max-width:1399.98px){.users-filter-form{grid-template-columns:minmax(260px,2fr) repeat(4,minmax(140px,1fr))}.users-filter-actions{grid-column:1/-1;justify-content:flex-end}}@media (max-width:991.98px){.users-table-wrap{display:none}.users-mobile-list{display:grid}.users-filter-form{grid-template-columns:1fr 1fr}.users-filter-actions{grid-column:span 2}}@media (max-width:767.98px){.users-page{padding:6px 6px 18px}.users-header{flex-direction:column;align-items:stretch}.users-new-btn,.users-filter-submit,.users-filter-reset{width:100%}.users-filter-form{grid-template-columns:1fr}.users-filter-actions{grid-column:auto;flex-direction:column;align-items:stretch}.users-mobile-grid{grid-template-columns:1fr}.users-mobile-top{flex-direction:column}}@media (max-width:575.98px){.users-mobile-actions>a,.users-mobile-actions>form{flex-basis:100%;min-width:100%}}
</style>
@endpush

@section('content')
@php
    $currentSort = $sort ?? 'name';
    $currentDirection = $direction ?? 'asc';
    $sortIcon = fn (string $column) => $currentSort !== $column ? 'fa-sort text-muted' : ($currentDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
    $nextDirection = fn (string $column) => $currentSort === $column ? ($currentDirection === 'asc' ? 'desc' : 'asc') : 'asc';
    $describeLastLogin = function ($value): array {
        if (! $value) return ['Jamais connecte', 'Aucune connexion enregistree'];
        $loginAt = $value instanceof \Illuminate\Support\Carbon ? $value : \Illuminate\Support\Carbon::parse($value);
        if ($loginAt->isToday()) return ["Aujourd'hui " . $loginAt->format('H:i'), $loginAt->format('d/m/Y H:i')];
        if ($loginAt->isYesterday()) return ['Hier', $loginAt->format('d/m/Y H:i')];
        return [$loginAt->diffForHumans(), $loginAt->format('d/m/Y H:i')];
    };
    $moduleAccessCount = function ($user): int {
        $permissions = $user->module_permissions;
        if (empty($permissions)) return 0;
        if (array_is_list($permissions)) return count($permissions);
        return count(array_filter($permissions, fn ($allowed) => (bool) $allowed));
    };
@endphp

<div class="users-page">
    <header class="users-header">
        <div class="users-head-copy">
            <span class="users-eyebrow">Administration</span>
            <h1 class="users-title">Utilisateurs</h1>
            <p class="users-subtitle">Gerez les comptes, les roles et les statuts d'acces avec une interface plus compacte, plus lisible et plus sobre.</p>
            <div class="users-meta">
                <span class="users-chip"><i class="fas fa-users"></i>{{ $users->total() }} compte{{ $users->total() > 1 ? 's' : '' }}</span>
            </div>
        </div>
        <a href="{{ route('utilisateurs.create') }}" class="btn btn-primary users-new-btn">
            <i class="fas fa-user-plus"></i><span>Nouvel utilisateur</span>
        </a>
    </header>

    <div class="users-alerts">
        @if(session('success'))
            <div class="alert alert-success mb-0">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning mb-0">{{ session('warning') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-0">{{ $errors->first() }}</div>
        @endif
    </div>

    <section class="users-filters">
        <form method="GET" action="{{ route('utilisateurs.index') }}" class="users-filter-form">
            <div class="users-filter-field">
                <label for="usersSearch">Recherche</label>
                <input id="usersSearch" type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Rechercher par nom ou email...">
            </div>
            <div class="users-filter-field">
                <label for="usersRole">Role</label>
                <select id="usersRole" name="role" class="form-select">
                    <option value="">Tous les roles</option>
                    @foreach(($roleOptions ?? []) as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="users-filter-field">
                <label for="usersStatus">Statut</label>
                <select id="usersStatus" name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    @foreach(($accountStatusOptions ?? []) as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="users-filter-field">
                <label for="usersCreatedFrom">Cree du</label>
                <input id="usersCreatedFrom" type="date" name="created_from" value="{{ request('created_from') }}" class="form-control">
            </div>
            <div class="users-filter-field">
                <label for="usersCreatedTo">Au</label>
                <input id="usersCreatedTo" type="date" name="created_to" value="{{ request('created_to') }}" class="form-control">
            </div>
            <div class="users-filter-actions">
                <button type="submit" class="btn btn-primary users-filter-submit">
                    <i class="fas fa-filter"></i><span>Appliquer</span>
                </button>
                <a href="{{ route('utilisateurs.index') }}" class="users-filter-reset">
                    <i class="fas fa-rotate-left"></i><span>Reset</span>
                </a>
            </div>
        </form>
    </section>

    <section class="users-table-card">
        <div class="users-table-head">
            <div>
                <h2>Comptes utilisateurs</h2>
                <p>Vue admin des acces, roles et dernieres connexions.</p>
            </div>
            <span>{{ $users->total() }} resultat{{ $users->total() > 1 ? 's' : '' }}</span>
        </div>

        <div class="users-table-wrap table-responsive">
            <table class="table users-table align-middle">
                <thead>
                    <tr>
                        <th><a href="{{ route('utilisateurs.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => $nextDirection('name')])) }}" class="users-sort-link">Utilisateur <i class="fas {{ $sortIcon('name') }} ms-1"></i></a></th>
                        <th><a href="{{ route('utilisateurs.index', array_merge(request()->query(), ['sort' => 'role', 'direction' => $nextDirection('role')])) }}" class="users-sort-link">Role <i class="fas {{ $sortIcon('role') }} ms-1"></i></a></th>
                        <th><a href="{{ route('utilisateurs.index', array_merge(request()->query(), ['sort' => 'account_status', 'direction' => $nextDirection('account_status')])) }}" class="users-sort-link">Statut <i class="fas {{ $sortIcon('account_status') }} ms-1"></i></a></th>
                        <th>Acces</th>
                        <th><a href="{{ route('utilisateurs.index', array_merge(request()->query(), ['sort' => 'last_login_at', 'direction' => $nextDirection('last_login_at')])) }}" class="users-sort-link">Derniere connexion <i class="fas {{ $sortIcon('last_login_at') }} ms-1"></i></a></th>
                        <th><a href="{{ route('utilisateurs.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => $nextDirection('created_at')])) }}" class="users-sort-link">Creation <i class="fas {{ $sortIcon('created_at') }} ms-1"></i></a></th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            [$lastLoginPrimary, $lastLoginSecondary] = $describeLastLogin($user->last_login_at);
                            $roleClass = match($user->role) { 'admin' => 'users-role-admin', 'medecin' => 'users-role-medecin', default => 'users-role-secretaire' };
                            $statusClass = 'users-status-' . $user->account_status_key;
                            $accessCount = $moduleAccessCount($user);
                            $permissionsLabel = $user->role === 'admin' ? 'Acces complet' : ($accessCount > 0 ? $accessCount . ' modules autorises' : 'Permissions a definir');
                            $permissionsNote = $user->role === 'admin' ? 'Toutes les permissions medicales et administratives' : 'Configuration geree dans la fiche utilisateur';
                            $toggleTarget = $user->account_status_key === 'actif' ? 'desactive' : 'actif';
                            $toggleLabel = $user->account_status_key === 'actif' ? 'Desactiver le compte' : 'Activer le compte';
                            $toggleIcon = $user->account_status_key === 'actif' ? 'fa-user-lock' : 'fa-user-check';
                        @endphp
                        <tr>
                            <td>
                                <div class="users-user">
                                    <span class="users-avatar">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="Avatar {{ $user->name }}">
                                        @else
                                            {{ $user->initials }}
                                        @endif
                                    </span>
                                    <div class="users-user-copy">
                                        <span class="users-user-name">{{ $user->name }}</span>
                                        <span class="users-user-meta">{{ $user->email }}</span>
                                        <span class="users-user-meta">{{ $user->job_title ? ucfirst($user->job_title) : $user->role_label }}@if($user->department) · {{ ucfirst($user->department) }}@endif</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="users-role-badge {{ $roleClass }}">{{ $user->role_label }}</span></td>
                            <td><span class="users-status-badge {{ $statusClass }}">{{ $user->account_status_label }}</span></td>
                            <td>
                                <div class="users-last-login-main">{{ $permissionsLabel }}</div>
                                <div class="users-access-note">{{ $permissionsNote }}</div>
                            </td>
                            <td>
                                <div class="users-last-login-main">{{ $lastLoginPrimary }}</div>
                                <div class="users-last-login-sub">{{ $lastLoginSecondary }}</div>
                            </td>
                            <td>{{ optional($user->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <div class="actions-cell users-mobile-actions">
                                    <a href="{{ route('utilisateurs.edit', $user) }}" title="Modifier" aria-label="Modifier"><i class="fas fa-pen"></i></a>
                                    <a href="{{ route('utilisateurs.activity', $user) }}" title="Activite" aria-label="Activite"><i class="fas fa-clock-rotate-left"></i></a>
                                    @if((int) $user->id !== (int) auth()->id())
                                        <form action="{{ route('utilisateurs.toggle-status', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $toggleLabel }} ?');">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $toggleTarget }}">
                                            <button type="submit" title="{{ $toggleLabel }}" aria-label="{{ $toggleLabel }}"><i class="fas {{ $toggleIcon }}"></i></button>
                                        </form>
                                        <form action="{{ route('utilisateurs.reset-password', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Reinitialiser le mot de passe de cet utilisateur ?');">
                                            @csrf
                                            <button type="submit" title="Reinitialiser le mot de passe" aria-label="Reinitialiser le mot de passe"><i class="fas fa-key"></i></button>
                                        </form>
                                        <form action="{{ route('utilisateurs.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Supprimer" aria-label="Supprimer"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="users-empty-row">Aucun utilisateur trouve.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="users-mobile-list">
            @forelse($users as $user)
                @php
                    [$lastLoginPrimary, $lastLoginSecondary] = $describeLastLogin($user->last_login_at);
                    $roleClass = match($user->role) { 'admin' => 'users-role-admin', 'medecin' => 'users-role-medecin', default => 'users-role-secretaire' };
                    $statusClass = 'users-status-' . $user->account_status_key;
                    $accessCount = $moduleAccessCount($user);
                    $permissionsLabel = $user->role === 'admin' ? 'Acces complet' : ($accessCount > 0 ? $accessCount . ' modules autorises' : 'Permissions a definir');
                    $toggleTarget = $user->account_status_key === 'actif' ? 'desactive' : 'actif';
                    $toggleLabel = $user->account_status_key === 'actif' ? 'Desactiver le compte' : 'Activer le compte';
                    $toggleIcon = $user->account_status_key === 'actif' ? 'fa-user-lock' : 'fa-user-check';
                @endphp
                <article class="users-mobile-card">
                    <div class="users-mobile-top">
                        <div class="users-user">
                            <span class="users-avatar">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="Avatar {{ $user->name }}">
                                @else
                                    {{ $user->initials }}
                                @endif
                            </span>
                            <div class="users-user-copy">
                                <span class="users-user-name">{{ $user->name }}</span>
                                <span class="users-user-meta">{{ $user->email }}</span>
                            </div>
                        </div>
                        <span class="users-status-badge {{ $statusClass }}">{{ $user->account_status_label }}</span>
                    </div>

                    <div><span class="users-role-badge {{ $roleClass }}">{{ $user->role_label }}</span></div>

                    <div class="users-mobile-grid">
                        <div class="users-mobile-item"><label>Acces</label><div>{{ $permissionsLabel }}</div></div>
                        <div class="users-mobile-item"><label>Derniere connexion</label><div>{{ $lastLoginPrimary }}<br><small>{{ $lastLoginSecondary }}</small></div></div>
                        <div class="users-mobile-item"><label>Creation</label><div>{{ optional($user->created_at)->format('d/m/Y H:i') }}</div></div>
                        <div class="users-mobile-item"><label>Service</label><div>{{ $user->department ? ucfirst($user->department) : 'Non renseigne' }}</div></div>
                    </div>

                    <div class="actions-cell users-mobile-actions">
                        <a href="{{ route('utilisateurs.edit', $user) }}" title="Modifier" aria-label="Modifier"><i class="fas fa-pen"></i></a>
                        <a href="{{ route('utilisateurs.activity', $user) }}" title="Activite" aria-label="Activite"><i class="fas fa-clock-rotate-left"></i></a>
                        @if((int) $user->id !== (int) auth()->id())
                            <form action="{{ route('utilisateurs.toggle-status', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $toggleLabel }} ?');">
                                @csrf
                                <input type="hidden" name="status" value="{{ $toggleTarget }}">
                                <button type="submit" title="{{ $toggleLabel }}" aria-label="{{ $toggleLabel }}"><i class="fas {{ $toggleIcon }}"></i></button>
                            </form>
                            <form action="{{ route('utilisateurs.reset-password', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Reinitialiser le mot de passe de cet utilisateur ?');">
                                @csrf
                                <button type="submit" title="Reinitialiser le mot de passe" aria-label="Reinitialiser le mot de passe"><i class="fas fa-key"></i></button>
                            </form>
                            <form action="{{ route('utilisateurs.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Supprimer" aria-label="Supprimer"><i class="fas fa-trash"></i></button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="text-center text-muted py-4">Aucun utilisateur trouve.</div>
            @endforelse
        </div>
    </section>

    <div class="users-pagination">
        <small class="text-muted">Resultats : {{ $users->total() }} utilisateur{{ $users->total() > 1 ? 's' : '' }}</small>
        {{ $users->links() }}
    </div>
</div>
@endsection
