@extends('layouts.app')
@section('title', 'Nouveau rendez-vous')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/rdv-create.css') }}">
@endpush

@section('content')
<div class="rdv-create-page" id="rdvCreatePage" data-selected-date="{{ $selectedDate }}" data-selected-time="{{ $selectedTime }}">
    <form id="rdvCreateForm" action="{{ route('rendezvous.store') }}" method="POST" novalidate>
        @csrf

        <input type="hidden" name="date" id="inputDate" value="{{ $selectedDate }}">
        <input type="hidden" name="heure_debut" id="inputTime" value="{{ $selectedTime }}">

        <div class="rdv-page">
            <aside class="rdv-left-panel">
                @include('rendezvous.partials.slot_picker', [
                    'selectedDateObj' => $selectedDateObj,
                    'calendarStart' => $calendarStart,
                    'calendarEnd' => $calendarEnd,
                    'heures' => $heures,
                    'blockedSlots' => $blockedSlots,
                    'selectedTime' => $selectedTime,
                ])
            </aside>

            <section class="rdv-main-panel">
                <header class="rdv-header-card rdv-card">
                    <h1><i class="far fa-calendar-check"></i> Nouveau rendez-vous</h1>
                    <p>Suivez les etapes pour planifier rapidement un rendez-vous.</p>
                </header>

                <div class="rdv-main-grid">
                    <div class="rdv-workflow">
                        <section class="rdv-step rdv-card" data-step>
                            <button type="button" class="rdv-step-toggle" data-accordion-toggle>
                                <span><i class="fas fa-user"></i> Patient</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="rdv-step-body" data-step-body>
                                @include('rendezvous.partials.patient_picker', [
                                    'patients' => $patients,
                                    'selectedPatientId' => $selectedPatientId,
                                    'selectedPatientModel' => $selectedPatientModel,
                                ])
                            </div>
                        </section>

                        <section class="rdv-step rdv-card" data-step>
                            <button type="button" class="rdv-step-toggle" data-accordion-toggle>
                                <span><i class="fas fa-user-md"></i> Medecin</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="rdv-step-body" data-step-body>
                                @include('rendezvous.partials.medecin_card', [
                                    'medecins' => $medecins,
                                    'selectedMedecinId' => $selectedMedecinId,
                                ])
                            </div>
                        </section>

                        <section class="rdv-step rdv-card" data-step>
                            <button type="button" class="rdv-step-toggle" data-accordion-toggle>
                                <span><i class="fas fa-clipboard-list"></i> Motif</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="rdv-step-body" data-step-body>
                                @include('rendezvous.partials.motif_picker', [
                                    'motifs' => $motifs,
                                    'selectedMotif' => $selectedMotif,
                                ])
                            </div>
                        </section>

                        <section class="rdv-step rdv-card" data-step>
                            <button type="button" class="rdv-step-toggle" data-accordion-toggle>
                                <span><i class="fas fa-clock"></i> Infos RDV</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="rdv-step-body" data-step-body>
                                <div class="rdv-inline-fields">
                                    <div>
                                        <label for="displayDate" class="rdv-label">Date selectionnee</label>
                                        <input id="displayDate" type="text" class="rdv-input" readonly value="{{ $selectedDateObj->format('d/m/Y') }}">
                                    </div>
                                    <div>
                                        <label for="displayTime" class="rdv-label">Heure selectionnee</label>
                                        <input id="displayTime" type="text" class="rdv-input" readonly value="{{ $selectedTime }}">
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="rdv-step rdv-card" data-step>
                            <button type="button" class="rdv-step-toggle" data-accordion-toggle>
                                <span><i class="fas fa-note-sticky"></i> Notes</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="rdv-step-body" data-step-body>
                                <label class="rdv-label" for="notes">Informations complementaires</label>
                                <textarea
                                    id="notes"
                                    name="notes"
                                    class="rdv-textarea"
                                    rows="4"
                                    placeholder="Ajoutez des informations utiles pour ce rendez-vous..."
                                >{{ old('notes') }}</textarea>
                            </div>
                        </section>

                        @include('rendezvous.partials.actions')
                    </div>
                </div>
            </section>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/rdv-create.js') }}"></script>
@endpush
