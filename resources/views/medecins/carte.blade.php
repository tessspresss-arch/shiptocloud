@extends('layouts.app')

@section('title', 'Carte Professionnelle - ' . $medecin->nom_complet)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800">Carte Professionnelle</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('medecins.index') }}">Médecins</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('medecins.show', $medecin) }}">{{ $medecin->matricule }}</a></li>
                    <li class="breadcrumb-item active">Carte</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('medecins.carte.pdf', $medecin) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-download"></i> Télécharger PDF
            </a>
            <a href="{{ route('medecins.show', $medecin) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Aperçu de la carte -->
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Aperçu de la Carte Professionnelle</h5>
                </div>
                <div class="card-body">
                    <div id="carte-professionnelle" class="border p-4 bg-white" style="font-family: 'Times New Roman', serif;">
                        <!-- En-tête -->
                        <div class="text-center mb-4">
                            <h3 class="mb-1" style="color: #2c3e50;">{{ $medecin->civilite }} {{ $medecin->prenom }} {{ $medecin->nom }}</h3>
                            <h5 class="text-muted mb-3">{{ $medecin->specialite ?: 'Médecin Généraliste' }}</h5>
                            <p class="mb-0"><strong>Matricule:</strong> {{ $medecin->matricule }}</p>
                        </div>

                        <div class="row">
                            <!-- Photo -->
                            <div class="col-md-4 text-center mb-3">
                                @if($medecin->photo_path)
                                    <img src="{{ asset('storage/' . $medecin->photo_path) }}" 
                                         alt="{{ $medecin->nom_complet }}" 
                                         class="img-fluid rounded" style="max-width: 120px; max-height: 120px;">
                                @else
                                    <div class="border rounded d-inline-flex align-items-center justify-content-center bg-light"
                                         style="width: 120px; height: 120px;">
                                        <i class="fas fa-user-md fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Informations -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <strong>Spécialité:</strong> {{ $medecin->specialite ?: 'Médecin Généraliste' }}<br>
                                    <strong>Numéro d'ordre:</strong> {{ $medecin->numero_ordre ?: 'Non défini' }}
                                </div>

                                <div class="mb-3">
                                    <strong>Téléphone:</strong> {{ $medecin->telephone ?: 'Non défini' }}<br>
                                    <strong>Email:</strong> {{ $medecin->email ?: 'Non défini' }}
                                </div>

                                @if($medecin->adresse_cabinet || $medecin->ville || $medecin->code_postal)
                                <div class="mb-3">
                                    <strong>Adresse du cabinet:</strong><br>
                                    {{ $medecin->adresse_cabinet }}<br>
                                    @if($medecin->code_postal || $medecin->ville)
                                        {{ $medecin->code_postal }} {{ $medecin->ville }}
                                    @endif
                                </div>
                                @endif

                                @if($medecin->tarif_consultation)
                                <div class="mb-3">
                                    <strong>Tarif consultation:</strong> {{ number_format($medecin->tarif_consultation, 2) }} €
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Signature -->
                        @if($medecin->signature_path)
                        <div class="text-center mt-4">
                            <hr class="my-3">
                            <p class="mb-2"><strong>Signature:</strong></p>
                            <img src="{{ asset('storage/' . $medecin->signature_path) }}" 
                                 alt="Signature" 
                                 class="img-fluid" style="max-height: 60px;">
                        </div>
                        @endif

                        <!-- Pied de page -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <small class="text-muted">
                                Cabinet Médical • {{ now()->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('medecins.carte.pdf', $medecin) }}" 
                               class="btn btn-primary btn-lg w-100" target="_blank">
                                <i class="fas fa-download"></i> Télécharger PDF
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button onclick="window.print()" class="btn btn-secondary btn-lg w-100">
                                <i class="fas fa-print"></i> Imprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #carte-professionnelle, #carte-professionnelle * {
            visibility: visible;
        }
        #carte-professionnelle {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
            border: none;
        }
    }
</style>
@endpush
