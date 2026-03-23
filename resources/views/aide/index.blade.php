@extends('layouts.app')

@section('title', 'Aide & Support - SCABINET')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        Centre d'Aide & Support
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Documentation</h5>
                            <div class="list-group mb-4">
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-book me-2"></i>Guide d'utilisation
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-video me-2"></i>Tutoriels vidéo
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-file-pdf me-2"></i>FAQ - Foire aux questions
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Contact Support</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p><i class="fas fa-envelope me-2"></i><strong>Email:</strong> support@scabinet.com</p>
                                    <p><i class="fas fa-phone me-2"></i><strong>Téléphone:</strong> +33 1 23 45 67 90</p>
                                    <p><i class="fas fa-clock me-2"></i><strong>Horaires:</strong> Lun-Ven 9h-18h</p>
                                    <button class="btn btn-primary w-100">
                                        <i class="fas fa-comments me-2"></i>Contacter le support
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
