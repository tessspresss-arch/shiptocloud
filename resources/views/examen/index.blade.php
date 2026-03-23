@extends('layouts.app')

@section('title', 'Examens & Tests - SCABINET')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-microscope me-2"></i>
                        Examens Médicaux & Tests
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <button class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Nouvel Examen
                            </button>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Rechercher un examen...">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-heartbeat fa-3x text-primary mb-3"></i>
                                    <h5>Examens Cardiologiques</h5>
                                    <p class="text-muted">ECG, Échographie cardiaque</p>
                                    <span class="badge bg-primary">5 examens</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-lungs fa-3x text-success mb-3"></i>
                                    <h5>Examens Pulmonaires</h5>
                                    <p class="text-muted">Spirométrie, Radiographie</p>
                                    <span class="badge bg-success">3 examens</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-brain fa-3x text-info mb-3"></i>
                                    <h5>Examens Neurologiques</h5>
                                    <p class="text-muted">IRM, Scanner, EEG</p>
                                    <span class="badge bg-info">2 examens</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Type d'examen</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Résultats</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-microscope fa-2x mb-2"></i>
                                        <br>Aucun examen programmé
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
