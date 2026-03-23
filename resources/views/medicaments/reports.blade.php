@extends('layouts.app')

@section('title', 'Rapports et statistiques des médicaments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Rapports et statistiques des médicaments
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('medicaments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtres de période -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('medicaments.reports') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="date_debut">Date de début</label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut"
                                           value="{{ request('date_debut', now()->startOfMonth()->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="date_fin">Date de fin</label>
                                    <input type="date" class="form-control" id="date_fin" name="date_fin"
                                           value="{{ request('date_fin', now()->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="type_rapport">Type de rapport</label>
                                    <select class="form-control" id="type_rapport" name="type_rapport">
                                        <option value="general" {{ request('type_rapport', 'general') == 'general' ? 'selected' : '' }}>Général</option>
                                        <option value="stock" {{ request('type_rapport') == 'stock' ? 'selected' : '' }}>Stock</option>
                                        <option value="mouvements" {{ request('type_rapport') == 'mouvements' ? 'selected' : '' }}>Mouvements</option>
                                        <option value="expiration" {{ request('type_rapport') == 'expiration' ? 'selected' : '' }}>Expiration</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Générer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Statistiques générales -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon"><i class="fas fa-pills text-primary"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total médicaments</span>
                                    <span class="info-box-number">{{ $stats['total_medicaments'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon"><i class="fas fa-check-circle text-success"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Médicaments actifs</span>
                                    <span class="info-box-number">{{ $stats['medicaments_actifs'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon"><i class="fas fa-exclamation-triangle text-warning"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Stock faible</span>
                                    <span class="info-box-number">{{ $stats['stock_faible'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon"><i class="fas fa-calendar-times text-danger"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Expirés ou périmés</span>
                                    <span class="info-box-number">{{ $stats['expires'] + $stats['perimes'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Valeur du stock -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-primary">
                                <div class="card-body text-white">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title mb-0">Valeur totale du stock</h5>
                                            <p class="mb-0">Tous médicaments confondus</p>
                                        </div>
                                        <div class="text-right">
                                            <h3 class="mb-0">{{ number_format($stats['valeur_totale_stock'], 2) }} €</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success">
                                <div class="card-body text-white">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title mb-0">Mouvements du mois</h5>
                                            <p class="mb-0">Entrées et sorties</p>
                                        </div>
                                        <div class="text-right">
                                            <h3 class="mb-0">{{ $stats['total_mouvements_mois'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphiques et tableaux selon le type de rapport -->
                    @if(request('type_rapport') == 'stock' || request('type_rapport', 'general') == 'general')
                        <!-- État du stock -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">État du stock par médicament</h5>
                                    </div>
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Médicament</th>
                                                    <th>Stock actuel</th>
                                                    <th>Seuil d'alerte</th>
                                                    <th>Stock idéal</th>
                                                    <th>Valeur (€)</th>
                                                    <th>Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($medicamentsStock as $medicament)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $medicament->nom_commercial }}</strong>
                                                        @if($medicament->dci)
                                                            <br><small class="text-muted">{{ $medicament->dci }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ $medicament->stock_status == 'rupture' ? 'danger' : ($medicament->stock_status == 'faible' ? 'warning' : 'success') }}">
                                                            {{ $medicament->quantite_stock }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $medicament->quantite_seuil }}</td>
                                                    <td>{{ $medicament->quantite_ideale }}</td>
                                                    <td>{{ number_format($medicament->valeur_stock, 2) }} €</td>
                                                    <td>
                                                        @switch($medicament->stock_status)
                                                            @case('rupture')
                                                                <span class="badge badge-danger">Rupture</span>
                                                                @break
                                                            @case('faible')
                                                                <span class="badge badge-warning">Faible</span>
                                                                @break
                                                            @case('normal')
                                                                <span class="badge badge-info">Normal</span>
                                                                @break
                                                            @case('optimal')
                                                                <span class="badge badge-success">Optimal</span>
                                                                @break
                                                        @endswitch
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Aucun médicament trouvé</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(request('type_rapport') == 'mouvements' || request('type_rapport', 'general') == 'general')
                        <!-- Mouvements de stock -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Mouvements de stock</h5>
                                    </div>
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Médicament</th>
                                                    <th>Type</th>
                                                    <th>Quantité</th>
                                                    <th>Stock avant</th>
                                                    <th>Stock après</th>
                                                    <th>Motif</th>
                                                    <th>Utilisateur</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($mouvements as $mouvement)
                                                <tr>
                                                    <td>{{ $mouvement->date_mouvement->format('d/m/Y H:i') }}</td>
                                                    <td>{{ $mouvement->medicament->nom_commercial }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $mouvement->type_mouvement == 'entree' ? 'success' : 'danger' }}">
                                                            {{ $mouvement->type_mouvement_label }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-{{ $mouvement->quantite >= 0 ? 'success' : 'danger' }}">
                                                            {{ $mouvement->quantite_formatee }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $mouvement->quantite_avant }}</td>
                                                    <td>{{ $mouvement->quantite_apres }}</td>
                                                    <td>{{ $mouvement->motif ?: '-' }}</td>
                                                    <td>{{ $mouvement->user->name ?? 'Système' }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">Aucun mouvement trouvé</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(request('type_rapport') == 'expiration' || request('type_rapport', 'general') == 'general')
                        <!-- Médicaments expirés ou périmés -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Médicaments expirés ou périmés</h5>
                                    </div>
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Médicament</th>
                                                    <th>Date de péremption</th>
                                                    <th>Jours restants</th>
                                                    <th>Stock actuel</th>
                                                    <th>Statut</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($medicamentsExpires as $medicament)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $medicament->nom_commercial }}</strong>
                                                        @if($medicament->dci)
                                                            <br><small class="text-muted">{{ $medicament->dci }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $medicament->date_peremption ? $medicament->date_peremption->format('d/m/Y') : '-' }}</td>
                                                    <td>
                                                        @if($medicament->jours_restants !== null)
                                                            <span class="badge badge-{{ $medicament->jours_restants < 0 ? 'danger' : ($medicament->jours_restants <= 30 ? 'warning' : 'info') }}">
                                                                {{ $medicament->jours_restants }} j.
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $medicament->quantite_stock }}</td>
                                                    <td>
                                                        @switch($medicament->expiration_status)
                                                            @case('expire')
                                                                <span class="badge badge-danger">Expiré</span>
                                                                @break
                                                            @case('bientot_expire')
                                                                <span class="badge badge-warning">Expire bientôt</span>
                                                                @break
                                                            @case('valide')
                                                                <span class="badge badge-success">Valide</span>
                                                                @break
                                                            @case('non_defini')
                                                                <span class="badge badge-light">Non défini</span>
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('medicaments.show', $medicament) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Aucun médicament expiré trouvé</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Répartition par catégories -->
                    @if(request('type_rapport', 'general') == 'general')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Répartition par catégories</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="categoriesChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Répartition par types</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="typesChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@if(request('type_rapport', 'general') == 'general')
@vite('resources/js/vendor-reporting.js')
<script>
    // Graphique des catégories
    const categoriesData = @json($stats['categories']);
    const categoriesChart = new Chart(document.getElementById('categoriesChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(categoriesData),
            datasets: [{
                data: Object.values(categoriesData),
                backgroundColor: [
                    '#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8',
                    '#6c757d', '#e83e8c', '#fd7e14', '#20c997', '#6f42c1'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Graphique des types
    const typesData = @json($stats['types']);
    const typesChart = new Chart(document.getElementById('typesChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(typesData),
            datasets: [{
                data: Object.values(typesData),
                backgroundColor: [
                    '#007bff', '#28a745', '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
@endif
@endpush
