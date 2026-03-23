@extends('layouts.app')

@section('title', 'Salles & Équipements - SCABINET')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-door-open me-2"></i>
                        Gestion des Salles & Équipements
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <button class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Ajouter une salle
                            </button>
                            <button class="btn btn-outline-success ms-2">
                                <i class="fas fa-tools me-2"></i>Maintenance
                            </button>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Rechercher salle/équipement...">
                                <select class="form-control">
                                    <option>Toutes les salles</option>
                                    <option>Consultation</option>
                                    <option>Examens</option>
                                    <option>Urgences</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Salles disponibles -->
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1">Salle 101</h5>
                                            <p class="text-muted mb-0">Consultation générale</p>
                                        </div>
                                        <span class="badge bg-success">Disponible</span>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <i class="fas fa-chair-office fa-2x text-primary mb-2"></i>
                                            <br><small>Siège patient</small>
                                        </div>
                                        <div class="col-6">
                                            <i class="fas fa-desktop fa-2x text-info mb-2"></i>
                                            <br><small>Ordinateur</small>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button class="btn btn-outline-primary btn-sm w-100">
                                            <i class="fas fa-eye me-1"></i>Détails
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1">Salle 102</h5>
                                            <p class="text-muted mb-0">Cardiologie</p>
                                        </div>
                                        <span class="badge bg-warning">Occupée</span>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <i class="fas fa-heartbeat fa-2x text-danger mb-2"></i>
                                            <br><small>ECG</small>
                                        </div>
                                        <div class="col-6">
                                            <i class="fas fa-lungs fa-2x text-success mb-2"></i>
                                            <br><small>Stéthoscope</small>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button class="btn btn-outline-warning btn-sm w-100">
                                            <i class="fas fa-clock me-1"></i>En cours
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1">Salle 201</h5>
                                            <p class="text-muted mb-0">Radiologie</p>
                                        </div>
                                        <span class="badge bg-secondary">Maintenance</span>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <i class="fas fa-x-ray fa-2x text-warning mb-2"></i>
                                            <br><small>Scanner</small>
                                        </div>
                                        <div class="col-6">
                                            <i class="fas fa-radiation fa-2x text-danger mb-2"></i>
                                            <br><small>Protection</small>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button class="btn btn-outline-secondary btn-sm w-100">
                                            <i class="fas fa-tools me-1"></i>Maintenance
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- État des équipements -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">État des Équipements</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Équipement</th>
                                            <th>Salle</th>
                                            <th>État</th>
                                            <th>Dernière maintenance</th>
                                            <th>Prochaine maintenance</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Électrocardiographe</td>
                                            <td>Salle 102</td>
                                            <td><span class="badge bg-success">Fonctionnel</span></td>
                                            <td>15/12/2023</td>
                                            <td>15/06/2024</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Vérifier</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Scanner radiologique</td>
                                            <td>Salle 201</td>
                                            <td><span class="badge bg-warning">Maintenance</span></td>
                                            <td>10/01/2024</td>
                                            <td>10/07/2024</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-warning">Suivre</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Stéthoscope électronique</td>
                                            <td>Salle 101</td>
                                            <td><span class="badge bg-danger">Hors service</span></td>
                                            <td>05/01/2024</td>
                                            <td>À programmer</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger">Réparer</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Otoscope</td>
                                            <td>Salle 103</td>
                                            <td><span class="badge bg-success">Fonctionnel</span></td>
                                            <td>20/12/2023</td>
                                            <td>20/06/2024</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Vérifier</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-primary">8</h3>
                                    <p class="text-muted mb-0">Salles totales</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-success">6</h3>
                                    <p class="text-muted mb-0">Salles disponibles</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-warning">1</h3>
                                    <p class="text-muted mb-0">En maintenance</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-info">85%</h3>
                                    <p class="text-muted mb-0">Équipements opérationnels</p>
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
