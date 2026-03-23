<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Facture;
use App\Models\Depense;
use App\Models\RendezVous;
use App\Models\Consultation;

class ReportService
{
    /**
     * Generate monthly revenue report
     */
    public static function monthlyRevenueReport($month, $year)
    {
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return [
            'period' => $startDate->format('F Y'),
            'factures' => Facture::whereBetween('date_facture', [$startDate, $endDate])
                ->with('patient')
                ->get(),
            'total' => Facture::whereBetween('date_facture', [$startDate, $endDate])
                ->sum('montant_total'),
            'payees' => Facture::whereBetween('date_facture', [$startDate, $endDate])
                ->where('statut', 'payee')
                ->sum('montant_total'),
            'impayees' => Facture::whereBetween('date_facture', [$startDate, $endDate])
                ->where('statut', '!=', 'payee')
                ->sum('montant_total'),
        ];
    }

    /**
     * Generate monthly expenses report
     */
    public static function monthlyExpensesReport($month, $year)
    {
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return [
            'period' => $startDate->format('F Y'),
            'depenses' => Depense::whereBetween('date_depense', [$startDate, $endDate])
                ->with('categorie')
                ->get(),
            'total' => Depense::whereBetween('date_depense', [$startDate, $endDate])
                ->sum('montant'),
            'par_categorie' => Depense::whereBetween('date_depense', [$startDate, $endDate])
                ->selectRaw('categorie_id, SUM(montant) as total')
                ->groupBy('categorie_id')
                ->with('categorie')
                ->get(),
        ];
    }

    /**
     * Generate patient statistics report
     */
    public static function patientStatisticsReport($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return [
            'total_patients' => Patient::count(),
            'nouveaux_patients' => Patient::whereBetween('created_at', [$startDate, $endDate])->count(),
            'consultations_mois' => Consultation::whereBetween('date_consultation', [$startDate, $endDate])
                ->count(),
            'rdv_completes' => RendezVous::whereBetween('date_rdv', [$startDate, $endDate])
                ->where('statut', 'confirme')
                ->count(),
            'rdv_annules' => RendezVous::whereBetween('date_rdv', [$startDate, $endDate])
                ->where('statut', 'annule')
                ->count(),
            'taux_presentation' => self::calculatePresenceRate($startDate, $endDate),
        ];
    }

    /**
     * Calculate appointment presence rate
     */
    private static function calculatePresenceRate($startDate, $endDate)
    {
        $total = RendezVous::whereBetween('date_rdv', [$startDate, $endDate])->count();
        if ($total === 0) return 0;

        $completes = RendezVous::whereBetween('date_rdv', [$startDate, $endDate])
            ->where('statut', 'confirme')
            ->count();

        return round(($completes / $total) * 100, 2);
    }

    /**
     * Generate annual summary report
     */
    public static function annualSummaryReport($year = null)
    {
        $year = $year ?? now()->year;

        $startDate = \Carbon\Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endDate = $startDate->copy()->endOfYear();

        return [
            'year' => $year,
            'total_patients' => Patient::count(),
            'new_patients' => Patient::whereYear('created_at', $year)->count(),
            'total_consultations' => Consultation::whereYear('date_consultation', $year)->count(),
            'total_revenue' => Facture::whereBetween('date_facture', [$startDate, $endDate])
                ->sum('montant_total'),
            'total_expenses' => Depense::whereBetween('date_depense', [$startDate, $endDate])
                ->sum('montant'),
            'net_profit' => Facture::whereBetween('date_facture', [$startDate, $endDate])->sum('montant_total') -
                           Depense::whereBetween('date_depense', [$startDate, $endDate])->sum('montant'),
            'total_appointments' => RendezVous::whereBetween('date_rdv', [$startDate, $endDate])->count(),
        ];
    }
}
