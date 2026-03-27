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

{{-- DEBUG PANEL: Sidebar Rendering Debug --}}
@if(app()->environment('local', 'staging') || config('app.debug'))
<div id="sidebar-debug" style="position:fixed;top:10px;right:10px;z-index:99999;background:#ffeb3b;color:#000;padding:12px;border-radius:8px;border:2px solid #f59e0b;max-width:380px;max-height:300px;overflow:auto;font-family:'Monaco','Menlo',monospace;font-size:12px;box-shadow:0 10px 30px rgba(0,0,0,0.3);line-height:1.4;">
    <strong>🔍 SIDEBAR DEBUG ({{ now()->toDateTimeString() }})</strong><br>
    <strong>User:</strong> {{ auth()->user()?->email ?? 'Guest' }} | <strong>Role:</strong> {{ auth()->user()?->role ?? 'N/A' }} | <strong>isAdmin:</strong> {{ auth()->user()?->isAdmin() ? '✅ YES' : '❌ NO' }}<br>
    <strong>PatientCount:</strong> {{ $patientCount ?? 'N/A' }}<br>
<strong>Raw $menuItems IDs:</strong><br>
    <pre style="margin:4px 0;background:#fff;padding:8px;border-radius:4px;font-size:11px;">{{ json_encode(array_column($menuItems ?? [], 'id'), JSON_PRETTY_PRINT) }}</pre>
    <strong>Module Permissions:</strong><br>
    <pre style="margin:4px 0;background:#e0f2fe;padding:8px;border-radius:4px;font-size:11px;">@json(auth()->user()?->module_permissions ?? [])</pre>
    <strong>Grouped sections:</strong> {{ collect($groupedMenu ?? [])->keys()->implode(', ') }} ({{ collect($groupedMenu ?? [])->flatten(1)->count() }} items)<br>
<strong>Per-Item Access Analysis:</strong>
    @php
        $criticalItems = ['dashboard','patients','consultations','planning','medecins','pharmacie','facturation','examens','depenses','contacts','sms','documents','statistiques','rapports','parametres','utilisateurs'];
        $user = auth()->user();
        $menuIds = array_column($menuItems ?? [], 'id');
    @endphp
    <div style="margin:6px 0;overflow-x:auto;">
        <table style="border-collapse:collapse;font-size:10px;background:#f8fafc;">
            <thead><tr style="background:#e2e8f0;"><th style="padding:2px 4px;border:1px solid #cbd5e1;">ID</th><th style="padding:2px 4px;border:1px solid #cbd5e1;">hasAccess</th><th style="padding:2px 4px;border:1px solid #cbd5e1;">Rendered</th><th style="padding:2px 4px;border:1px solid #cbd5e1;">Reason</th></tr></thead>
            <tbody>
            @foreach($criticalItems as $id)
                @php
                    $hasAccess = $user?->hasModuleAccess($id) ?? false;
                    $isRendered = in_array($id, $menuIds);
                    $reason = $user?->isAdmin() ? 'admin' : ($hasAccess ? 'permission' : 'missing');
                    if (in_array($id, ['parametres','utilisateurs']) && !$user?->isAdmin()) $reason = 'admin-only';
                @endphp
                <tr style="background:{{ $isRendered ? '#dcfce7' : '#fee2e2' }};">
                    <td style="padding:1px 4px;border:1px solid #cbd5e1;font-weight:{{ $isRendered ? 'bold' : 'normal' }};">{{ $id }}</td>
                    <td style="padding:1px 4px;border:1px solid #cbd5e1;text-align:center;">{{ $hasAccess ? '✅' : '❌' }}</td>
                    <td style="padding:1px 4px;border:1px solid #cbd5e1;text-align:center;">{{ $isRendered ? '✅' : '❌' }}</td>
                    <td style="padding:1px 4px;border:1px solid #cbd5e1;font-size:9px;">{{ $reason }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <strong>Expected critical:</strong> patients,planning,consultations,facturation,pharmacie{{ auth()->user()?->isAdmin() ? ',parametres,utilisateurs' : '' }}<br>
    <em>Inspect DOM: F12 → Ctrl+F "data-id="planning"" etc.</em><br>
    <button onclick="this.parentElement.remove();localStorage.removeItem('sidebarDebugDismissed')" style="margin-top:4px;padding:2px 8px;background:#ef4444;color:white;border:none;border-radius:4px;font-size:11px;cursor:pointer;">❌ Dismiss</button>
</div>

<script>
if(localStorage.getItem('sidebarDebugDismissed')) document.getElementById('sidebar-debug')?.remove();
</script>
@endif

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
