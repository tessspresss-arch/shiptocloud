@extends('layouts.app')

@section('title', 'Centre de gouvernance')

@section('content')
<style>
    .governance-v2 {
        width: 100%;
        max-width: none;
        margin: 0;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0;
        border-left: 0;
        border-right: 0;
        padding: 1rem;
        min-height: calc(100vh - 130px);
    }

    .governance-v2 .governance-head {
        border: 1px solid #dbe7f7;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 8px 22px rgba(2, 6, 23, 0.05);
        padding: 0.9rem 1rem;
    }

    .governance-v2 .governance-title {
        color: #0f172a;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .governance-v2 .governance-subtitle {
        color: #6b7280 !important;
    }

    .governance-v2 .module-card {
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 8px 22px rgba(2, 6, 23, 0.05);
    }

    .governance-v2 .system-status-table {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
    }

    .governance-v2 .system-status-table .table {
        margin-bottom: 0;
    }

    .governance-v2 .system-status-table td,
    .governance-v2 .system-status-table th {
        padding: .95rem 1rem;
        vertical-align: middle;
    }

    .governance-v2 .status-label {
        display: inline-flex;
        align-items: center;
        gap: .65rem;
        font-weight: 700;
        color: #0f172a;
    }

    .governance-v2 .status-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
    }

    .governance-v2 .status-hint {
        color: #64748b;
        font-size: .875rem;
        line-height: 1.45;
    }

    .governance-v2 .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border-radius: 999px;
        padding: .42rem .72rem;
        font-size: .78rem;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .governance-v2 .status-badge::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: currentColor;
    }

    .governance-v2 .status-badge.success { background: #ecfdf5; color: #15803d; border-color: #bbf7d0; }
    .governance-v2 .status-badge.warning { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
    .governance-v2 .status-badge.danger { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }

    .governance-v2 .console-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .governance-v2 .console-card {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #fff;
        padding: 1rem;
        text-decoration: none;
        color: inherit;
        display: grid;
        gap: .7rem;
        min-height: 132px;
        box-shadow: 0 10px 24px -28px rgba(15, 23, 42, 0.2);
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .governance-v2 .console-card:hover {
        transform: translateY(-2px);
        border-color: #bfdbfe;
        box-shadow: 0 16px 28px -28px rgba(37, 99, 235, 0.24);
    }

    .governance-v2 .console-card.active {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .governance-v2 .console-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .governance-v2 .console-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        color: #2563eb;
    }

    .governance-v2 .console-card-title {
        font-weight: 800;
        color: #0f172a;
    }

    .governance-v2 .console-card-desc {
        color: #64748b;
        font-size: .88rem;
        line-height: 1.5;
    }

    .governance-v2 .content-card {
        min-height: calc(100vh - 280px);
    }

    .governance-v2 .content-card .card-body {
        padding: 1.1rem 1.2rem 1.3rem;
    }

    .governance-v2 h2.h5 {
        color: #1e3a8a;
        font-weight: 700;
        margin-bottom: 0.9rem !important;
    }

    .governance-v2 h3.h6,
    .governance-v2 h4.h6 {
        color: #1e40af;
        font-weight: 700;
    }

    .governance-v2 .border.rounded.p-3 {
        background: #f8fbff;
        border-color: #dbe7f7 !important;
    }

    .governance-v2 .table-responsive {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .governance-v2 .table {
        margin-bottom: 0;
    }

    .governance-v2 .form-label {
        font-weight: 600;
        color: #334155;
    }

    .governance-v2 .pagination {
        margin-top: 0.9rem;
        margin-bottom: 0;
        justify-content: end;
    }

    .governance-v2 .alert {
        border-radius: 12px;
    }

    .governance-v2 .nav-card {
        background: #ffffff;
    }

    .governance-v2 .btn-outline-secondary {
        border-color: #cbd5e1;
        color: #334155;
    }

    .governance-v2 .btn-outline-secondary:hover {
        background: #f1f5f9;
        color: #1e293b;
        border-color: #94a3b8;
    }

    .governance-v2 .table thead th {
        white-space: nowrap;
    }

    .governance-v2 .table-light,
    .governance-v2 .table-light th {
        background: #f8fafc;
        color: #334155;
    }

    .governance-v2 .badge.bg-success { background-color: #16a34a !important; }
    .governance-v2 .badge.bg-secondary { background-color: #64748b !important; }

    body.dark-mode .governance-v2 .module-card,
    body.dark-mode .governance-v2 .nav-card {
        background: rgba(17, 24, 39, 0.94);
        border-color: #374151 !important;
        color: #f3f4f6;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35);
    }

    body.dark-mode .governance-v2 h2.h5 {
        color: #dbeafe;
    }

    body.dark-mode .governance-v2 h3.h6,
    body.dark-mode .governance-v2 h4.h6,
    body.dark-mode .governance-v2 .form-label {
        color: #bfdbfe;
    }

    body.dark-mode .governance-v2 {
        background: #0f172a;
        border-color: #2f4b67;
    }

    body.dark-mode .governance-v2 .governance-head {
        background: linear-gradient(180deg, #102236 0%, #0f1d30 100%);
        border-color: #2f4b67;
        box-shadow: 0 14px 28px -20px rgba(0, 0, 0, 0.65);
    }

    body.dark-mode .governance-v2 .governance-title {
        color: #f9fafb;
    }

    body.dark-mode .governance-v2 .text-muted {
        color: #9ca3af !important;
    }

    body.dark-mode .governance-v2 .border,
    body.dark-mode .governance-v2 .table,
    body.dark-mode .governance-v2 .table td,
    body.dark-mode .governance-v2 .table th {
        border-color: #374151 !important;
    }

    body.dark-mode .governance-v2 .border.rounded.p-3,
    body.dark-mode .governance-v2 .table-responsive {
        background: #111827;
    }

    body.dark-mode .governance-v2 .table {
        color: #f3f4f6;
    }

    body.dark-mode .governance-v2 .table-light,
    body.dark-mode .governance-v2 .table-light th {
        background: #1f2937 !important;
        color: #f3f4f6 !important;
    }

    body.dark-mode .governance-v2 .form-control,
    body.dark-mode .governance-v2 .form-select {
        background: #111827;
        color: #f3f4f6;
        border-color: #374151;
    }

    body.dark-mode .governance-v2 .btn-outline-primary,
    body.dark-mode .governance-v2 .btn-outline-secondary {
        border-color: #3b82f6;
        color: #bfdbfe;
    }

    body.dark-mode .governance-v2 .btn-outline-primary:hover,
    body.dark-mode .governance-v2 .btn-outline-secondary:hover {
        background: #1d4ed8;
        color: #fff;
    }

    body.dark-mode .governance-v2 .system-status-table,
    body.dark-mode .governance-v2 .console-card {
        background: #111827;
        border-color: #374151;
    }

    body.dark-mode .governance-v2 .console-card.active {
        background: #172554;
        border-color: #3b82f6;
    }

    body.dark-mode .governance-v2 .console-card-title,
    body.dark-mode .governance-v2 .status-label {
        color: #f3f4f6;
    }

    body.dark-mode .governance-v2 .console-card-desc,
    body.dark-mode .governance-v2 .status-hint {
        color: #9ca3af;
    }

    @media (max-width: 768px) {
        .governance-v2 {
            padding: 0.75rem;
            border-radius: 14px;
        }

        .governance-v2 .governance-head {
            padding: 0.75rem;
        }

        .governance-v2 .content-card {
            min-height: auto;
        }

        .governance-v2 .content-card .card-body {
            padding: 0.9rem;
        }

        .governance-v2 .pagination {
            justify-content: center;
        }

        .governance-v2 .console-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="container-fluid p-0 governance-v2">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 governance-head">
        <div>
            <h1 class="h3 mb-1 governance-title">Centre de Gouvernance Applicative</h1>
            <p class="text-muted mb-0 governance-subtitle">Console d administration systeme pour la gouvernance, la securite et l exploitation.</p>
        </div>
        <a href="{{ route('parametres.index') }}" class="btn btn-outline-secondary">Retour Parametres</a>
    </div>

    <div class="card border-0 shadow-sm mb-3 module-card nav-card">
        <div class="card-body">
            <div class="console-grid">
                @foreach($sections as $key => $label)
                    @php
                        $routeName = match($key) {
                            'overview' => 'admin.settings.index',
                            'general' => 'admin.settings.general',
                            'rbac' => 'admin.settings.rbac',
                            'security' => 'admin.settings.security',
                            'audit' => 'admin.settings.audit',
                            'notifications' => 'admin.settings.notifications',
                            'performance' => 'admin.settings.performance',
                            'integrations' => 'admin.settings.integrations',
                            default => 'admin.settings.index',
                        };
                        $card = $sectionCards[$key] ?? ['icon' => 'fa-circle', 'desc' => 'Console systeme'];
                    @endphp
                    <a href="{{ route($routeName) }}" class="console-card {{ $activeSection === $key ? 'active' : '' }}">
                        <div class="console-card-head">
                            <span class="console-card-icon"><i class="fas {{ $card['icon'] }}"></i></span>
                            @if($activeSection === $key)
                                <span class="status-badge success">Actif</span>
                            @endif
                        </div>
                        <div class="console-card-title">{{ $label }}</div>
                        <div class="console-card-desc">{{ $card['desc'] }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm module-card content-card">
        <div class="card-body">
            @if($activeSection === 'overview')
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 class="h5 mb-0">Statut plateforme</h2>
                    <span class="text-muted small">Lecture temps reel de la console systeme</span>
                </div>
                <div class="system-status-table">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Service</th>
                                    <th>Etat</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($platformStatuses as $status)
                                    <tr>
                                        <td>
                                            <div class="status-label">
                                                <span class="status-icon"><i class="fas {{ $status['icon'] }}"></i></span>
                                                <span>{{ $status['label'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $status['badge'] }}">{{ $status['state_label'] }}</span>
                                        </td>
                                        <td>
                                            <div class="status-hint">{{ $status['hint'] }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($activeSection === 'general')
                <h2 class="h5 mb-3">Parametres generaux avances</h2>
                <p class="text-muted mb-0">Timezone, locale, formats date/heure, devise principale et multi-devises seront geres dans cette section.</p>
            @elseif($activeSection === 'rbac')
                <h2 class="h5 mb-3">RBAC avance</h2>
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="small text-muted">Roles definis</div>
                            <div class="h4 mb-0">{{ $roles->count() }}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="small text-muted">Permissions actionnelles</div>
                            <div class="h4 mb-0">{{ $permissionsCount }}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="small text-muted">Permissions effectives (utilisateur selectionne)</div>
                            <div class="h4 mb-0">{{ $effectiveCount }}</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="h6">Heritage des roles</h3>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Role</th>
                                    <th>Parent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                    <tr>
                                        <td>{{ $role->label }} <span class="text-muted">({{ $role->name }})</span></td>
                                        <td>{{ $role->parent?->label ?? 'Aucun' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-3">
                    <h3 class="h6">Previsualisation effective des permissions</h3>
                    <form method="GET" action="{{ route('admin.settings.rbac') }}" class="row g-2 align-items-end mb-3">
                        <div class="col-12 col-lg-6">
                            <label class="form-label">Utilisateur</label>
                            <select name="user_id" class="form-select" onchange="this.form.submit()">
                                @foreach($usersForPreview as $userPreview)
                                    <option value="{{ $userPreview->id }}" {{ $selectedUserId === $userPreview->id ? 'selected' : '' }}>
                                        {{ $userPreview->name }} ({{ $userPreview->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    @if($selectedUser)
                        <div class="small text-muted mb-2">
                            Utilisateur selectionne: {{ $selectedUser->name }} - Roles: {{ $selectedUser->roles->pluck('name')->join(', ') ?: 'Aucun' }}
                        </div>

                        <div class="border rounded p-3 mb-3">
                            <h4 class="h6 mb-3">Gestion des roles utilisateur</h4>
                            <form method="POST" action="{{ route('admin.settings.rbac.update-roles', $selectedUser) }}" class="row g-2">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                                @foreach($roles as $roleOption)
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="role_ids[]" value="{{ $roleOption->id }}"
                                                id="role_{{ $roleOption->id }}"
                                                {{ in_array($roleOption->id, $selectedUserRoleIds, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $roleOption->id }}">
                                                {{ $roleOption->label }} <span class="text-muted">({{ $roleOption->name }})</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer les roles</button>
                                </div>
                            </form>
                        </div>

                        <div class="border rounded p-3 mb-3">
                            <h4 class="h6 mb-3">Overrides de permissions (inherit / allow / deny)</h4>
                            <form method="POST" action="{{ route('admin.settings.rbac.update-overrides', $selectedUser) }}">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                                <div class="table-responsive mb-2">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Permission</th>
                                                <th>Override</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($permissions as $permission)
                                                @php $currentOverride = $selectedUserOverrides[$permission->id] ?? 'inherit'; @endphp
                                                <tr>
                                                    <td>
                                                        <span class="fw-semibold">{{ $permission->code }}</span>
                                                    </td>
                                                    <td>
                                                        <select name="overrides[{{ $permission->id }}]" class="form-select form-select-sm">
                                                            <option value="inherit" {{ $currentOverride === 'inherit' ? 'selected' : '' }}>inherit</option>
                                                            <option value="allow" {{ $currentOverride === 'allow' ? 'selected' : '' }}>allow</option>
                                                            <option value="deny" {{ $currentOverride === 'deny' ? 'selected' : '' }}>deny</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Enregistrer les overrides</button>
                            </form>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ressource</th>
                                    <th>Permissions actives</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissionsByResource as $resource => $items)
                                    <tr>
                                        <td>{{ $resource }}</td>
                                        <td>
                                            @foreach($items as $item)
                                                <span class="badge {{ $item['allowed'] ? 'bg-success' : 'bg-secondary' }} me-1 mb-1">
                                                    {{ $item['action'] }}
                                                </span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-muted">Aucune permission disponible</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($activeSection === 'security')
                <h2 class="h5 mb-3">Securite applicative</h2>
                <p class="text-muted mb-0">Politique mot de passe, 2FA, limite des tentatives, timeout session, whitelist IP et CORS.</p>
            @elseif($activeSection === 'audit')
                <h2 class="h5 mb-3">Audit & tracabilite</h2>
                <div class="row g-3 mb-3">
                    <div class="col-12 col-xl-8">
                        <div class="border rounded p-3 h-100">
                            <h3 class="h6 mb-3">Filtres</h3>
                            <form method="GET" action="{{ route('admin.settings.audit') }}" class="row g-2">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Utilisateur</label>
                                    <select name="user_id" class="form-select">
                                        <option value="">Tous</option>
                                        @foreach($auditUsers as $auditUser)
                                            <option value="{{ $auditUser->id }}" {{ (string)($auditFilters['user_id'] ?? '') === (string)$auditUser->id ? 'selected' : '' }}>
                                                {{ $auditUser->name }} ({{ $auditUser->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Module</label>
                                    <select name="module" class="form-select">
                                        <option value="">Tous</option>
                                        @foreach($auditModules as $module)
                                            <option value="{{ $module }}" {{ (string)($auditFilters['module'] ?? '') === (string)$module ? 'selected' : '' }}>
                                                {{ $module }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Date debut</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ $auditFilters['date_from'] ?? '' }}">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Date fin</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ $auditFilters['date_to'] ?? '' }}">
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
                                    <a href="{{ route('admin.settings.audit') }}" class="btn btn-outline-secondary btn-sm">Reinitialiser</a>
                                    <a href="{{ route('admin.settings.audit.export', request()->query()) }}" class="btn btn-outline-primary btn-sm">Exporter CSV</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="border rounded p-3 h-100">
                            <h3 class="h6 mb-3">Retention des logs</h3>
                            <form method="POST" action="{{ route('admin.settings.audit.retention') }}" class="row g-2">
                                @csrf
                                <div class="col-12">
                                    <label class="form-label">Nombre de jours</label>
                                    <input type="number" min="1" max="3650" step="1" class="form-control" name="retention_days" value="{{ $auditRetentionDays }}">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                                </div>
                            </form>
                            <div class="small text-muted mt-2">
                                La commande planifiee <code>audit:prune</code> utilise cette valeur par defaut.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered align-middle mb-2">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Utilisateur</th>
                                <th>Module</th>
                                <th>Action</th>
                                <th>Cible</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditLogs as $log)
                                <tr>
                                    <td>{{ optional($log->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        {{ $log->user?->name ?? 'Systeme' }}
                                        @if($log->user?->email)
                                            <div class="small text-muted">{{ $log->user->email }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $log->module }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>
                                        {{ $log->target_type ?? '-' }}
                                        @if($log->target_id)
                                            <span class="text-muted">#{{ $log->target_id }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->ip_address ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted">Aucun log d audit trouve.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $auditLogs->links() }}
            @elseif($activeSection === 'notifications')
                <h2 class="h5 mb-3">Notifications & communication</h2>
                <p class="text-muted mb-0">SMTP avance, templates dynamiques, API SMS, webhooks et monitoring queue.</p>
            @elseif($activeSection === 'performance')
                <h2 class="h5 mb-3">Performance & cache</h2>
                <p class="text-muted mb-3">Cache de configuration, gestion cache applicatif, parametres cron et maintenance programmable.</p>

                <div class="border rounded p-3">
                    <h3 class="h6 mb-2">Maintenance cache systeme</h3>
                    <p class="text-muted mb-3">Cette action execute <strong>optimize:clear</strong> pour nettoyer config, routes, vues, events et cache applicatif.</p>
                    <form method="POST" action="{{ route('parametres.clear-cache') }}" onsubmit="return confirm('Confirmer le vidage des caches systeme ?');">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Vider les caches systeme</button>
                    </form>
                </div>
            @elseif($activeSection === 'integrations')
                <h2 class="h5 mb-3">API & integrations</h2>
                <p class="text-muted mb-0">Clients API, tokens, rate limits, webhooks entrants/sortants et documentation interne.</p>
            @endif
        </div>
    </div>
</div>
@endsection
