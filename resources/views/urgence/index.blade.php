@extends('layouts.app')

@section('title', 'Urgence - SCABINET')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        SERVICE D'URGENCE - 24/7
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-phone-volume me-2"></i>Numéros d'urgence</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>SAMU:</strong> 15<br>
                                <small class="text-muted">Urgences médicales</small>
                            </div>
                            <div class="col-md-4">
                                <strong>Pompiers:</strong> 18<br>
                                <small class="text-muted">Incendies, accidents</small>
                            </div>
                            <div class="col-md-4">
                                <strong>Police:</strong> 17<br>
                                <small class="text-muted">Sécurité, secours</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Protocoles d'urgence</h5>
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-heartbeat me-2 text-danger"></i>
                                    Arrêt cardiaque - Défibrillation
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-lungs me-2 text-warning"></i>
                                    Difficultés respiratoires
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-brain me-2 text-info"></i>
                                    Accident vasculaire cérébral
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-user-injured me-2 text-danger"></i>
                                    Traumatismes graves
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Contact d'urgence interne</h5>
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-md fa-3x text-danger mb-3"></i>
                                    <h6>Dr. Sarah Martin</h6>
                                    <p class="text-muted">Médecin de garde</p>
                                    <p><i class="fas fa-phone me-2"></i><strong>06 12 34 56 78</strong></p>
                                    <button class="btn btn-danger w-100">
                                        <i class="fas fa-phone me-2"></i>Appeler maintenant
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
