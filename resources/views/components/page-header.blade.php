@props([
    'visible' => false,
    'title' => '',
    'subtitle' => '',
    'badgeLabel' => '',
    'searchPlaceholder' => 'Rechercher patient, dossier, rendez-vous...',
    'searchAriaLabel' => 'Recherche dans le module',
    'icon' => 'fa-heart-pulse',
    'authUser' => null,
    'avatarUrl' => null,
    'initials' => 'U',
    'roleLabel' => 'Utilisateur',
    'profileRoute' => '#',
    'settingsRoute' => '#',
    'settingsIcon' => 'fa-sliders',
    'showSettings' => false,
])

@php
    $decodedBadgeLabel = html_entity_decode($badgeLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $decodedTitle = html_entity_decode($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $decodedSubtitle = html_entity_decode($subtitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $formattedTitle = str_replace(' - ', '&nbsp;&ndash;&nbsp;', $decodedTitle);
@endphp

@if($visible)
    <section class="app-topbar app-page-header" aria-label="En-tête de page">
        <div class="app-topbar-inner">
            <div class="topbar-heading">
                <span class="topbar-heading-icon" aria-hidden="true">
                    <i class="fas {{ $icon }}"></i>
                </span>
                <div class="topbar-heading-copy">
                    @if(trim((string) $badgeLabel) !== '')
                        <span class="topbar-heading-badge">{!! $decodedBadgeLabel !!}</span>
                    @endif
                    <h1 class="topbar-heading-title">{!! $formattedTitle !!}</h1>
                    <p class="topbar-heading-subtitle">{!! $decodedSubtitle !!}</p>
                </div>
            </div>

            <div class="topbar-search-wrap" role="search" aria-label="Recherche globale">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input id="globalTopbarSearch" type="search" class="topbar-search-input" placeholder="{!! html_entity_decode($searchPlaceholder, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') !!}" aria-label="{!! html_entity_decode($searchAriaLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') !!}">
            </div>

            <div class="topbar-right">
                <div class="topbar-icon-group" aria-label="Raccourcis">
                    <button type="button" class="topbar-icon-btn" aria-label="Notifications" title="Notifications">
                        <i class="fas fa-bell"></i>
                    </button>
                    @if($showSettings)
                        <a href="{{ $settingsRoute }}" class="topbar-icon-btn" aria-label="Paramètres" title="Paramètres">
                            <i class="fas {{ $settingsIcon }}"></i>
                        </a>
                    @endif
                </div>

                <label class="topbar-theme-toggle" for="topDarkModeToggle">
                    <input type="checkbox" id="topDarkModeToggle">
                    <span class="topbar-theme-switch" aria-hidden="true"><span class="topbar-theme-thumb"></span></span>
                    <span class="topbar-theme-label">Sombre</span>
                </label>

                <div class="topbar-user-menu" id="topUserMenu">
                    <button type="button" class="topbar-user-btn" id="topUserMenuBtn" aria-haspopup="menu" aria-controls="topUserDropdown" aria-expanded="false">
                        <span class="topbar-avatar">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Avatar utilisateur">
                            @else
                                {{ $initials }}
                            @endif
                        </span>
                        <span class="topbar-user-meta">
                            <span class="topbar-user-name">{{ $authUser->name ?? 'Utilisateur' }}</span>
                            <span class="topbar-user-role">{{ $roleLabel }}</span>
                        </span>
                        <i class="fas fa-chevron-down topbar-chevron"></i>
                    </button>

                    <div class="topbar-dropdown" id="topUserDropdown" role="menu" aria-label="Menu utilisateur">
                        <div class="topbar-dropdown-head">
                            <span class="topbar-avatar">
                                @if($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="Avatar utilisateur">
                                @else
                                    {{ $initials }}
                                @endif
                            </span>
                            <div class="topbar-dropdown-identity">
                                <div class="topbar-dropdown-name">{{ $authUser->name ?? 'Utilisateur' }}</div>
                                <div class="topbar-user-mail">{{ $authUser->email ?? '' }}</div>
                                <div class="topbar-user-role-chip">{{ $roleLabel }}</div>
                            </div>
                        </div>

                        <div class="topbar-dropdown-section-label">Mon compte</div>

                        <div class="topbar-dropdown-actions">
                            <a href="{{ $profileRoute }}" class="topbar-dropdown-item" role="menuitem">
                                <span class="topbar-dropdown-item-icon">
                                    <i class="fas fa-user-circle" aria-hidden="true"></i>
                                </span>
                                <span class="topbar-dropdown-item-label">Profil</span>
                                <span class="topbar-dropdown-item-arrow" aria-hidden="true">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            </a>

                            @if($showSettings)
                                <a href="{{ $settingsRoute }}" class="topbar-dropdown-item" role="menuitem">
                                    <span class="topbar-dropdown-item-icon">
                                        <i class="fas {{ $settingsIcon }}" aria-hidden="true"></i>
                                    </span>
                                    <span class="topbar-dropdown-item-label">Paramètres</span>
                                    <span class="topbar-dropdown-item-arrow" aria-hidden="true">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </a>
                            @endif
                        </div>

                        <div class="topbar-dropdown-footer">
                            <form id="topbarLogoutForm" method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="topbar-dropdown-item topbar-dropdown-item-danger" role="menuitem">
                                    <span class="topbar-dropdown-item-icon">
                                        <i class="fas fa-right-from-bracket" aria-hidden="true"></i>
                                    </span>
                                    <span class="topbar-dropdown-item-label">Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
