<?php

namespace App\Http\Controllers;

use App\Models\CertificatMedical;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Consultation;
use App\Models\ModeleCertificat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\Pdf\PdfBuilder;

class CertificatMedicalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CertificatMedical::with('patient', 'medecin');

        // Filtrer par patient
        if ($request->has('patient') && $request->patient) {
            $query->byPatient($request->patient);
        }

        // Filtrer par type
        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }

        // Filtrer par transmission
        if ($request->has('transmis')) {
            if ($request->transmis === 'oui') {
                $query->transmis();
            } elseif ($request->transmis === 'non') {
                $query->nonTransmis();
            }
        }

        // Recherche
        if ($request->has('search') && $request->search) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%");
            });
        }

        $certificats = $query->orderBy('date_emission', 'desc')->paginate(20);
        $patients = Patient::orderBy('nom')->get();
        $types = ['ArrÃƒÂªt de travail', 'Justificatif', 'IncapacitÃƒÂ©', 'Dispense d\'activitÃƒÂ© physique', 'Autre'];

        return view('certificats.index', compact('certificats', 'patients', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $patients = Patient::orderBy('nom')->get();
        $medecins = Medecin::where('id', Auth::user()->medecin_id)->orWhere(function ($q) {
            $q->where('is_active', true);
        })->get();
        $types = ['ArrÃƒÂªt de travail', 'Justificatif', 'IncapacitÃƒÂ©', 'Dispense d\'activitÃƒÂ© physique', 'Autre'];
        $modeles = ModeleCertificat::actifs()
            ->where(function ($q) {
                $q->where('est_template_general', true)
                  ->orWhere('medecin_id', Auth::user()->medecin_id);
            })
            ->get();

        $patient = null;
        if ($request->has('patient_id')) {
            $patient = Patient::find($request->patient_id);
        }

        return view('certificats.create', compact('patients', 'medecins', 'types', 'modeles', 'patient'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'type' => 'required|string|max:100',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'nombre_jours' => 'nullable|integer|min:1',
            'motif' => 'required|string',
            'observations' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        $validated['date_emission'] = now();
        $validated['date_debut'] = Carbon::createFromFormat('Y-m-d', $request->date_debut)->startOfDay();
        $validated['date_fin'] = Carbon::createFromFormat('Y-m-d', $request->date_fin)->endOfDay();
        
        if (!$validated['nombre_jours']) {
            $validated['nombre_jours'] = $validated['date_debut']->diffInDays($validated['date_fin']) + 1;
        }

        $certificat = CertificatMedical::create($validated);

        // GÃƒÂ©nÃƒÂ©rer le PDF
        $this->generatePDF($certificat);

        return redirect()->route('certificats.show', $certificat)->with('success', 'Certificat crÃƒÂ©ÃƒÂ© avec succÃƒÂ¨s');
    }

    /**
     * Display the specified resource.
     */
    public function show(CertificatMedical $certificat)
    {
        $certificat->load('patient', 'medecin', 'consultation');
        return view('certificats.show', compact('certificat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CertificatMedical $certificat)
    {
        $patients = Patient::orderBy('nom')->get();
        $medecins = Medecin::where('is_active', true)->get();
        $types = ['ArrÃƒÂªt de travail', 'Justificatif', 'IncapacitÃƒÂ©', 'Dispense d\'activitÃƒÂ© physique', 'Autre'];

        return view('certificats.edit', compact('certificat', 'patients', 'medecins', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CertificatMedical $certificat)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'type' => 'required|string|max:100',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'nombre_jours' => 'nullable|integer|min:1',
            'motif' => 'required|string',
            'observations' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        $validated['date_debut'] = Carbon::createFromFormat('Y-m-d', $request->date_debut)->startOfDay();
        $validated['date_fin'] = Carbon::createFromFormat('Y-m-d', $request->date_fin)->endOfDay();
        
        if (!$validated['nombre_jours']) {
            $validated['nombre_jours'] = $validated['date_debut']->diffInDays($validated['date_fin']) + 1;
        }

        $certificat->update($validated);

        // RÃƒÂ©gÃƒÂ©nÃƒÂ©rer le PDF
        $this->generatePDF($certificat);

        return redirect()->route('certificats.show', $certificat)->with('success', 'Certificat mis ÃƒÂ  jour avec succÃƒÂ¨s');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CertificatMedical $certificat)
    {
        if ($certificat->fichier_pdf && \Storage::exists($certificat->fichier_pdf)) {
            \Storage::delete($certificat->fichier_pdf);
        }

        $certificat->delete();
        
        return redirect()->route('certificats.index')->with('success', 'Certificat supprimÃƒÂ© avec succÃƒÂ¨s');
    }

    /**
     * Generate PDF certificate
     */
    protected function generatePDF(CertificatMedical $certificat)
    {
        $certificat->load('patient', 'medecin');
        
        $html = view('certificats.pdf', compact('certificat'))->render();
        
        $pdf = app(PdfBuilder::class)->fromHtml($html);
        $filename = 'certificat_' . $certificat->patient->id . '_' . $certificat->id . '_' . now()->timestamp . '.pdf';
        $path = 'certificats/' . $filename;

        \Storage::put($path, $pdf->output());
        
        $certificat->update(['fichier_pdf' => $path]);
    }

    /**
     * Download certificate PDF
     */
    public function downloadPDF(CertificatMedical $certificat)
    {
        if (!$certificat->fichier_pdf || !\Storage::exists($certificat->fichier_pdf)) {
            $this->generatePDF($certificat);
        }

        return \Storage::download($certificat->fichier_pdf);
    }

    /**
     * Mark as transmitted
     */
    public function marquerTransmis(CertificatMedical $certificat)
    {
        $certificat->update([
            'est_transmis' => true,
            'date_transmission' => now()
        ]);

        return back()->with('success', 'Certificat marquÃƒÂ© comme transmis');
    }

    /**
     * Export to Excel
     */
    public function export(Request $request)
    {
        $query = CertificatMedical::with('patient', 'medecin');

        if ($request->has('patient') && $request->patient) {
            $query->byPatient($request->patient);
        }

        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }

        $certificats = $query->orderBy('date_emission', 'desc')->get();

        return \Excel::download(new \App\Exports\CertificatsExport($certificats), 'certificats.xlsx');
    }
}
