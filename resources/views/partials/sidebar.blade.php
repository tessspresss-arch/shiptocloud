@php
    $sidebarSections = [
        'Clinique' => ['dashboard', 'patients', 'consultations', 'planning', 'medecins'],
        'Soins et pharmacie' => ['pharmacie', 'examens', 'documents'],
        'Gestion et relation patient' => ['facturation', 'depenses', 'contacts', 'sms'],
        'Pilotage' => ['statistiques', 'rapports'],
        'Administration' => ['parametres', 'utilisateurs'],
    ];

    $menuCollection = collect($menuItems ?? []);
    $coveredIds = collect($sidebarSections)->flatten()->all();
    $groupedMenu = collect();

    foreach ($sidebarSections as $sectionLabel => $sectionIds) {
        $sectionItems = $menuCollection
            ->filter(fn (array $item) => in_array((string) ($item['id'] ?? ''), $sectionIds, true))
            ->values();

        if ($sectionItems->isNotEmpty()) {
            $groupedMenu->put($sectionLabel, $sectionItems);
        }
    }

    $remainingItems = $menuCollection
        ->reject(fn (array $item) => in_array((string) ($item['id'] ?? ''), $coveredIds, true))
        ->values();

    if ($remainingItems->isNotEmpty()) {
        $groupedMenu->put('Autres modules', $remainingItems);
    }
@endphp

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            @include('partials.brand-logo')
        </div>
        <button id="sidebarToggle" class="sidebar-toggle" aria-label="{{ session('sidebar_collapsed', false) ? 'Ouvrir le menu' : 'Reduire le menu' }}" aria-expanded="{{ session('sidebar_collapsed', false) ? 'false' : 'true' }}">
            <i class="fas {{ session('sidebar_collapsed', false) ? 'fa-chevron-right' : 'fa-chevron-left' }}" id="sidebarIcon" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        @foreach($groupedMenu as $sectionLabel => $sectionItems)
            <div class="sidebar-section">
                <div class="sidebar-section-label">{{ $sectionLabel }}</div>

                @foreach($sectionItems as $item)
                    @php
                        $displayBadge = $item['badge'] ?? null;
                        if (($item['id'] ?? '') === 'patients' && $displayBadge !== null) {
                            $count = (int) ($patientCount ?? 0);
                            $displayBadge = $count > 99 ? '99+' : (string) $count;
                        }
                    @endphp

                    @if($item['has_submenu'])
                        @php
                            $isSubmenuActive = false;
                            foreach (($item['submenu'] ?? []) as $subitemCheck) {
                                $subRoute = $subitemCheck['route'] ?? '';
                                $subPrefix = $subRoute !== '' ? explode('.', $subRoute)[0] . '.*' : '';
                                if ($subRoute !== '' && (request()->routeIs($subRoute) || ($subPrefix !== '' && request()->routeIs($subPrefix)))) {
                                    $isSubmenuActive = true;
                                    break;
                                }
                            }
                        @endphp

                        <div class="nav-item has-submenu {{ $isSubmenuActive ? 'active expanded' : '' }}" data-id="{{ $item['id'] }}">
                            <div
                                class="nav-item-main {{ $isSubmenuActive ? 'active' : '' }}"
                                role="button"
                                tabindex="0"
                                aria-expanded="{{ $isSubmenuActive ? 'true' : 'false' }}"
                            >
                                <span class="nav-icon"><i class="fas fa-{{ $item['icon'] }}"></i></span>
                                <span class="nav-label">{{ $item['label'] }}</span>
                                <div class="nav-spacer"></div>
                                @if($displayBadge)
                                    <span class="nav-badge">{{ $displayBadge }}</span>
                                @endif
                                <span class="nav-arrow"><i class="fas fa-chevron-right"></i></span>
                            </div>
                            <div class="nav-submenu">
                                @foreach($item['submenu'] as $subitem)
                                    @php
                                        $subRoute = $subitem['route'] ?? '';
                                        $isSubitemActive = $subRoute !== '' && request()->routeIs($subRoute);
                                    @endphp
                                    <a href="{{ route($subitem['route']) }}" class="nav-subitem {{ $isSubitemActive ? 'active' : '' }}" data-nav-priority="module">
                                        {{ $subitem['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        @php
                            $itemRoute = $item['route'] ?? '';
                            $itemPrefix = $itemRoute !== '' ? explode('.', $itemRoute)[0] . '.*' : '';
                            $isItemActive = $itemRoute !== '' && (request()->routeIs($itemRoute) || ($itemPrefix !== '' && request()->routeIs($itemPrefix)));
                        @endphp
                        <a href="{{ route($item['route']) }}" class="nav-item {{ $isItemActive ? 'active' : '' }}" data-id="{{ $item['id'] }}" data-nav-priority="module">
                            <span class="nav-icon"><i class="fas fa-{{ $item['icon'] }}"></i></span>
                            <span class="nav-label">{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-footer-card">
            <span class="sidebar-footer-card-label">Cadence du jour</span>
            <div class="sidebar-footer-card-row">
                <strong class="sidebar-footer-card-value">{{ now()->format('H:i') }}</strong>
                <span class="sidebar-footer-card-note">Vue synchronisee</span>
            </div>
        </div>

        <form id="logoutForm" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                <span class="btn-logout-label">Deconnexion</span>
            </button>
        </form>
    </div>
</aside>
