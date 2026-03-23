@extends('layouts.app')

@section('title', 'Test Agenda')

@section('content')
<div class="container">
    <h1>Test Agenda</h1>
    <p>Controller data: {{ count($todayAppointments ?? []) }} appointments</p>
</div>
@endsection
