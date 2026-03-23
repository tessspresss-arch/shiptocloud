@extends('layouts.app')

@section('title', 'Planning Médecins - SCABINET')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Planning des Médecins
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <select class="form-control">
                                <option>Sélectionner un médecin</option>
                                <option>Dr. Martin</option>
                                <option>Dr. Dubois</option>
                                <option>Dr. Leroy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control">
                                <option>Tous les services</option>
                                <option>Consultation générale</option>
                                <option>Urgences</option>
                                <option>Spécialisé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i>Ajouter créneau
                            </button>
                        </div>
                    </div>

                    <!-- Planning hebdomadaire -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Horaire</th>
                                    <th>Lundi</th>
                                    <th>Mardi</th>
                                    <th>Mercredi</th>
                                    <th>Jeudi</th>
                                    <th>Vendredi</th>
                                    <th>Samedi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">09:00 - 10:00</td>
                                    <td class="bg-success text-white">Dr. Martin</td>
                                    <td class="bg-success text-white">Dr. Dubois</td>
                                    <td class="bg-warning text-dark">Dr. Leroy</td>
                                    <td class="bg-success text-white">Dr. Martin</td>
                                    <td class="bg-success text-white">Dr. Dubois</td>
                                    <td class="bg-light text-muted">Fermé</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">10:00 - 11:00</td>
                                    <td class="bg-success text-white">Dr. Martin</td>
                                    <td class="bg-success text-white">Dr. Dubois</td>
                                    <td class="bg-warning text-dark">Dr. Leroy</td>
                                    <td class="bg-success text-white">Dr. Martin</td>
                                    <td class="bg-success text-white">Dr. Dubois</td>
                                    <td class="bg-light text-muted">Fermé</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">11:00 - 12:00</td>
                                    <td class="bg-warning text-dark">Dr. Leroy</td>
                                    <td class="bg-success text-white">Dr. Martin</td>
                                    <td class="bg-success text-white">Dr. Dubois</td>
                                    <td class="bg-warning text-dark">Dr. Leroy</td>
                                    <td class="bg-success text-white">Dr. Martin</td>
                                    <td class="bg-light text-muted">Fermé</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">14:00 - 15:00</td>
                                    <td class="bg-success text-white">Dr. Dubois</td>
                                    <td class="bg-warning text-dark">Dr. Leroy</td>
                                    <td class="bg-success text-white">Dr. Martin</td>
                                    <td class="bg-success text-white">Dr. Dubois</td>
                                    <td class="bg-warning text-dark">Dr. Leroy</td>
                                    <td class="bg-light text-muted">Fermé</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">15:00 - 16:00</td>
                                    <td class="bg-success text-white">Dr. Dubois</td>
                                    <td class="bg-warning text-dark">Dr. Leroy</td>
                                    <td class="bg-success text-white">Dr. Martin</td>
                                    <td class="bg-success text-white">Dr. Dubois</td>
                                    <td class="bg-warning text-dark">Dr. Leroy</td>
                                    <td class="bg-light text-muted">Fermé</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Légende</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="badge bg-success me-2">&nbsp;</div>
                                        <span>Disponible</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="badge bg-warning me-2">&nbsp;</div>
                                        <span>Occupé</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-light text-muted me-2">&nbsp;</div>
                                        <span>Fermé</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Actions rapides</h6>
                                </div>
                                <div class="card-body">
                                    <button class="btn btn-outline-primary w-100 mb-2">
                                        <i class="fas fa-print me-2"></i>Imprimer planning
                                    </button>
                                    <button class="btn btn-outline-success w-100 mb-2">
                                        <i class="fas fa-share me-2"></i>Partager planning
                                    </button>
                                    <button class="btn btn-outline-info w-100">
                                        <i class="fas fa-sync me-2"></i>Synchroniser
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
@endsection
