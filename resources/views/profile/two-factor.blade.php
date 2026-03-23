@extends('layouts.app')

@section('title', 'Sécurité 2FA')

@section('content')
<div class="container py-4" style="max-width: 880px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Authentification à deux facteurs (TOTP)</h1>
            <p class="text-muted mb-0">Recommandé pour tous les comptes, obligatoire pour les administrateurs.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-4">
            <h2 class="h6 mb-3">Statut</h2>
            @if($user->two_factor_enabled && $user->two_factor_confirmed_at)
                <span class="badge bg-success">Activée</span>
                <span class="text-muted small ms-2">confirmée le {{ $user->two_factor_confirmed_at->format('d/m/Y H:i') }}</span>
            @else
                <span class="badge bg-warning text-dark">Non activée</span>
            @endif
        </div>
    </div>

    @if(!($user->two_factor_enabled && $user->two_factor_confirmed_at))
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
                <h2 class="h6 mb-3">Activation</h2>
                <p class="mb-2">1) Ajoutez un compte dans votre application TOTP avec la clé suivante :</p>
                <code class="d-block p-2 rounded bg-light mb-3">{{ $pendingSecret }}</code>
                <p class="mb-2">2) URI de provisioning (copie manuelle) :</p>
                <code class="d-block p-2 rounded bg-light mb-3" style="word-break: break-all;">{{ $provisioningUri }}</code>

                <form method="POST" action="{{ route('profile.2fa.enable') }}" class="row g-2">
                    @csrf
                    <div class="col-md-6">
                        <label for="code" class="form-label">Code de vérification</label>
                        <input id="code" name="code" type="text" pattern="\d{6}" maxlength="6" class="form-control" placeholder="123456" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Activer la 2FA</button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
                <h2 class="h6 mb-3">Codes de secours</h2>
                <div class="row g-2 mb-3">
                    @foreach($recoveryCodes as $code)
                        <div class="col-md-3 col-6"><code class="d-block p-2 rounded bg-light text-center">{{ $code }}</code></div>
                    @endforeach
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <form method="POST" action="{{ route('profile.2fa.recovery') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">Régénérer les codes</button>
                    </form>

                    <form method="POST" action="{{ route('profile.2fa.disable') }}" onsubmit="return confirm('Désactiver la 2FA ?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Désactiver la 2FA</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
