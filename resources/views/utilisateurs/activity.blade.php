@extends('layouts.app')

@section('title', 'Activite utilisateur')
@section('topbar_subtitle', 'Suivi du compte, des acces et des evenements recents dans une vue detail plus nette.')

@push('styles')
<style>
.ua-page{display:grid;gap:16px;padding:8px 8px 24px}.ua-header,.ua-card,.ua-timeline-card{background:var(--card);border:1px solid var(--border);border-radius:18px;box-shadow:0 14px 30px -28px rgba(15,23,42,.18)}.ua-header{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;padding:1rem 1.05rem}.ua-head-copy{display:grid;gap:.35rem;min-width:0}.ua-eyebrow{width:fit-content;display:inline-flex;align-items:center;padding:.28rem .65rem;border-radius:999px;border:1px solid color-mix(in srgb,var(--border) 82%,var(--primary));background:color-mix(in srgb,#fff 86%,var(--primary-soft));color:var(--color-sidebar);font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em}.ua-title{margin:0;color:var(--text);font-size:clamp(1.35rem,1.7vw,1.9rem);font-weight:800;line-height:1.1;letter-spacing:-.02em}.ua-subtitle{margin:0;color:var(--muted);font-size:.94rem;line-height:1.5;max-width:62ch}.ua-back-btn{min-height:42px;padding:.65rem 1rem;border-radius:12px;font-weight:800;display:inline-flex;align-items:center;justify-content:center;gap:.5rem}.ua-layout{display:grid;grid-template-columns:320px minmax(0,1fr);gap:16px}.ua-card{padding:1rem}.ua-profile-top{display:flex;align-items:center;gap:.9rem;padding-bottom:1rem;border-bottom:1px solid var(--border)}.ua-avatar{width:64px;height:64px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;overflow:hidden;background:color-mix(in srgb,var(--primary-soft) 55%,#fff);border:1px solid color-mix(in srgb,var(--border) 82%,var(--primary));color:var(--color-sidebar);font-size:1rem;font-weight:800;flex:0 0 auto}.ua-avatar img{width:100%;height:100%;object-fit:cover}.ua-profile-copy{min-width:0;display:grid;gap:.22rem}.ua-name{margin:0;color:var(--text);font-size:1.06rem;font-weight:800;line-height:1.2}.ua-email{color:var(--muted);font-size:.9rem;overflow-wrap:anywhere}.ua-badges{display:flex;flex-wrap:wrap;gap:.45rem;margin-top:.35rem}.ua-badge{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;border:1px solid transparent;padding:.34rem .7rem;font-size:.74rem;font-weight:800}.ua-role-admin{background:rgba(229,83,61,.08);color:#b13f2d;border-color:rgba(229,83,61,.16)}.ua-role-medecin{background:rgba(44,123,229,.1);color:#2b5fb8;border-color:rgba(44,123,229,.18)}.ua-role-secretaire{background:rgba(95,115,138,.1);color:#4f647d;border-color:rgba(95,115,138,.18)}.ua-status-actif{background:rgba(0,163,137,.1);color:#0d7a66;border-color:rgba(0,163,137,.18)}.ua-status-desactive,.ua-status-suspendu{background:rgba(95,115,138,.1);color:#566b83;border-color:rgba(95,115,138,.18)}.ua-status-en_attente{background:rgba(240,173,78,.12);color:#a56716;border-color:rgba(240,173,78,.2)}.ua-panel{padding-top:1rem}.ua-panel+.ua-panel{margin-top:1rem;border-top:1px solid var(--border)}.ua-panel h2{margin:0 0 .8rem;color:var(--text);font-size:.96rem;font-weight:800}.ua-kv{display:grid;gap:.7rem}.ua-kv-row{display:grid;grid-template-columns:120px 1fr;gap:.7rem}.ua-kv-row label{color:var(--muted);font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em}.ua-kv-row div{color:var(--text);font-weight:700;line-height:1.4}.ua-timeline-card{padding:1rem}.ua-timeline-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding-bottom:.9rem;border-bottom:1px solid var(--border)}.ua-timeline-head h2{margin:0;color:var(--text);font-size:1rem;font-weight:800}.ua-timeline-head p{margin:.18rem 0 0;color:var(--muted);font-size:.88rem}.ua-timeline{position:relative;padding:1rem 0 0 1rem}.ua-timeline::before{content:\"\";position:absolute;left:15px;top:1rem;bottom:0;width:1px;background:color-mix(in srgb,var(--border) 84%,var(--primary))}.ua-item{position:relative;display:grid;grid-template-columns:32px minmax(0,1fr);gap:.9rem;padding:0 0 1rem}.ua-dot{width:12px;height:12px;border-radius:999px;margin:4px 0 0 10px;background:#4f8ef7;box-shadow:0 0 0 5px rgba(79,142,247,.12);position:relative;z-index:1}.ua-dot.success{background:#18a65f;box-shadow:0 0 0 5px rgba(24,166,95,.12)}.ua-dot.warning{background:#f59e0b;box-shadow:0 0 0 5px rgba(245,158,11,.14)}.ua-entry{padding:.9rem 1rem;border:1px solid color-mix(in srgb,var(--border) 88%,transparent);border-radius:14px;background:color-mix(in srgb,var(--card) 92%,var(--primary-soft))}.ua-entry-title{margin:0;color:var(--text);font-size:.94rem;font-weight:800}.ua-entry-meta{margin:.28rem 0 0;color:var(--muted);font-size:.82rem;font-weight:600;line-height:1.5}.ua-empty{padding:1.4rem 1rem;color:var(--muted);text-align:center}.ua-module-list{display:flex;flex-wrap:wrap;gap:.38rem}.ua-module-chip{display:inline-flex;align-items:center;padding:.28rem .55rem;border-radius:999px;border:1px solid var(--border);background:color-mix(in srgb,#fff 92%,var(--primary-soft));color:var(--color-sidebar);font-size:.72rem;font-weight:700}.ua-muted{color:var(--muted)}@media (max-width:991.98px){.ua-layout{grid-template-columns:1fr}}@media (max-width:767.98px){.ua-page{padding:6px 6px 18px}.ua-header{flex-direction:column;align-items:stretch}.ua-back-btn{width:100%}.ua-profile-top{align-items:flex-start}.ua-kv-row{grid-template-columns:1fr}.ua-timeline{padding-left:0}.ua-timeline::before{left:15px}.ua-entry{padding:.8rem .9rem}}
</style>
@endpush

@section('content')
@php
    $roleClass = match($user->role) {'admin' => 'ua-role-admin', 'medecin' => 'ua-role-medecin', default => 'ua-role-secretaire'};
    $statusClass = 'ua-status-' . $user->account_status_key;
    $lastLoginText = $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais connecte';
    $lastActivityText = $user->last_activity_at ? $user->last_activity_at->format('d/m/Y H:i') : 'Aucune activite';
@endphp

<div class="ua-page">
    <header class="ua-header">
        <div class="ua-head-copy">
            <span class="ua-eyebrow">Suivi utilisateur</span>
            <h1 class="ua-title">Activite utilisateur</h1>
            <p class="ua-subtitle">Suivi du compte, dernieres connexions, acces modules et evenements recents de securite dans une vue detail plus claire.</p>
        </div>
        <a href="{{ route('utilisateurs.index') }}" class="btn btn-outline-primary ua-back-btn">
            <i class="fas fa-arrow-left"></i><span>Retour a la liste</span>
        </a>
    </header>

    <div class="ua-layout">
        <aside class="ua-card">
            <div class="ua-profile-top">
                <div class="ua-avatar">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="Avatar {{ $user->name }}">
                    @else
                        {{ $user->initials }}
                    @endif
                </div>
                <div class="ua-profile-copy">
                    <h2 class="ua-name">{{ $user->name }}</h2>
                    <div class="ua-email">{{ $user->email }}</div>
                    <div class="ua-badges">
                        <span class="ua-badge {{ $roleClass }}">{{ $user->role_label }}</span>
                        <span class="ua-badge {{ $statusClass }}">{{ $user->account_status_label }}</span>
                    </div>
                </div>
            </div>

            <section class="ua-panel">
                <h2>Compte</h2>
                <div class="ua-kv">
                    <div class="ua-kv-row"><label>Derniere connexion</label><div>{{ $lastLoginText }}</div></div>
                    <div class="ua-kv-row"><label>Derniere activite</label><div>{{ $lastActivityText }}</div></div>
                    <div class="ua-kv-row"><label>Creation</label><div>{{ optional($user->created_at)->format('d/m/Y H:i') }}</div></div>
                    <div class="ua-kv-row"><label>Telephone</label><div>{{ $user->professional_phone ?: 'Non renseigne' }}</div></div>
                    <div class="ua-kv-row"><label>Service</label><div>{{ $user->department ? ucfirst($user->department) : 'Non renseigne' }}</div></div>
                </div>
            </section>

            <section class="ua-panel">
                <h2>Acces</h2>
                <div class="ua-kv">
                    <div class="ua-kv-row"><label>Mode</label><div>{{ $user->role === 'admin' ? 'Acces complet a tous les modules' : 'Permissions module par module' }}</div></div>
                    <div class="ua-kv-row">
                        <label>Modules</label>
                        <div>
                            @if($user->role === 'admin')
                                Tous les modules
                            @elseif(count($selectedModules) > 0)
                                <div class="ua-module-list">
                                    @foreach($selectedModules as $moduleId)
                                        <span class="ua-module-chip">{{ $managedModules[$moduleId]['label'] ?? $moduleId }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="ua-muted">Aucun module specifique actif</span>
                            @endif
                        </div>
                    </div>
                    <div class="ua-kv-row"><label>2FA</label><div>{{ $user->two_factor_enabled ? 'Activee' : 'Desactivee' }}</div></div>
                    <div class="ua-kv-row"><label>Mot de passe</label><div>{{ $user->force_password_change ? 'Changement force a la prochaine connexion' : 'Normal' }}</div></div>
                </div>
            </section>
        </aside>

        <section class="ua-timeline-card">
            <div class="ua-timeline-head">
                <div>
                    <h2>Journal recent de securite</h2>
                    <p>Connexions, deconnexions et actions de securite recentes sur ce compte.</p>
                </div>
            </div>

            <div class="ua-timeline">
                @forelse($recentEvents as $event)
                    <article class="ua-item">
                        <span class="ua-dot {{ $event['tone'] }}"></span>
                        <div class="ua-entry">
                            <h3 class="ua-entry-title">{{ $event['label'] }}</h3>
                            <p class="ua-entry-meta">
                                {{ $event['occurred_at'] }}
                                @if(!empty($event['ip']))
                                    · IP {{ $event['ip'] }}
                                @endif
                                @if(!empty($event['actor_user_id']))
                                    · Action par utilisateur #{{ $event['actor_user_id'] }}
                                @endif
                            </p>
                        </div>
                    </article>
                @empty
                    <div class="ua-empty">Aucun evenement recent trouve pour cet utilisateur.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
