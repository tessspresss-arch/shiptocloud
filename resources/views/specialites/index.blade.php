@extends('layouts.app')

@section('title', 'Spécialités Médicales - SCABINET')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Spécialités Médicales
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <button class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Ajouter une spécialité
                            </button>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Rechercher une spécialité...">
                        </div>
                    </div>

                    <!-- Grille des spécialités -->
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-heartbeat fa-3x text-danger mb-3"></i>
                                    <h5>Cardiologie</h5>
                                    <p class="text-muted">Maladies du cœur et du système cardiovasculaire</p>
                                    <div class="mt-3">
                                        <span class="badge bg-primary me-1">2 médecins</span>
                                        <span class="badge bg-success">Disponible</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-brain fa-3x text-info mb-3"></i>
                                    <h5>Neurologie</h5>
                                    <p class="text-muted">Maladies du système nerveux</p>
                                    <div class="mt-3">
                                        <span class="badge bg-primary me-1">1 médecin</span>
                                        <span class="badge bg-success">Disponible</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-lungs fa-3x text-success mb-3"></i>
                                    <h5>Pneumologie</h5>
                                    <p class="text-muted">Maladies des voies respiratoires</p>
                                    <div class="mt-3">
                                        <span class="badge bg-primary me-1">1 médecin</span>
                                        <span class="badge bg-success">Disponible</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-bone fa-3x text-warning mb-3"></i>
                                    <h5>Rhumatologie</h5>
                                    <p class="text-muted">Maladies des articulations et du système locomoteur</p>
                                    <div class="mt-3">
                                        <span class="badge bg-primary me-1">1 médecin</span>
                                        <span class="badge bg-success">Disponible</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-stethoscope fa-3x text-primary mb-3"></i>
                                    <h5>Médecine générale</h5>
                                    <p class="text-muted">Médecine de premier recours</p>
                                    <div class="mt-3">
                                        <span class="badge bg-primary me-1">3 médecins</span>
                                        <span class="badge bg-success">Disponible</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-md fa-3x text-secondary mb-3"></i>
                                    <h5>Dermatologie</h5>
                                    <p class="text-muted">Maladies de la peau</p>
                                    <div class="mt-3">
                                        <span class="badge bg-primary me-1">1 médecin</span>
                                        <span class="badge bg-warning">Limité</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques des spécialités -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Statistiques des Spécialités</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-2">
                                    <h3 class="text-primary">6</h3>
                                    <p class="text-muted mb-0">Spécialités</p>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="text-success">9</h3>
                                    <p class="text-muted mb-0">Médecins</p>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="text-info">85%</h3>
                                    <p class="text-muted mb-0">Taux d'occupation</p>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="text-warning">15</h3>
                                    <p class="text-muted mb-0">Consultations/jour</p>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="text-danger">98%</h3>
                                    <p class="text-muted mb-0">Satisfaction</p>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="text-secondary">24h</h3>
                                    <p class="text-muted mb-0">Délai moyen</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
