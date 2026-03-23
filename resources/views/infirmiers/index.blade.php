@extends('layouts.app')

@section('title', 'Infirmiers - SCABINET')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-nurse me-2"></i>
                        Équipe d'Infirmiers
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <button class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Ajouter un infirmier
                            </button>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Rechercher un infirmier...">
                        </div>
                    </div>

                    <!-- Grille des infirmiers -->
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-nurse fa-3x text-primary mb-3"></i>
                                    <h5>Marie Dupont</h5>
                                    <p class="text-muted">Infirmière en chef</p>
                                    <div class="mt-3">
                                        <span class="badge bg-success me-1">Disponible</span>
                                        <span class="badge bg-info">Expérience: 15 ans</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-nurse fa-3x text-success mb-3"></i>
                                    <h5>Jean Martin</h5>
                                    <p class="text-muted">Infirmier spécialisé</p>
                                    <div class="mt-3">
                                        <span class="badge bg-success me-1">Disponible</span>
                                        <span class="badge bg-info">Expérience: 10 ans</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-nurse fa-3x text-info mb-3"></i>
                                    <h5>Sophie Bernard</h5>
                                    <p class="text-muted">Infirmière de nuit</p>
                                    <div class="mt-3">
                                        <span class="badge bg-warning me-1">En service</span>
                                        <span class="badge bg-info">Expérience: 8 ans</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-nurse fa-3x text-warning mb-3"></i>
                                    <h5>Paul Durand</h5>
                                    <p class="text-muted">Infirmier polyvalent</p>
                                    <div class="mt-3">
                                        <span class="badge bg-success me-1">Disponible</span>
                                        <span class="badge bg-info">Expérience: 12 ans</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-nurse fa-3x text-danger mb-3"></i>
                                    <h5>Isabelle Moreau</h5>
                                    <p class="text-muted">Infirmière pédiatrique</p>
                                    <div class="mt-3">
                                        <span class="badge bg-success me-1">Disponible</span>
                                        <span class="badge bg-info">Expérience: 7 ans</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-nurse fa-3x text-secondary mb-3"></i>
                                    <h5>Luc Petit</h5>
                                    <p class="text-muted">Infirmier d'urgence</p>
                                    <div class="mt-3">
                                        <span class="badge bg-danger me-1">Indisponible</span>
                                        <span class="badge bg-info">Expérience: 20 ans</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques des infirmiers -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Statistiques de l'Équipe</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-2">
                                    <h3 class="text-primary">6</h3>
                                    <p class="text-muted mb-0">Infirmiers</p>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="text-success">5</h3>
                                    <p class="text-muted mb-0">Disponibles</p>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="text-info">72</h3>
                                    <p class="text-muted mb-0">Années d'expérience</p>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="text-warning">95%</h3>
                                    <p class="text-muted mb-0">Taux de satisfaction</p>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="text-danger">24/7</h3>
                                    <p class="text-muted mb-0">Disponibilité</p>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="text-secondary">100%</h3>
                                    <p class="text-muted mb-0">Formation continue</p>
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
