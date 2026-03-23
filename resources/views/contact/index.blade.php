@extends('layouts.app')

@section('title', 'Contact - SCABINET')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-address-book me-2"></i>
                        Contact & Support
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informations de contact</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p><i class="fas fa-map-marker-alt me-2"></i><strong>Adresse:</strong><br>
                                    123 Avenue de la Santé<br>
                                    75001 Paris, France</p>

                                    <p><i class="fas fa-phone me-2"></i><strong>Téléphone:</strong><br>
                                    +33 1 23 45 67 90</p>

                                    <p><i class="fas fa-envelope me-2"></i><strong>Email:</strong><br>
                                    contact@scabinet.com</p>

                                    <p><i class="fas fa-clock me-2"></i><strong>Horaires d'ouverture:</strong><br>
                                    Lun-Ven: 8h00 - 18h00<br>
                                    Sam: 9h00 - 12h00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Envoyez-nous un message</h5>
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Nom complet</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sujet</label>
                                    <select class="form-control" required>
                                        <option>Support technique</option>
                                        <option>Demande d'information</option>
                                        <option>Signalement de bug</option>
                                        <option>Autre</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
