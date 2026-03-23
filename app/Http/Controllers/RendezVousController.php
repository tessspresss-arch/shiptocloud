<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\RendezVousStatusHistory;
use App\Models\Patient;
use App\Models\Medecin;
use App\Services\RendezVous\RendezVousAgendaViewService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class RendezVousController extends Controller
{
    public function __construct(private readonly RendezVousAgendaViewService $agendaViewService)
    {
    }

    private const STATUS_A_VENIR = 'a_venir';
    private const STATUS_EN_ATTENTE = 'en_attente';
    private const STATUS_EN_SOINS = 'en_soins';
    private const STATUS_VU = 'vu';
    private const STATUS_ABSENT = 'absent';
    private const STATUS_ANNULE = 'annule';

    private static function allowedStatuses(): array
    {
        return [
            self::STATUS_A_VENIR,
            self::STATUS_EN_ATTENTE,
            self::STATUS_EN_SOINS,
            self::STATUS_VU,
            self::STATUS_ABSENT,
            self::STATUS_ANNULE,
        ];
    }

    /**
     * Afficher la liste des rendez-vous (page index)
     */
    public function index(Request $request)
    {
        $perPage = max(10, min(100, (int) $request->integer('per_page', 20)));
        $selectedStatut = RendezVous::normalizeStatus($request->input('statut'));
        $selectedType = trim(mb_strtolower((string) $request->input('type', ''), 'UTF-8'));

        $rendezvous = RendezVous::with([
                'patient:id,nom,prenom,telephone',
                'medecin:id,nom,specialite',
            ])
            ->select([
                'id',
                'patient_id',
                'medecin_id',
                'date_heure',
                'duree',
                'type',
                'statut',
                'created_at',
            ])
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = Carbon::parse((string) $request->input('date'), $this->appTimezone());
                $query->whereBetween('date_heure', $this->dayBounds($date));
            })
            ->when($request->filled('patient_id'), function ($query) use ($request) {
                $query->where('patient_id', $request->input('patient_id'));
            })
            ->when($request->filled('medecin_id'), function ($query) use ($request) {
                $query->where('medecin_id', $request->input('medecin_id'));
            })
            ->when($selectedStatut, function ($query) use ($selectedStatut) {
                $query->where('statut', $selectedStatut);
            })
            ->when($selectedType !== '', function ($query) use ($selectedType) {
                $query->where(function ($inner) use ($selectedType) {
                    $inner->whereRaw('LOWER(type) like ?', ['%' . $selectedType . '%'])
                        ->orWhereRaw('LOWER(motif) like ?', ['%' . $selectedType . '%']);
                });
            })
            ->orderBy('date_heure', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $rendezvous->getCollection()->transform(function (RendezVous $rdv) {
            [$statusClass, $statusLabel, $statusIcon] = $this->resolveRendezVousStatusPresentation((string) $rdv->statut);
            $rdv->status_class = $statusClass;
            $rdv->status_label = $statusLabel;
            $rdv->status_icon = $statusIcon;

            return $rdv;
        });

        $patients = Patient::query()
            ->select('id', 'nom', 'prenom')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        $medecins = Medecin::query()
            ->select('id', 'nom', 'prenom')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('rendezvous.index', compact('rendezvous', 'patients', 'medecins'));
    }

    /**
     * Afficher l'agenda complet (page agenda)
     */
    public function agenda(Request $request)
    {
        $requestedView = (string) $request->get('view', 'day');
        $currentView = in_array($requestedView, ['day', 'week', 'month'], true)
            ? $requestedView
            : ($requestedView === 'dense' ? 'week' : 'day');
        $weekLayout = ($currentView === 'week' && ($requestedView === 'dense' || $request->get('layout') === 'dense'))
            ? 'dense'
            : 'standard';

        try {
            $selectedDate = Carbon::parse(
                $request->get('date', now($this->appTimezone())->toDateString()),
                $this->appTimezone()
            )->startOfDay();
        } catch (\Throwable $e) {
            $selectedDate = now($this->appTimezone())->startOfDay();
        }

        $selectedMedecinId = $request->get('medecin_id');
        $selectedStatut = RendezVous::normalizeStatus($request->get('statut'));
        $searchTerm = trim((string) $request->get('search', ''));

        return view('agenda.index', $this->agendaViewService->build(
            $currentView,
            $weekLayout,
            $selectedDate,
            $selectedMedecinId,
            $selectedStatut,
            $searchTerm
        ));
    }

    private function resolveDenseStatusPresentation(string $status): array
    {
        return match ($status) {
            self::STATUS_EN_ATTENTE => ['dense-status-waiting', 'En attente'],
            self::STATUS_EN_SOINS => ['dense-status-active', 'En consultation'],
            self::STATUS_VU => ['dense-status-done', 'Termine'],
            self::STATUS_ABSENT => ['dense-status-absent', 'Absent'],
            self::STATUS_ANNULE => ['dense-status-cancelled', 'Annule'],
            default => ['dense-status-upcoming', 'A venir'],
        };
    }

    private function resolveDenseActPresentation(string $rawActLower, string $status, string $fallbackLabel): array
    {
        if ($status === self::STATUS_ABSENT) {
            return ['dense-act-absence', 'Absence'];
        }

        if (str_contains($rawActLower, 'premiere consultation') || str_contains($rawActLower, 'première consultation')) {
            return ['dense-act-first', 'Premiere consultation'];
        }

        if (str_contains($rawActLower, 'bilan')) {
            return ['dense-act-bilan', 'Bilan'];
        }

        if (str_contains($rawActLower, 'suivi')) {
            return ['dense-act-followup', 'Suivi'];
        }

        if (str_contains($rawActLower, 'injection')) {
            return ['dense-act-injection', 'Injection'];
        }

        if (str_contains($rawActLower, 'chimio')) {
            return ['dense-act-chimio', 'Chimio'];
        }

        if (str_contains($rawActLower, 'scan')) {
            return ['dense-act-scan', 'Scan'];
        }

        if (str_contains($rawActLower, 'consult')) {
            return ['dense-act-consultation', 'Consultation'];
        }

        return ['dense-act-consultation', $fallbackLabel !== '' ? $fallbackLabel : 'Consultation'];
    }

    private function agendaAppointmentsQuery()
    {
        return RendezVous::query()
            ->select([
                'id',
                'patient_id',
                'medecin_id',
                'date_heure',
                'duree',
                'type',
                'motif',
                'statut',
                'arrived_at',
                'consultation_started_at',
            ])
            ->with([
                'patient:id,nom,prenom,photo,numero_dossier',
                'medecin:id,nom,prenom,specialite',
            ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
    {
        $medecins = Medecin::select('id', 'nom', 'prenom', 'specialite')->actif()->get();

        // Vérifier que des médecins sont disponibles
        if ($medecins->isEmpty()) {
            return redirect()->route('agenda.index')
                ->with('warning', 'Aucun medecin actif n\'est configure. Veuillez contacter l\'administrateur.');
        }

        $selectedPatientId = $request->integer('patient_id');
        $patients = Patient::select('id', 'nom', 'prenom', 'telephone', 'cin', 'date_naissance')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        $selectedPatient = $selectedPatientId
            ? $patients->firstWhere('id', $selectedPatientId)
            : null;

        $selectedDate = old('date', $request->get('date', now($this->appTimezone())->format('Y-m-d')));
        $selectedTime = old('heure_debut', $request->get('heure', '09:00'));
        $selectedDateObj = Carbon::parse($selectedDate, $this->appTimezone());
        $calendarStart = $selectedDateObj->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $selectedDateObj->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        $selectedMedecinId = (int) old('medecin_id');
        $selectedMotif = old('motif');
        $motifs = $this->rendezVousMotifs();
        $heures = $this->rendezVousHeures();
        $blockedSlots = ['12:00', '12:30', '13:00', '13:30'];
        $selectedPatientModel = $selectedPatient ?? ($patients->firstWhere('id', $selectedPatientId) ?? null);

        return view('rendezvous.create', [
            'date_preselectionnee' => $request->get('date', now($this->appTimezone())->format('Y-m-d')),
            'heure_preselectionnee' => $request->get('heure', '09:00'),
            'selectedPatientId' => $selectedPatientId,
            'selectedPatient' => $selectedPatient,
            'patients' => $patients,
            'medecins' => $medecins,
            'selectedDate' => $selectedDate,
            'selectedTime' => $selectedTime,
            'selectedDateObj' => $selectedDateObj,
            'calendarStart' => $calendarStart,
            'calendarEnd' => $calendarEnd,
            'selectedMedecinId' => $selectedMedecinId,
            'selectedMotif' => $selectedMotif,
            'motifs' => $motifs,
            'heures' => $heures,
            'blockedSlots' => $blockedSlots,
            'selectedPatientModel' => $selectedPatientModel,
        ]);
    }

    /**
     * Enregistrer un nouveau rendez-vous
     */
    public function store(Request $request)
    {
        $normalizedDate = $this->normalizeDateInput($request->input('date'));
        if ($normalizedDate === null) {
            return back()
                ->withInput()
                ->withErrors(['date' => 'La date selectionnee est invalide.']);
        }

        $today = now($this->appTimezone())->startOfDay();
        if ($normalizedDate->copy()->startOfDay()->lt($today)) {
            return back()
                ->withInput()
                ->withErrors(['date' => 'La date doit etre egale ou posterieure au ' . $today->format('d/m/Y') . '.']);
        }

        $request->merge([
            'date' => $normalizedDate->format('Y-m-d'),
        ]);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'date' => 'required|date_format:Y-m-d',
            'heure_debut' => 'required|date_format:H:i',
            'motif' => 'required|string|max:255',
        ], [
            'date.date_format' => 'La date selectionnee est invalide.',
        ]);

        // Valeurs par defaut pour la nouvelle interface simplifiee
        $duree = 30; // Duree fixe de 30 minutes
        $type = "Consultation g\u{00E9}n\u{00E9}rale"; // Type par defaut

        // Combiner date et heure
        $dateHeure = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['date'] . ' ' . $validated['heure_debut'],
            $this->appTimezone()
        )->setTimezone($this->appTimezone());

        // Vérifier les conflits
        $conflict = RendezVous::where('medecin_id', $validated['medecin_id'])
            ->where('date_heure', '<', $dateHeure->copy()->addMinutes($duree))
            ->whereRaw('DATE_ADD(date_heure, INTERVAL duree MINUTE) > ?', [$dateHeure])
            ->where('statut', '!=', self::STATUS_ANNULE)
            ->exists();

        if ($conflict) {
            if (!$request->expectsJson()) {
                $nextAvailableSlot = $this->findNextAvailableSameDaySlot(
                    (int) $validated['medecin_id'],
                    $normalizedDate,
                    $dateHeure,
                    $duree
                );

                if ($nextAvailableSlot !== null) {
                    $dateHeure = $nextAvailableSlot;
                } else {
                    return back()->withErrors(['heure_debut' => 'Ce creneau horaire est deja occupe.'])->withInput();
                }
            } else {
                return back()->withErrors(['heure_debut' => 'Ce creneau horaire est deja occupe.'])->withInput();
            }
        }

        $rendezvous = RendezVous::create(array_merge($validated, [
            'date_heure' => $dateHeure,
            'duree' => $duree,
            'type' => $type,
            'statut' => self::STATUS_A_VENIR
        ]));

        if ($request->expectsJson()) {
            // Retourner l'événement au format FullCalendar pour affichage immédiat
            $event = $this->formatEventForCalendar($rendezvous->load(['patient', 'medecin']));

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous cree avec succes !',
                'event' => $event
            ]);
        }

        return redirect()->route('rendezvous.index')
            ->with(
                $conflict ? 'warning' : 'success',
                $conflict
                    ? 'Le creneau demande etait occupe. Le rendez-vous a ete repositionne automatiquement a ' . $dateHeure->format('H:i') . '.'
                    : 'Rendez-vous cree avec succes !'
            );
    }

    /**
     * Afficher un rendez-vous spécifique
     */
    public function show(Request $request, $id)
    {
        $rendezvous = RendezVous::with(['patient', 'medecin'])->findOrFail($id);

        [$badgeClass, $statusLabel, $statusIcon] = $this->resolveRendezVousStatusPresentation((string) $rendezvous->statut);
        $patientName = trim(($rendezvous->patient->prenom ?? '') . ' ' . ($rendezvous->patient->nom ?? ''));
        $patientName = $patientName !== '' ? $patientName : 'Patient inconnu';
        $medecinName = trim(($rendezvous->medecin->nom ?? '') . ' ' . ($rendezvous->medecin->prenom ?? ''));
        $medecinName = $medecinName !== '' ? $medecinName : 'Medecin inconnu';
        $patientInitials = collect(explode(' ', $patientName))->filter()->take(2)->map(fn ($p) => strtoupper(substr($p, 0, 1)))->implode('');
        $medecinInitials = collect(explode(' ', $medecinName))->filter()->take(2)->map(fn ($p) => strtoupper(substr($p, 0, 1)))->implode('');
        $appointmentDate = optional($rendezvous->date_heure);
        $createdAt = optional($rendezvous->created_at);
        $updatedAt = optional($rendezvous->updated_at);
        $patientEmail = $rendezvous->patient->email ?? 'Email non renseigne';
        $patientTelephone = $rendezvous->patient->telephone ?? 'Telephone non renseigne';
        $medecinEmail = $rendezvous->medecin->email ?? 'Email non renseigne';
        $medecinTelephone = $rendezvous->medecin->telephone ?? 'Telephone non renseigne';
        $medecinSpecialite = $rendezvous->medecin->specialite ?? 'Specialite non renseignee';

        if ($request->expectsJson()) {
            return response()->json($rendezvous);
        }

        return view('rendezvous.show', compact(
            'rendezvous',
            'badgeClass',
            'statusLabel',
            'statusIcon',
            'patientName',
            'medecinName',
            'patientInitials',
            'medecinInitials',
            'appointmentDate',
            'createdAt',
            'updatedAt',
            'patientEmail',
            'patientTelephone',
            'medecinEmail',
            'medecinTelephone',
            'medecinSpecialite'
        ));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit($id)
    {
        $rendezvous = RendezVous::with([
            'patient:id,nom,prenom,email,telephone',
            'medecin:id,nom,prenom,specialite,email',
        ])->findOrFail($id);
        $oldStatus = RendezVous::normalizeStatus($rendezvous->statut) ?? self::STATUS_A_VENIR;
        $patients = Patient::query()
            ->select(['id', 'nom', 'prenom'])
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        $medecins = Medecin::query()
            ->select(['id', 'nom', 'prenom', 'specialite'])
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        $patientName = trim(($rendezvous->patient->prenom ?? '') . ' ' . ($rendezvous->patient->nom ?? ''));
        $patientName = $patientName !== '' ? $patientName : 'Patient inconnu';
        $medecinName = trim(($rendezvous->medecin->nom ?? '') . ' ' . ($rendezvous->medecin->prenom ?? ''));
        $medecinName = $medecinName !== '' ? $medecinName : 'Medecin inconnu';
        $patientInitials = collect(explode(' ', $patientName))->filter()->take(2)->map(fn ($p) => strtoupper(substr($p, 0, 1)))->implode('');
        $medecinInitials = collect(explode(' ', $medecinName))->filter()->take(2)->map(fn ($p) => strtoupper(substr($p, 0, 1)))->implode('');

        return view('rendezvous.edit', compact(
            'rendezvous',
            'patients',
            'medecins',
            'patientName',
            'medecinName',
            'patientInitials',
            'medecinInitials'
        ));
    }

    /**
     * Mettre à jour un rendez-vous
     */
    public function update(Request $request, $id)
    {
        $rendezvous = RendezVous::findOrFail($id);
        $oldStatus = RendezVous::normalizeStatus($rendezvous->statut) ?? self::STATUS_A_VENIR;

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'date_heure' => 'required|date',
            'duree' => 'required|integer|min:15|max:240',
            'type' => 'required|string|max:50',
            'motif' => 'required|string|max:255',
            'statut' => 'required|string|max:50',
            'notes' => 'nullable|string'
        ]);

        $normalizedStatus = RendezVous::normalizeStatus($validated['statut']);
        if (!$normalizedStatus || !in_array($normalizedStatus, self::allowedStatuses(), true)) {
            throw ValidationException::withMessages([
                'statut' => 'Le statut selectionne est invalide.',
            ]);
        }

        $dateHeure = Carbon::parse($validated['date_heure'], $this->appTimezone())
            ->setTimezone($this->appTimezone());

        if ($normalizedStatus !== self::STATUS_ANNULE) {
            $conflict = RendezVous::query()
                ->where('id', '!=', $rendezvous->id)
                ->where('medecin_id', $validated['medecin_id'])
                ->where('date_heure', '<', $dateHeure->copy()->addMinutes((int) $validated['duree']))
                ->whereRaw('DATE_ADD(date_heure, INTERVAL duree MINUTE) > ?', [$dateHeure->format('Y-m-d H:i:s')])
                ->where('statut', '!=', self::STATUS_ANNULE)
                ->exists();

            if ($conflict) {
                $message = 'Ce creneau horaire est deja occupe.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $message,
                        'errors' => ['date_heure' => [$message]],
                    ], 422);
                }

                return back()->withErrors(['date_heure' => $message])->withInput();
            }
        }

        $validated['date_heure'] = $dateHeure->format('Y-m-d H:i:s');
        $validated['statut'] = $normalizedStatus;

        $rendezvous->fill($validated);

        if ($oldStatus !== $normalizedStatus) {
            $transitionedAt = now($this->appTimezone());
            $this->applyWaitingRoomTransitionTimestamps($rendezvous, $normalizedStatus, $transitionedAt);
        }

        $rendezvous->save();

        if ($oldStatus !== $normalizedStatus) {
            $this->logStatusTransition(
                $rendezvous->id,
                $oldStatus,
                $normalizedStatus,
                auth()->id(),
                'edit_form',
                now($this->appTimezone())
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous mis a jour avec succes !',
                'rendezvous' => $rendezvous
            ]);
        }

        return redirect()->route('rendezvous.show', $rendezvous->id)
            ->with('success', 'Rendez-vous mis a jour avec succes !');
    }

    /**
     * Supprimer un rendez-vous
     */
    public function destroy(Request $request, $id)
    {
        $rendezvous = RendezVous::findOrFail($id);
        $rendezvous->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous supprime avec succes !'
            ]);
        }

        return redirect()->route('rendezvous.index')
            ->with('success', 'Rendez-vous supprime avec succes !');
    }

    /**
     * API pour FullCalendar
     */
    public function apiEvents(Request $request)
    {
        $timezone = $this->appTimezone();

        $query = RendezVous::with(['patient' => function($q) {
            $q->where('is_draft', false); // Exclude draft patients
        }, 'medecin' => function($q) {
            $q->withTrashed(); // Include soft deleted doctors
        }]);

        $medecinId = $request->get('medecin_id');
        if ($medecinId && $medecinId !== 'all') {
            $query->where('medecin_id', $medecinId);
        }

        $status = RendezVous::normalizeStatus($request->get('statut', $request->get('status')));
        if ($status && $status !== 'all') {
            $query->byStatut($status);
        }

        $searchTerm = trim((string) $request->get('search', ''));
        if ($searchTerm !== '') {
            $query->whereHas('patient', function ($patientQuery) use ($searchTerm) {
                $patientQuery
                    ->where('nom', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('prenom', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('telephone', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->get('today') == '1') {
            // Mode dashboard: seulement les RDV du jour
            $query->whereBetween('date_heure', $this->dayBounds(now($timezone)));
        } else {
            // Mode agenda normal: plage de dates
            $start = $request->get('start');
            $end = $request->get('end');

            try {
                if ($start && $end) {
                    $startDate = Carbon::parse($start, $timezone)->setTimezone($timezone);
                    $endDate = Carbon::parse($end, $timezone)->setTimezone($timezone);

                    if ($endDate->lessThanOrEqualTo($startDate)) {
                        $endDate = $startDate->copy()->addDay();
                    }

                    $query->whereBetween('date_heure', [
                        $startDate->toDateTimeString(),
                        $endDate->toDateTimeString(),
                    ]);
                } else {
                    $day = Carbon::parse($request->get('date', now($timezone)->toDateString()), $timezone);
                    $query->whereBetween('date_heure', [
                        $day->copy()->startOfDay()->toDateTimeString(),
                        $day->copy()->endOfDay()->toDateTimeString(),
                    ]);
                }
            } catch (\Throwable $e) {
                $today = now($timezone);
                $query->whereBetween('date_heure', [
                    $today->copy()->startOfDay()->toDateTimeString(),
                    $today->copy()->endOfDay()->toDateTimeString(),
                ]);
            }
        }

        $events = $query
            ->orderBy('date_heure')
            ->get()
            ->map(fn (RendezVous $rdv) => $this->formatEventForCalendar($rdv))
            ->filter()
            ->values();

        return response()->json($events);
    }

    /**
     * API pour les statistiques
     */
    public function statistiques()
    {
        try {
            $today = Carbon::today();
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            $data = [
                'today' => RendezVous::whereBetween('date_heure', $this->dayBounds($today))->count(),
                'week' => RendezVous::whereBetween('date_heure', [$weekStart, $weekEnd])->count(),
                'pending' => RendezVous::whereIn('statut', [self::STATUS_A_VENIR, self::STATUS_EN_ATTENTE])->count(),
                'confirmed' => RendezVous::where('statut', self::STATUS_EN_SOINS)->count(),
                'cancelled' => RendezVous::where('statut', self::STATUS_ANNULE)->count(),
                'completed' => RendezVous::where('statut', self::STATUS_VU)->count()
            ];

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => "Erreur lors de la r\u{00E9}cup\u{00E9}ration des statistiques",
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Formater un rendez-vous pour FullCalendar
     */
    private function formatEventForCalendar(RendezVous $rdv)
    {
        $start = $rdv->date_heure ?? $rdv->date_rdv;
        if (!$start) {
            return null;
        }

        $startDate = $start instanceof Carbon
            ? $start->copy()->setTimezone($this->appTimezone())
            : Carbon::parse($start, $this->appTimezone())->setTimezone($this->appTimezone());

        $patientName = $rdv->patient ? (trim($rdv->patient->prenom . ' ' . $rdv->patient->nom)) : 'Patient inconnu';
        $patientName = $patientName ?: 'Patient inconnu';
        $status = RendezVous::normalizeStatus($rdv->statut) ?? self::STATUS_A_VENIR;

        $medecinName = $rdv->medecin ? trim($rdv->medecin->nom) : "M\u{00E9}decin inconnu";
        $medecinName = $medecinName ?: "M\u{00E9}decin inconnu";
        // Handle cases where name is "ERR" or invalid
        if (strtoupper($medecinName) === 'ERR' || $medecinName === "M\u{00E9}decin inconnu") {
            $medecinName = "M\u{00E9}decin inconnu";
        }

        return [
            'id' => $rdv->id,
            'title' => $patientName . ' - ' . $rdv->type,
            'start' => $startDate->toIso8601String(),
            'end' => $startDate->copy()->addMinutes((int) $rdv->duree)->toIso8601String(),
            'color' => $this->getEventColor($status),
            'extendedProps' => [
                'patient' => $patientName,
                'patient_id' => $rdv->patient_id,
                'medecin' => $medecinName,
                'medecin_id' => $rdv->medecin_id,
                'type' => $rdv->type,
                'statut' => $status,
                'duree' => $rdv->duree,
                'motif' => $rdv->motif
            ]
        ];
    }

    /**
     * Recherche de patients pour autocomplete
     */
    public function searchPatients(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);

        $patients = Patient::where(function ($q) use ($query) {
            $q->where('nom', 'LIKE', "%{$query}%")
              ->orWhere('prenom', 'LIKE', "%{$query}%")
              ->orWhere('telephone', 'LIKE', "%{$query}%")
              ->orWhere('cin', 'LIKE', "%{$query}%");
        })
        ->limit($limit)
        ->get()
        ->map(function ($patient) {
            return [
                'id' => $patient->id,
                'text' => $patient->nom . ' ' . $patient->prenom . ' (' . ($patient->cin ?: $patient->telephone) . ')',
                'nom' => $patient->nom,
                'prenom' => $patient->prenom,
                'telephone' => $patient->telephone,
                'cin' => $patient->cin,
                'date_naissance' => $patient->date_naissance?->format('d/m/Y'),
                'age' => $patient->date_naissance?->age,
                'genre' => $patient->genre,
                'adresse' => $patient->adresse,
                'ville' => $patient->ville
            ];
        });

        return response()->json($patients);
    }

    /**
     * Obtenir la disponibilité d'un médecin
     */
    public function getDoctorAvailability(Request $request)
    {
        $medecinId = $request->get('medecin_id');
        $date = $request->get('date');

        $medecin = Medecin::findOrFail($medecinId);

        // Vérifier si le médecin travaille ce jour
        $jourSemaine = strtolower(Carbon::parse($date, $this->appTimezone())->format('l'));
        $horaires = $medecin->horaires_travail[$jourSemaine] ?? null;

        if (!$horaires) {
            return response()->json(['available' => false, 'message' => "M\u{00E9}decin non disponible ce jour"]);
        }

        // Générer les créneaux disponibles
        $debut = Carbon::parse($date . ' ' . $horaires['debut'], $this->appTimezone());
        $fin = Carbon::parse($date . ' ' . $horaires['fin'], $this->appTimezone());
        $dureeDefaut = 30; // minutes

        $slots = [];
        $current = $debut->copy();

        while ($current->lt($fin)) {
            $slotEnd = $current->copy()->addMinutes($dureeDefaut);

            // Vérifier si ce créneau est libre
            $conflict = RendezVous::where('medecin_id', $medecinId)
                ->where('date_heure', '<', $slotEnd)
                ->whereRaw('DATE_ADD(date_heure, INTERVAL duree MINUTE) > ?', [$current])
                ->where('statut', '!=', self::STATUS_ANNULE)
                ->exists();

            $slots[] = [
                'time' => $current->format('H:i'),
                'available' => !$conflict,
                'datetime' => $current->toISOString()
            ];

            $current->addMinutes($dureeDefaut);
        }

        return response()->json([
            'available' => true,
            'horaires' => $horaires,
            'slots' => $slots
        ]);
    }

    /**
     * Obtenir les types de consultation configurables
     */
    public function getConsultationTypes()
    {
        $types = \App\Models\Setting::get('consultation_types', [
            "Consultation g\u{00E9}n\u{00E9}rale" => ['duree' => 30, 'couleur' => '#4A90E2'],
            "Consultation sp\u{00E9}cialis\u{00E9}e" => ['duree' => 45, 'couleur' => '#50C878'],
            'Suivi' => ['duree' => 15, 'couleur' => '#F39C12'],
            'Urgence' => ['duree' => 60, 'couleur' => '#E74C3C'],
            "Contr\u{00F4}le" => ['duree' => 20, 'couleur' => '#9B59B6'],
            'Autre' => ['duree' => 30, 'couleur' => '#95A5A6']
        ]);

        return response()->json($types);
    }

    /**
     * Suggérer le prochain créneau disponible
     */
    public function suggestNextSlot(Request $request)
    {
        $medecinId = $request->get('medecin_id');
        $date = $request->get('date', Carbon::today($this->appTimezone())->format('Y-m-d'));
        $type = $request->get('type', "Consultation g\u{00E9}n\u{00E9}rale");

        $medecin = Medecin::findOrFail($medecinId);

        // Durée suggérée selon le type
        $types = $this->getConsultationTypes()->getData();
        $duree = $types[$type]['duree'] ?? 30;

        // Chercher le prochain créneau disponible
        $startDate = Carbon::parse($date, $this->appTimezone());
        $maxDays = 7; // Chercher sur 7 jours maximum

        for ($i = 0; $i < $maxDays; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $jourSemaine = strtolower($currentDate->format('l'));
            $horaires = $medecin->horaires_travail[$jourSemaine] ?? null;

            if (!$horaires) continue;

            $debut = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $horaires['debut'], $this->appTimezone());
            $fin = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $horaires['fin'], $this->appTimezone());

            // Si c'est aujourd'hui, commencer à partir de maintenant
            if ($i === 0) {
                $now = Carbon::now($this->appTimezone());
                if ($debut->lt($now)) {
                    $debut = $now->copy()->addMinutes(15); // Arrondir au quart d'heure suivant
                    $debut->minute(floor($debut->minute / 15) * 15);
                }
            }

            $current = $debut->copy();

            while ($current->lt($fin)) {
                $slotEnd = $current->copy()->addMinutes($duree);

                if ($slotEnd->gt($fin)) break;

                // Vérifier si ce créneau est libre
                $conflict = RendezVous::where('medecin_id', $medecinId)
                    ->where('date_heure', '<', $slotEnd)
                    ->whereRaw('DATE_ADD(date_heure, INTERVAL duree MINUTE) > ?', [$current])
                    ->where('statut', '!=', self::STATUS_ANNULE)
                    ->exists();

                if (!$conflict) {
                    return response()->json([
                        'date' => $currentDate->format('Y-m-d'),
                        'heure' => $current->format('H:i'),
                        'duree' => $duree,
                        'datetime' => $current->toISOString()
                    ]);
                }

                $current->addMinutes(15); // Vérifier toutes les 15 minutes
            }
        }

        return response()->json(['error' => 'Aucun creneau disponible trouve'], 404);
    }

    /**
     * Obtenir la couleur selon le statut
     */
    private function appTimezone(): string
    {
        $configured = \App\Models\Setting::get('timezone', config('app.timezone', 'UTC'));
        if (is_string($configured) && in_array($configured, timezone_identifiers_list(), true)) {
            return $configured;
        }

        return config('app.timezone', 'UTC');
    }

    private function normalizeDateInput($value): ?Carbon
    {
        $date = trim((string) $value);
        if ($date === '') {
            return null;
        }

        $timezone = $this->appTimezone();

        try {
            return Carbon::createFromFormat('Y-m-d', $date, $timezone)->startOfDay();
        } catch (\Throwable $e) {
            // continue
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $date, $timezone)->startOfDay();
        } catch (\Throwable $e) {
            // continue
        }

        return null;
    }

    private function findNextAvailableSameDaySlot(int $medecinId, Carbon $selectedDate, Carbon $requestedStart, int $duree): ?Carbon
    {
        $medecin = Medecin::find($medecinId);
        if (!$medecin) {
            return null;
        }

        $jourSemaine = strtolower($selectedDate->copy()->setTimezone($this->appTimezone())->format('l'));
        $horaires = is_array($medecin->horaires_travail ?? null)
            ? ($medecin->horaires_travail[$jourSemaine] ?? null)
            : null;

        $slotStart = $requestedStart->copy()->addMinutes(15);
        $slotStart->minute((int) (floor($slotStart->minute / 15) * 15));
        $slotStart->second(0);

        $dayStart = Carbon::parse(
            $selectedDate->format('Y-m-d') . ' ' . ($horaires['debut'] ?? '08:00'),
            $this->appTimezone()
        );
        $dayEnd = Carbon::parse(
            $selectedDate->format('Y-m-d') . ' ' . ($horaires['fin'] ?? '19:00'),
            $this->appTimezone()
        );

        if ($slotStart->lt($dayStart)) {
            $slotStart = $dayStart->copy();
        }

        while ($slotStart->copy()->addMinutes($duree)->lte($dayEnd)) {
            $slotEnd = $slotStart->copy()->addMinutes($duree);
            $conflict = RendezVous::query()
                ->where('medecin_id', $medecinId)
                ->where('date_heure', '<', $slotEnd)
                ->whereRaw('DATE_ADD(date_heure, INTERVAL duree MINUTE) > ?', [$slotStart])
                ->where('statut', '!=', self::STATUS_ANNULE)
                ->exists();

            if (!$conflict) {
                return $slotStart;
            }

            $slotStart->addMinutes(15);
        }

        return null;
    }

    private function getEventColor($statut)
    {
        $status = RendezVous::normalizeStatus(is_string($statut) ? $statut : null) ?? self::STATUS_A_VENIR;

        $colors = [
            self::STATUS_A_VENIR => '#4A90E2',
            self::STATUS_EN_ATTENTE => '#F59E0B',
            self::STATUS_EN_SOINS => '#50C878',
            self::STATUS_VU => '#95A5A6',
            self::STATUS_ABSENT => '#FB923C',
            self::STATUS_ANNULE => '#FF6B6B',
        ];

        return $colors[$status] ?? '#95A5A6';
    }

    /**
     * API JSON pour la gestion de la salle d'attente (flux des patients)
     */
    public function waitingRoomData(Request $request)
    {
        $date = $request->get('date', now($this->appTimezone())->toDateString());
        $medecinId = $request->get('medecin_id');
        $motif = trim((string) $request->get('motif', ''));
        $search = trim((string) $request->get('search', ''));
        $statusFilter = RendezVous::normalizeStatus($request->get('status'));

        $query = RendezVous::with([
                'patient:id,nom,prenom,telephone,cin,numero_dossier,photo',
                'medecin:id,nom,prenom,specialite',
                'consultation:id,rendez_vous_id',
            ])
            ->whereDate('date_heure', $date)
            ->where('statut', '!=', self::STATUS_ANNULE);

        if ($medecinId && $medecinId !== 'all') {
            $query->where('medecin_id', $medecinId);
        }

        if ($motif !== '') {
            $query->where('motif', 'like', "%{$motif}%");
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('motif', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery
                            ->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%")
                            ->orWhere('cin', 'like', "%{$search}%")
                            ->orWhere('numero_dossier', 'like', "%{$search}%");
                    })
                    ->orWhereHas('medecin', function ($medecinQuery) use ($search) {
                        $medecinQuery
                            ->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                            ->orWhere('specialite', 'like', "%{$search}%");
                    });
            });
        }

        $all = $query->orderBy('date_heure')->get();
        $now = now($this->appTimezone());

        $grouped = [
            'a_venir' => [],
            'en_attente' => [],
            'en_soins' => [],
            'vu' => [],
            'absent' => [],
        ];
        $selectedByPatient = [];

        foreach ($all as $rdv) {
            $status = $this->waitingRoomStatusKey($rdv->statut);
            $normalizedStatus = RendezVous::normalizeStatus($rdv->statut);

            if ($statusFilter !== null && $normalizedStatus !== $statusFilter) {
                continue;
            }

            $patientName = $rdv->patient ? trim(($rdv->patient->prenom ? $rdv->patient->prenom . ' ' : '') . $rdv->patient->nom) : 'Patient inconnu';
            $medecinName = $rdv->medecin ? trim(($rdv->medecin->prenom ? $rdv->medecin->prenom . ' ' : '') . $rdv->medecin->nom) : 'Medecin non assigne';
            $scheduledAt = $rdv->date_heure ? $rdv->date_heure->copy()->setTimezone($this->appTimezone()) : null;
            $waitingMinutes = 0;
            $remainingMinutes = null;
            if ($scheduledAt !== null) {
                $minutesUntil = (int) $now->diffInMinutes($scheduledAt, false);
                $remainingMinutes = max(0, $minutesUntil);
                $waitingMinutes = max(0, -$minutesUntil);
            }

            $item = [
                'id' => $rdv->id,
                'patient_id' => $rdv->patient_id,
                'medecin_id' => $rdv->medecin_id,
                'consultation_id' => $rdv->consultation?->id,
                'patient' => $patientName,
                'patient_initials' => mb_strtoupper((string) collect(explode(' ', $patientName))
                    ->filter()
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->take(2)
                    ->implode(''), 'UTF-8'),
                'patient_photo_url' => $this->resolvePatientPhotoUrl($rdv->patient),
                'patient_cin' => $rdv->patient?->cin,
                'patient_dossier' => $rdv->patient?->numero_dossier,
                'medecin' => $medecinName,
                'motif' => $rdv->motif,
                'type' => $rdv->type,
                'heure' => $rdv->date_heure ? $rdv->date_heure->format('H:i') : null,
                'date_heure' => $rdv->date_heure?->toDateTimeString(),
                'date_heure_human' => $scheduledAt?->format('d/m/Y H:i'),
                'statut' => $status,
                'waiting_minutes' => $waitingMinutes,
                'remaining_minutes' => $remainingMinutes,
                'is_delayed' => $waitingMinutes >= 10,
                'is_urgent' => str_contains(mb_strtolower((string) ($rdv->type ?? ''), 'UTF-8'), 'urgence')
                    || str_contains(mb_strtolower((string) ($rdv->motif ?? ''), 'UTF-8'), 'urgence'),
                'salle' => $rdv->medecin?->specialite ? 'Cabinet ' . $rdv->medecin->specialite : 'Salle consultation',
                'arrived_at' => $rdv->arrived_at?->toDateTimeString(),
                'consultation_started_at' => $rdv->consultation_started_at?->toDateTimeString(),
                'consultation_finished_at' => $rdv->consultation_finished_at?->toDateTimeString(),
                'absent_marked_at' => $rdv->absent_marked_at?->toDateTimeString(),
            ];

            $patientGroupingKey = $rdv->patient_id ?: ('rdv_' . $rdv->id);

            if (!isset($selectedByPatient[$patientGroupingKey])) {
                $selectedByPatient[$patientGroupingKey] = $item;
                continue;
            }

            if ($this->shouldReplaceWaitingRoomEntry($selectedByPatient[$patientGroupingKey], $item)) {
                $selectedByPatient[$patientGroupingKey] = $item;
            }
        }

        foreach ($selectedByPatient as $item) {
            $status = $item['statut'] ?? 'a_venir';

            if (!isset($grouped[$status])) {
                $grouped[$status] = [];
            }

            $grouped[$status][] = $item;
        }

        foreach ($grouped as $status => &$items) {
            usort($items, function (array $left, array $right): int {
                return strcmp((string) ($left['date_heure'] ?? ''), (string) ($right['date_heure'] ?? ''));
            });
        }
        unset($items);

        $counts = [
            'a_venir' => count($grouped['a_venir'] ?? []),
            'en_attente' => count($grouped['en_attente'] ?? []),
            'en_soins' => count($grouped['en_soins'] ?? []),
            'vu' => count($grouped['vu'] ?? []),
            'absent' => count($grouped['absent'] ?? []),
        ];

        return response()->json([
            'counts' => $counts,
            'lists' => $grouped,
            'meta' => [
                'refreshed_at' => $now->toIso8601String(),
                'total' => $all->count(),
            ],
        ]);
    }

    /**
     * Mettre à jour le statut d'un rendez-vous (actions rapides)
     */
    private function resolvePatientPhotoUrl(?Patient $patient): ?string
    {
        $photo = trim((string) ($patient?->photo ?? ''));
        if ($photo === '') {
            return null;
        }

        if (
            str_starts_with($photo, 'http://')
            || str_starts_with($photo, 'https://')
            || str_starts_with($photo, 'data:')
        ) {
            return $photo;
        }

        if (str_starts_with($photo, 'storage/')) {
            return asset($photo);
        }

        return asset('storage/' . ltrim($photo, '/'));
    }

    public function updateStatus(Request $request, $id)
    {
        $rdv = RendezVous::findOrFail($id);
        $oldStatus = RendezVous::normalizeStatus($rdv->statut) ?? self::STATUS_A_VENIR;

        $newStatus = RendezVous::normalizeStatus($request->input('statut', $request->input('status')));
        if (!$newStatus || !in_array($newStatus, self::allowedStatuses(), true)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Statut invalide'], 422);
            }

            throw ValidationException::withMessages([
                'statut' => 'Le statut selectionne est invalide.',
            ]);
        }

        if ($oldStatus === $newStatus) {
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'rendezvous' => $rdv->load(['patient', 'medecin'])]);
            }

            return redirect()->back()->with('success', 'Le statut du rendez-vous est deja a jour.');
        }

        $now = now($this->appTimezone());
        $rdv->statut = $newStatus;
        $this->applyWaitingRoomTransitionTimestamps($rdv, $newStatus, $now);
        $rdv->save();

        $this->logStatusTransition(
            $rdv->id,
            $oldStatus,
            $newStatus,
            auth()->id(),
            'waiting_room_ui',
            $now
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'rendezvous' => $rdv->load(['patient', 'medecin'])]);
        }

        return redirect()->back()->with('success', 'Statut du rendez-vous mis a jour avec succes.');
    }

    private function applyWaitingRoomTransitionTimestamps(RendezVous $rdv, string $newStatus, Carbon $at): void
    {
        if ($newStatus === self::STATUS_EN_ATTENTE && $rdv->arrived_at === null) {
            $rdv->arrived_at = $at;
        }

        if ($newStatus === self::STATUS_EN_SOINS && $rdv->consultation_started_at === null) {
            $rdv->consultation_started_at = $at;
        }

        if ($newStatus === self::STATUS_VU) {
            if ($rdv->arrived_at === null) {
                $rdv->arrived_at = $at;
            }
            if ($rdv->consultation_started_at === null) {
                $rdv->consultation_started_at = $at;
            }
            if ($rdv->consultation_finished_at === null) {
                $rdv->consultation_finished_at = $at;
            }
        }

        if ($newStatus === self::STATUS_ABSENT && $rdv->absent_marked_at === null) {
            $rdv->absent_marked_at = $at;
        }
    }

    private function logStatusTransition(
        int $rendezVousId,
        ?string $oldStatus,
        string $newStatus,
        ?int $changedBy,
        string $source,
        Carbon $at,
        ?string $notes = null
    ): void {
        RendezVousStatusHistory::create([
            'rendez_vous_id' => $rendezVousId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedBy,
            'source' => $source,
            'notes' => $notes,
            'transitioned_at' => $at,
        ]);
    }

    /**
     * Afficher la page indépendante de la salle d'attente
     */
    public function waitingRoomPage(Request $request)
    {
        try {
            $selectedDate = Carbon::parse($request->get('date', now($this->appTimezone())->toDateString()), $this->appTimezone())->startOfDay();
        } catch (\Throwable $e) {
            $selectedDate = now($this->appTimezone())->startOfDay();
        }

        $selectedMedecinId = $request->get('medecin_id', 'all');
        $selectedStatusInput = trim((string) $request->get('status', 'all'));
        $selectedStatus = $selectedStatusInput === '' || $selectedStatusInput === 'all'
            ? 'all'
            : $this->waitingRoomStatusKey($selectedStatusInput);
        $searchTerm = trim((string) $request->get('search', ''));
        $selectedMotif = trim((string) $request->get('motif', ''));
        $displayMode = $request->get('display') === 'tv' ? 'tv' : 'default';

        $medecins = Medecin::select('id', 'nom', 'prenom')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('agenda.waiting_room', compact(
            'medecins',
            'selectedDate',
            'selectedMedecinId',
            'selectedStatus',
            'searchTerm',
            'selectedMotif',
            'displayMode'
        ));
    }

    private function waitingRoomStatusKey(?string $status): string
    {
        return match (RendezVous::normalizeStatus($status) ?? self::STATUS_A_VENIR) {
            self::STATUS_A_VENIR => 'a_venir',
            self::STATUS_EN_ATTENTE => 'en_attente',
            self::STATUS_EN_SOINS => 'en_soins',
            self::STATUS_VU => 'vu',
            self::STATUS_ABSENT => 'absent',
            default => 'a_venir',
        };
    }

    private function shouldReplaceWaitingRoomEntry(array $current, array $candidate): bool
    {
        $currentRank = $this->waitingRoomStatusRank((string) ($current['statut'] ?? 'a_venir'));
        $candidateRank = $this->waitingRoomStatusRank((string) ($candidate['statut'] ?? 'a_venir'));

        if ($candidateRank < $currentRank) {
            return true;
        }

        if ($candidateRank > $currentRank) {
            return false;
        }

        $activeStatuses = ['en_soins', 'en_attente', 'a_venir'];
        $sameStatus = (string) ($candidate['statut'] ?? '') === (string) ($current['statut'] ?? '');

        if ($sameStatus && in_array((string) ($candidate['statut'] ?? ''), $activeStatuses, true)) {
            return strcmp((string) ($candidate['date_heure'] ?? ''), (string) ($current['date_heure'] ?? '')) < 0;
        }

        return strcmp((string) ($candidate['date_heure'] ?? ''), (string) ($current['date_heure'] ?? '')) > 0;
    }

    private function waitingRoomStatusRank(string $status): int
    {
        return match ($status) {
            'en_soins' => 1,
            'en_attente' => 2,
            'a_venir' => 3,
            'vu' => 4,
            'absent' => 5,
            default => 99,
        };
    }

    private function resolveRendezVousStatusPresentation(string $status): array
    {
        $normalizedStatus = RendezVous::normalizeStatus($status) ?? self::STATUS_A_VENIR;

        return match ($normalizedStatus) {
            self::STATUS_EN_ATTENTE => ['rdv-status-waiting', 'En attente', 'fa-hourglass-half'],
            self::STATUS_EN_SOINS => ['rdv-status-active', 'En soins', 'fa-stethoscope'],
            self::STATUS_VU => ['rdv-status-done', 'Vu', 'fa-circle-check'],
            self::STATUS_ABSENT => ['rdv-status-missed', 'Absent', 'fa-user-clock'],
            self::STATUS_ANNULE => ['rdv-status-cancelled', 'Annule', 'fa-ban'],
            default => ['rdv-status-upcoming', 'A venir', 'fa-calendar-days'],
        };
    }

    private function rendezVousMotifs(): array
    {
        return [
            ['label' => 'Consultation generale', 'icon' => 'fas fa-stethoscope'],
            ['label' => 'Controle', 'icon' => 'fas fa-heartbeat'],
            ['label' => 'Urgence', 'icon' => 'fas fa-triangle-exclamation'],
            ['label' => 'Renouvellement', 'icon' => 'fas fa-file-prescription'],
            ['label' => 'Bilan sante', 'icon' => 'fas fa-notes-medical'],
            ['label' => 'Vaccination', 'icon' => 'fas fa-syringe'],
        ];
    }

    private function rendezVousHeures(): array
    {
        return [
            '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30',
        ];
    }

    private function dayBounds(Carbon $date): array
    {
        return [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
    }
}




