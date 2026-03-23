@extends('layouts.app')

@section('title', 'Vérification 2FA')

@section('content')
<div class="container py-4" style="max-width: 520px;">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <h1 class="h4 mb-2">Vérification à deux facteurs</h1>
            <p class="text-muted mb-4">Entrez le code à 6 chiffres généré par votre application d’authentification.</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.challenge.verify') }}">
                @csrf
                <div class="mb-3">
                    <label for="code" class="form-label">Code 2FA</label>
                    <input id="code" name="code" type="text" inputmode="numeric" pattern="\d{6}" maxlength="6" class="form-control" placeholder="123456" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary w-100">Valider</button>
            </form>
        </div>
    </div>
</div>
@endsection
