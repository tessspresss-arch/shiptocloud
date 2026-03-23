<?php

namespace App\Services\Billing;

use App\Models\Depense;
use App\Models\Examen;
use App\Models\Facture;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PaiementLedgerService
{
    public function build(Request $request): Collection
    {
        $ledger = $this->invoiceEntries($request)
            ->concat($this->expenseEntries($request))
            ->concat($this->examEntries($request))
            ->sortByDesc(fn (array $row) => $row['sort_date'])
            ->values();

        if ($request->filled('date_from')) {
            $from = Carbon::parse((string) $request->input('date_from'))->startOfDay();
            $ledger = $ledger
                ->filter(fn (array $row) => $row['date_operation'] && $row['date_operation']->greaterThanOrEqualTo($from))
                ->values();
        }

        if ($request->filled('date_to')) {
            $to = Carbon::parse((string) $request->input('date_to'))->endOfDay();
            $ledger = $ledger
                ->filter(fn (array $row) => $row['date_operation'] && $row['date_operation']->lessThanOrEqualTo($to))
                ->values();
        }

        if ($request->filled('mode')) {
            $selectedMode = $this->normalizeKey((string) $request->input('mode'));
            $ledger = $ledger
                ->filter(fn (array $row) => $this->normalizeKey((string) ($row['mode_paiement'] ?? '')) === $selectedMode)
                ->values();
        }

        if ($request->filled('statut')) {
            $selectedStatus = $this->normalizeKey((string) $request->input('statut'));
            $ledger = $ledger
                ->filter(fn (array $row) => $this->normalizeKey((string) ($row['statut'] ?? '')) === $selectedStatus)
                ->values();
        }

        return $ledger;
    }

    public function find(string $source, int $id): ?array
    {
        return match ($source) {
            'factures' => ($facture = Facture::query()
                ->with(['patient:id,nom,prenom', 'medecin:id,nom,prenom'])
                ->select(['id', 'numero_facture', 'patient_id', 'medecin_id', 'date_facture', 'date_paiement', 'mode_paiement', 'statut', 'montant_total', 'remise', 'created_at'])
                ->find($id)) ? $this->mapInvoice($facture) : null,
            'depenses' => ($depense = Depense::query()
                ->select(['id', 'description', 'beneficiaire', 'date_depense', 'date_paiement', 'mode_paiement', 'methode_paiement', 'reference_paiement', 'statut', 'montant', 'created_at'])
                ->find($id)) ? $this->mapExpense($depense) : null,
            'examens' => ($examen = Examen::query()
                ->with(['patient:id,nom,prenom', 'medecin:id,nom,prenom'])
                ->select(['id', 'patient_id', 'medecin_id', 'nom_examen', 'date_demande', 'cout', 'payee', 'created_at'])
                ->find($id)) ? $this->mapExam($examen) : null,
            default => null,
        };
    }

    private function invoiceEntries(Request $request): Collection
    {
        return Facture::query()
            ->with(['patient:id,nom,prenom', 'medecin:id,nom,prenom'])
            ->select(['id', 'numero_facture', 'patient_id', 'medecin_id', 'date_facture', 'date_paiement', 'mode_paiement', 'statut', 'montant_total', 'remise', 'created_at'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($inner) use ($search) {
                    $inner->where('numero_facture', 'like', '%' . $search . '%')
                        ->orWhereHas('patient', function ($patientQuery) use ($search) {
                            $patientQuery->where('nom', 'like', '%' . $search . '%')
                                ->orWhere('prenom', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($request->filled('source'), fn ($query) => $request->input('source') === 'factures' ? $query : $query->whereRaw('1 = 0'))
            ->get()
            ->map(fn (Facture $facture): array => $this->mapInvoice($facture));
    }

    private function expenseEntries(Request $request): Collection
    {
        return Depense::query()
            ->select(['id', 'description', 'beneficiaire', 'date_depense', 'date_paiement', 'mode_paiement', 'methode_paiement', 'reference_paiement', 'statut', 'montant', 'created_at'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($inner) use ($search) {
                    $inner->where('description', 'like', '%' . $search . '%')
                        ->orWhere('beneficiaire', 'like', '%' . $search . '%')
                        ->orWhere('reference_paiement', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('source'), fn ($query) => $request->input('source') === 'depenses' ? $query : $query->whereRaw('1 = 0'))
            ->get()
            ->map(fn (Depense $depense): array => $this->mapExpense($depense));
    }

    private function examEntries(Request $request): Collection
    {
        return Examen::query()
            ->with(['patient:id,nom,prenom', 'medecin:id,nom,prenom'])
            ->select(['id', 'patient_id', 'medecin_id', 'nom_examen', 'date_demande', 'cout', 'payee', 'created_at'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($inner) use ($search) {
                    $inner->where('nom_examen', 'like', '%' . $search . '%')
                        ->orWhereHas('patient', function ($patientQuery) use ($search) {
                            $patientQuery->where('nom', 'like', '%' . $search . '%')
                                ->orWhere('prenom', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($request->filled('source'), fn ($query) => $request->input('source') === 'examens' ? $query : $query->whereRaw('1 = 0'))
            ->get()
            ->map(fn (Examen $examen): array => $this->mapExam($examen));
    }

    private function mapInvoice(Facture $facture): array
    {
        $amount = (float) (($facture->montant_total ?? 0) - ($facture->remise ?? 0));

        return [
            'id' => $facture->id,
            'source' => 'factures',
            'source_label' => 'Facture',
            'reference' => $facture->numero_facture ?: ('FAC-' . $facture->id),
            'patient' => $facture->patient ? trim($facture->patient->prenom . ' ' . $facture->patient->nom) : 'Patient inconnu',
            'tiers' => $facture->patient ? trim($facture->patient->prenom . ' ' . $facture->patient->nom) : 'Patient inconnu',
            'medecin' => $facture->medecin ? trim($facture->medecin->prenom . ' ' . $facture->medecin->nom) : null,
            'mode_paiement' => $facture->mode_paiement ?: 'Non defini',
            'statut' => (string) ($facture->statut ?? 'en_attente'),
            'montant' => $amount,
            'date_operation' => $facture->date_paiement ?: $facture->date_facture ?: $facture->created_at,
            'sort_date' => optional($facture->date_paiement ?: $facture->date_facture ?: $facture->created_at)?->timestamp ?? 0,
            'detail_url' => route('paiements.show', ['source' => 'factures', 'id' => $facture->id]),
            'source_detail_url' => route('factures.show', $facture),
        ];
    }

    private function mapExpense(Depense $depense): array
    {
        return [
            'id' => $depense->id,
            'source' => 'depenses',
            'source_label' => 'Depense',
            'reference' => $depense->reference_paiement ?: ('DEP-' . $depense->id),
            'patient' => null,
            'tiers' => $depense->beneficiaire ?: $depense->description ?: 'Depense',
            'medecin' => null,
            'mode_paiement' => $depense->mode_paiement ?: $depense->methode_paiement ?: 'Non defini',
            'statut' => (string) ($depense->statut ?? 'enregistre'),
            'montant' => -1 * (float) ($depense->montant ?? 0),
            'date_operation' => $depense->date_paiement ?: $depense->date_depense ?: $depense->created_at,
            'sort_date' => optional($depense->date_paiement ?: $depense->date_depense ?: $depense->created_at)?->timestamp ?? 0,
            'detail_url' => route('paiements.show', ['source' => 'depenses', 'id' => $depense->id]),
            'source_detail_url' => route('depenses.show', $depense),
        ];
    }

    private function mapExam(Examen $examen): array
    {
        return [
            'id' => $examen->id,
            'source' => 'examens',
            'source_label' => 'Examen',
            'reference' => $examen->nom_examen ?: ('EX-' . $examen->id),
            'patient' => $examen->patient ? trim($examen->patient->prenom . ' ' . $examen->patient->nom) : 'Patient inconnu',
            'tiers' => $examen->patient ? trim($examen->patient->prenom . ' ' . $examen->patient->nom) : 'Patient inconnu',
            'medecin' => $examen->medecin ? trim($examen->medecin->prenom . ' ' . $examen->medecin->nom) : null,
            'mode_paiement' => $examen->payee ? 'Regle' : 'A definir',
            'statut' => $examen->payee ? 'payee' : 'en_attente',
            'montant' => (float) ($examen->cout ?? 0),
            'date_operation' => $examen->date_demande ?: $examen->created_at,
            'sort_date' => optional($examen->date_demande ?: $examen->created_at)?->timestamp ?? 0,
            'detail_url' => route('paiements.show', ['source' => 'examens', 'id' => $examen->id]),
            'source_detail_url' => route('examens.show', $examen),
        ];
    }

    private function normalizeKey(string $value): string
    {
        return (string) Str::of($value)->lower()->ascii()->replace([' ', '/', '_'], '-');
    }
}
