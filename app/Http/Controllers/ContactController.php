<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private const ALLOWED_SORT_COLUMNS = [
        'nom',
        'prenom',
        'type',
        'email',
        'telephone',
        'created_at',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }

        if ($request->has('actif')) {
            if ($request->actif === 'oui') {
                $query->actifs();
            } elseif ($request->actif === 'non') {
                $query->where('is_actif', false);
            }
        } else {
            $query->actifs();
        }

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        $sortBy = (string) $request->get('sort_by', 'nom');
        $sortOrder = (string) $request->get('sort_order', 'asc');
        if (!in_array($sortBy, self::ALLOWED_SORT_COLUMNS, true)) {
            $sortBy = 'nom';
        }
        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $contacts = $query->paginate(20)->appends($request->query());
        $types = ['patient', 'laboratoire', 'fournisseur', 'hopital', 'assurance', 'autre'];
        $typesMap = [
            'patient' => 'Patient',
            'laboratoire' => 'Laboratoire',
            'fournisseur' => 'Fournisseur',
            'hopital' => 'Hopital',
            'assurance' => 'Assurance',
            'autre' => 'Autre',
        ];
        $typesList = [];

        foreach ($types as $value) {
            $typesList[$value] = $typesMap[$value] ?? ucfirst((string) $value);
        }

        $totalContacts = Contact::count();
        $activeContacts = Contact::where('is_actif', true)->count();
        $favoriteContacts = Contact::where('is_favorite', true)->count();
        $typeCount = Contact::whereNotNull('type')->distinct('type')->count('type');

        $contactsList = $contacts;

        return view('contacts.index', compact(
            'contacts',
            'contactsList',
            'types',
            'typesList',
            'totalContacts',
            'activeContacts',
            'favoriteContacts',
            'typeCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = [
            'patient' => 'Patient',
            'laboratoire' => 'Laboratoire',
            'fournisseur' => 'Fournisseur',
            'hopital' => 'Hopital',
            'assurance' => 'Assurance',
            'autre' => 'Autre',
        ];

        return view('contacts.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'prenom' => 'nullable|string|max:150',
            'type' => 'required|in:patient,laboratoire,fournisseur,hopital,assurance,autre',
            'email' => 'nullable|email|max:150',
            'telephone' => 'nullable|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:100',
            'codepostal' => 'nullable|string|max:10',
            'entreprise' => 'nullable|string|max:255',
            'fonction' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['is_actif'] = true;
        Contact::create($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact cree avec succes');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return view('contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        $types = [
            'patient' => 'Patient',
            'laboratoire' => 'Laboratoire',
            'fournisseur' => 'Fournisseur',
            'hopital' => 'Hopital',
            'assurance' => 'Assurance',
            'autre' => 'Autre',
        ];

        return view('contacts.edit', compact('contact', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'prenom' => 'nullable|string|max:150',
            'type' => 'required|in:patient,laboratoire,fournisseur,hopital,assurance,autre',
            'email' => 'nullable|email|max:150',
            'telephone' => 'nullable|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:100',
            'codepostal' => 'nullable|string|max:10',
            'entreprise' => 'nullable|string|max:255',
            'fonction' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $contact->update($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact mis a jour avec succes');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact supprime avec succes');
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(Contact $contact)
    {
        $contact->update(['is_favorite' => !$contact->is_favorite]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'is_favorite' => $contact->is_favorite]);
        }

        return back();
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Contact $contact)
    {
        $contact->update(['is_actif' => !$contact->is_actif]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'is_actif' => $contact->is_actif]);
        }

        return back();
    }

    /**
     * Export contacts to Excel
     */
    public function export(Request $request)
    {
        $query = Contact::actifs();

        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }

        $contacts = $query->orderBy('nom')->get();

        return \Excel::download(new \App\Exports\ContactsExport($contacts), 'contacts.xlsx');
    }
}
