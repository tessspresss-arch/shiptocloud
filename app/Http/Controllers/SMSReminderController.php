<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\SMSReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SMSReminderController extends Controller
{
    public function index()
    {
        $reminders = SMSReminder::with([
            'patient:id,nom,prenom,telephone',
            'rendezvous:id,date_heure,medecin_id',
            'rendezvous.medecin:id,nom,prenom',
        ])
            ->orderByDesc('date_envoi_prevue')
            ->paginate(15);

        $stats = [
            'total' => SMSReminder::count(),
            'planifies' => SMSReminder::where('statut', 'planifie')->count(),
            'envoyes' => SMSReminder::where('statut', 'envoye')
                ->whereMonth('date_envoi_reelle', now()->month)
                ->whereYear('date_envoi_reelle', now()->year)
                ->count(),
            'echoues' => SMSReminder::where('statut', 'echec')->count(),
        ];

        $this->decorateReminderCollection($reminders);

        return view('sms.index', compact('reminders', 'stats'));
    }

    public function logs(Request $request)
    {
        $query = SMSReminder::with([
            'patient:id,nom,prenom,telephone',
            'rendezvous:id,date_heure,medecin_id',
            'rendezvous.medecin:id,nom,prenom',
        ]);

        $selectedStatut = $request->filled('statut') ? (string) $request->string('statut') : '';

        if ($selectedStatut !== '') {
            $query->where('statut', $selectedStatut);
        }

        $reminders = $query->orderByDesc('date_envoi_prevue')->paginate(20)->appends($request->query());
        $statuts = ['planifie', 'envoye', 'echec', 'desactive'];

        $stats = [
            'total' => SMSReminder::count(),
            'planifies' => SMSReminder::where('statut', 'planifie')->count(),
            'envoyes' => SMSReminder::where('statut', 'envoye')->count(),
            'echoues' => SMSReminder::where('statut', 'echec')->count(),
            'desactives' => SMSReminder::where('statut', 'desactive')->count(),
        ];

        $this->decorateReminderCollection($reminders);

        return view('sms.logs', compact('reminders', 'statuts', 'stats', 'selectedStatut'));
    }

    public function create(Request $request)
    {
        $rendezvousList = RendezVous::with([
            'patient:id,nom,prenom,telephone',
            'medecin:id,nom,prenom',
        ])
            ->where('date_heure', '>=', now()->subDay())
            ->orderBy('date_heure')
            ->limit(100)
            ->get();

        $selectedRendezvousId = $request->integer('rendezvous_id');
        $rendezvousCount = is_countable($rendezvousList ?? null) ? count($rendezvousList) : 0;

        return view('sms.create', compact('rendezvousList', 'selectedRendezvousId', 'rendezvousCount'));
    }

    public function show(SMSReminder $reminder)
    {
        $reminder->load([
            'patient:id,nom,prenom,telephone,email',
            'rendezvous:id,date_heure,medecin_id',
            'rendezvous.medecin:id,nom,prenom,specialite',
        ]);

        $this->decorateReminder($reminder);

        return view('sms.show', compact('reminder'));
    }

    public function edit(SMSReminder $reminder)
    {
        $reminder->load(['patient:id,nom,prenom,telephone', 'rendezvous:id,date_heure,medecin_id']);

        $rendezvousList = RendezVous::with([
            'patient:id,nom,prenom,telephone',
            'medecin:id,nom,prenom',
        ])
            ->where(function ($query) use ($reminder) {
                $query->where('date_heure', '>=', now()->subDay())
                    ->orWhere('id', $reminder->rendezvous_id);
            })
            ->orderBy('date_heure')
            ->limit(100)
            ->get();

        $this->decorateReminder($reminder);

        return view('sms.edit', compact('reminder', 'rendezvousList'));
    }

    public function store(Request $request)
    {
        // Support old and new form field names.
        $data = $request->all();
        $data['rendezvous_id'] = $data['rendezvous_id'] ?? $data['appointment_id'] ?? null;
        $data['telephone'] = $data['telephone'] ?? $data['phone_number'] ?? null;
        $data['message_template'] = $data['message_template'] ?? $data['message'] ?? null;
        $data['date_envoi_prevue'] = $data['date_envoi_prevue'] ?? $data['send_date'] ?? null;

        $validated = validator($data, [
            'rendezvous_id' => 'required|exists:rendez_vous,id',
            'telephone' => ['required', 'string', 'max:20', 'regex:/^(\+212|0)[0-9]{9}$/'],
            'message_template' => 'nullable|string|max:1000',
            'date_envoi_prevue' => 'nullable|date',
            'heures_avant' => 'nullable|integer|min:1|max:72',
        ], [
            'telephone.regex' => 'Le numero doit etre au format +212XXXXXXXXX ou 0XXXXXXXXX.',
        ])->validate();

        $rendezvous = RendezVous::findOrFail($validated['rendezvous_id']);
        $rendezvousAt = Carbon::parse($rendezvous->date_heure);

        $plannedAt = !empty($validated['date_envoi_prevue'])
            ? Carbon::parse($validated['date_envoi_prevue'])
            : null;

        $heuresAvant = isset($validated['heures_avant']) ? (int) $validated['heures_avant'] : 24;

        if (!$plannedAt) {
            $plannedAt = $rendezvousAt->copy()->subHours($heuresAvant);
        }

        if ($plannedAt->greaterThan($rendezvousAt)) {
            return back()
                ->withInput()
                ->withErrors([
                    'date_envoi_prevue' => "La date d'envoi doit etre avant la date du rendez-vous.",
                ]);
        }

        $heuresAvantCalculees = max(0, (int) $plannedAt->diffInHours($rendezvousAt, false));

        SMSReminder::create([
            'rendezvous_id' => $rendezvous->id,
            'patient_id' => $rendezvous->patient_id,
            'telephone' => $validated['telephone'],
            'heures_avant' => $heuresAvantCalculees,
            'statut' => 'planifie',
            'date_envoi_prevue' => $plannedAt,
            'message_template' => $validated['message_template'] ?? null,
        ]);

        return redirect()->route('sms.index')->with('success', 'Rappel SMS cree avec succes.');
    }

    public function update(Request $request, SMSReminder $reminder)
    {
        $validated = $request->validate([
            'rendezvous_id' => 'required|exists:rendez_vous,id',
            'telephone' => ['required', 'string', 'max:20', 'regex:/^(\+212|0)[0-9]{9}$/'],
            'message_template' => 'nullable|string|max:1000',
            'date_envoi_prevue' => 'nullable|date',
            'heures_avant' => 'nullable|integer|min:1|max:72',
            'statut' => 'nullable|in:planifie,envoye,echec,desactive',
        ], [
            'telephone.regex' => 'Le numero doit etre au format +212XXXXXXXXX ou 0XXXXXXXXX.',
        ]);

        $rendezvous = RendezVous::findOrFail($validated['rendezvous_id']);
        $rendezvousAt = Carbon::parse($rendezvous->date_heure);

        $plannedAt = !empty($validated['date_envoi_prevue'])
            ? Carbon::parse($validated['date_envoi_prevue'])
            : null;
        $heuresAvant = isset($validated['heures_avant']) ? (int) $validated['heures_avant'] : 24;

        if (!$plannedAt) {
            $plannedAt = $rendezvousAt->copy()->subHours($heuresAvant);
        }

        if ($plannedAt->greaterThan($rendezvousAt)) {
            return back()
                ->withInput()
                ->withErrors([
                    'date_envoi_prevue' => "La date d'envoi doit etre avant la date du rendez-vous.",
                ]);
        }

        $heuresAvantCalculees = max(0, (int) $plannedAt->diffInHours($rendezvousAt, false));

        $reminder->update([
            'rendezvous_id' => $rendezvous->id,
            'patient_id' => $rendezvous->patient_id,
            'telephone' => $validated['telephone'],
            'message_template' => $validated['message_template'] ?? null,
            'date_envoi_prevue' => $plannedAt,
            'heures_avant' => $heuresAvantCalculees,
            'statut' => $validated['statut'] ?? 'planifie',
            'code_erreur' => null,
            'erreur_message' => null,
        ]);

        return redirect()->route('sms.show', $reminder)
            ->with('success', 'Rappel SMS modifie avec succes.');
    }

    public function resend(SMSReminder $reminder)
    {
        try {
            $reminder->loadMissing(['patient', 'rendezvous']);

            $sent = \App\Services\SMSService::sendReminder($reminder);
            if (!$sent) {
                return back()->with('error', "Le SMS n'a pas pu etre renvoye.");
            }

            return back()->with('success', 'SMS renvoye avec succes.');
        } catch (\Throwable $e) {
            Log::error('SMS reminder resend failed', [
                'reminder_id' => $reminder->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', "Erreur lors du renvoi du SMS.");
        }
    }

    public function cancel(SMSReminder $reminder)
    {
        $reminder->update(['statut' => 'desactive']);

        return back()->with('success', 'Rappel annule.');
    }

    public function sendTest(Request $request)
    {
        $validated = $request->validate([
            'telephone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        try {
            \App\Services\SMSService::send(
                $validated['telephone'],
                $validated['message'],
                'test'
            );

            return back()->with('success', 'SMS test envoye avec succes.');
        } catch (\Exception $e) {
            Log::error('SMS test send failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', "Erreur lors de l'envoi du SMS test.");
        }
    }

    private function decorateReminderCollection($reminders): void
    {
        $reminders->getCollection()->transform(function (SMSReminder $reminder) {
            return $this->decorateReminder($reminder);
        });
    }

    private function decorateReminder(SMSReminder $reminder): SMSReminder
    {
        $reminder->display_patient_name = trim(($reminder->patient->prenom ?? '') . ' ' . ($reminder->patient->nom ?? '')) ?: 'Patient inconnu';
        $reminder->display_doctor_name = trim(($reminder->rendezvous->medecin->prenom ?? '') . ' ' . ($reminder->rendezvous->medecin->nom ?? ''));
        $reminder->display_rdv_date = optional($reminder->rendezvous?->date_heure)->format('d/m/Y H:i') ?: '--';
        $reminder->display_send_date = optional($reminder->date_envoi_prevue)->format('d/m/Y H:i') ?: '--';
        $reminder->display_status_class = match ($reminder->statut) {
            'envoye' => 'status-sent',
            'echec' => 'status-failed',
            default => 'status-planned',
        };
        $reminder->display_status_label = match ($reminder->statut) {
            'envoye' => 'Envoye',
            'echec' => 'Echoue',
            'desactive' => 'Desactive',
            default => 'Planifie',
        };
        $reminder->display_status_icon = match ($reminder->statut) {
            'envoye' => 'fa-check-circle',
            'echec' => 'fa-times-circle',
            default => 'fa-clock',
        };
        $reminder->show_status_class = match ($reminder->statut) {
            'envoye' => 'is-envoye',
            'echec' => 'is-echec',
            'desactive' => 'is-desactive',
            default => 'is-planifie',
        };

        return $reminder;
    }
}
