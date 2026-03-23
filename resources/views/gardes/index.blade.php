@extends('layouts.app')

@section('title', 'Services de Garde - SCABINET')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-moon me-2"></i>
                        Services de Garde & Astreinte
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Organisation des services de garde pour assurer la continuité des soins 24h/24.
                    </div>

                    <!-- Planning de garde actuel -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Planning de garde - Cette semaine</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Jour</th>
                                            <th>Date</th>
                                            <th>Médecin de garde</th>
                                            <th>Infirmier(e)</th>
                                            <th>Contact</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Lundi</strong></td>
                                            <td>{{ now()->startOfWeek()->format('d/m') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-2">
                                                        <i class="fas fa-user-md"></i>
                                                    </div>
                                                    Dr. Martin
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-info text-white me-2">
                                                        <i class="fas fa-user-nurse"></i>
                                                    </div>
                                                    Marie Dubois
                                                </div>
                                            </td>
                                            <td>
                                                <i class="fas fa-phone me-2"></i>06 12 34 56 78<br>
                                                <small class="text-muted">urgences@scabinet.com</small>
                                            </td>
                                            <td><span class="badge bg-success">Confirmé</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mardi</strong></td>
                                            <td>{{ now()->startOfWeek()->addDay()->format('d/m') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-success text-white me-2">
                                                        <i class="fas fa-user-md"></i>
                                                    </div>
                                                    Dr. Dubois
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-success text-white me-2">
                                                        <i class="fas fa-user-nurse"></i>
                                                    </div>
                                                    Pierre Martin
                                                </div>
                                            </td>
                                            <td>
                                                <i class="fas fa-phone me-2"></i>06 98 76 54 32<br>
                                                <small class="text-muted">urgences@scabinet.com</small>
                                            </td>
                                            <td><span class="badge bg-success">Confirmé</span></td>
                                        </tr>
                                        <tr class="table-warning">
                                            <td><strong>Aujourd'hui</strong></td>
                                            <td>{{ now()->format('d/m') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-warning text-white me-2">
                                                        <i class="fas fa-user-md"></i>
                                                    </div>
                                                    Dr. Leroy
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-warning text-white me-2">
                                                        <i class="fas fa-user-nurse"></i>
                                                    </div>
                                                    Sophie Leroy
                                                </div>
                                            </td>
                                            <td>
                                                <i class="fas fa-phone me-2"></i>06 55 44 33 22<br>
                                                <small class="text-muted">urgences@scabinet.com</small>
                                            </td>
                                            <td><span class="badge bg-primary">En cours</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jeudi</strong></td>
                                            <td>{{ now()->startOfWeek()->addDays(3)->format('d/m') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-danger text-white me-2">
                                                        <i class="fas fa-user-md"></i>
                                                    </div>
                                                    Dr. Moreau
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-danger text-white me-2">
                                                        <i class="fas fa-user-nurse"></i>
                                                    </div>
                                                    Anne Petit
                                                </div>
                                            </td>
                                            <td>
                                                <i class="fas fa-phone me-2"></i>06 11 22 33 44<br>
                                                <small class="text-muted">urgences@scabinet.com</small>
                                            </td>
                                            <td><span class="badge bg-warning">À confirmer</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Protocoles d'urgence -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        Protocoles d'urgence
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <i class="fas fa-heartbeat text-danger me-2"></i>
                                            Arrêt cardiaque - Défibrillation
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <i class="fas fa-lungs text-info me-2"></i>
                                            Difficultés respiratoires
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <i class="fas fa-brain text-primary me-2"></i>
                                            Accident vasculaire cérébral
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <i class="fas fa-user-injured text-warning me-2"></i>
                                            Traumatismes graves
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <i class="fas fa-baby text-success me-2"></i>
                                            Urgences pédiatriques
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-ambulance text-danger me-2"></i>
                                        Contacts d'urgence
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="p-3 border rounded mb-3">
                                                <i class="fas fa-phone fa-2x text-danger mb-2"></i>
                                                <h5 class="mb-1">SAMU</h5>
                                                <strong class="text-danger">15</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 border rounded mb-3">
                                                <i class="fas fa-fire fa-2x text-warning mb-2"></i>
                                                <h5 class="mb-1">Pompiers</h5>
                                                <strong class="text-warning">18</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 border rounded">
                                                <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                                                <h5 class="mb-1">Police</h5>
                                                <strong class="text-primary">17</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 border rounded">
                                                <i class="fas fa-hospital fa-2x text-success mb-2"></i>
                                                <h5 class="mb-1">Clinique</h5>
                                                <strong class="text-success">24/7</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques des gardes -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-primary">4</h3>
                                    <p class="text-muted mb-0">Médecins de garde</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-success">12</h3>
                                    <p class="text-muted mb-0">Interventions/mois</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-info">98%</h3>
                                    <p class="text-muted mb-0">Temps de réponse</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-warning">4.2/5</h3>
                                    <p class="text-muted mb-0">Satisfaction patients</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Actions de gestion</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <button class="btn btn-primary w-100">
                                        <i class="fas fa-plus me-2"></i>Nouvelle garde
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-outline-success w-100">
                                        <i class="fas fa-exchange-alt me-2"></i>Échanger garde
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-outline-warning w-100">
                                        <i class="fas fa-bell me-2"></i>Rappels
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-outline-info w-100">
                                        <i class="fas fa-file-alt me-2"></i>Rapport
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}
</style>
@endsection
