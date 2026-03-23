@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1 class="mb-3">Agenda</h1>

    <a href="{{ route('rendezvous.create') }}" class="btn btn-primary">
        Nouveau rendez-vous
    </a>

    <div class="alert alert-info">
        Agenda des rendez-vous – en cours de développement
    </div>
</div>
@endsection
