@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Créer un Compte Administrateur
                    </h4>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nom complet</label>
                            <input id="name" type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}"
                                   required autofocus
                                   placeholder="Dr. Jean Dupont">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Adresse email</label>
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   required
                                   placeholder="admin@cabinet.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mot de passe -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Mot de passe</label>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required
                                   placeholder="Minimum 8 caractères">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Inclure majuscules, chiffres et caractères spéciaux
                            </small>
                        </div>

                        <!-- Confirmation mot de passe -->
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-bold">Confirmer le mot de passe</label>
                            <input id="password-confirm" type="password"
                                   class="form-control"
                                   name="password_confirmation" required
                                   placeholder="Répéter le mot de passe">
                        </div>

                        <!-- Bouton -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Créer le compte
                            </button>
                        </div>

                        <!-- Lien connexion -->
                        <div class="text-center mt-4">
                            <p class="mb-0">
                                Déjà un compte ?
                                <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                                    <i class="fas fa-sign-in-alt"></i> Se connecter
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
