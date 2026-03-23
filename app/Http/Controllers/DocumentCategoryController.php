<?php

namespace App\Http\Controllers;

use App\Models\CategorieDocument;
use App\Models\DocumentMedical;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        CategorieDocument::ensureDefaultCatalog();

        $categories = CategorieDocument::query()
            ->ordered()
            ->get();

        $documentsByCategory = DocumentMedical::query()
            ->selectRaw('categorie_document_id, COUNT(*) as total')
            ->whereNotNull('categorie_document_id')
            ->groupBy('categorie_document_id')
            ->pluck('total', 'categorie_document_id');

        $totalDocuments = (int) $documentsByCategory->sum();
        $activeCategories = (int) $categories->where('actif', true)->count();
        $confidentialCategories = (int) $categories->where('confidentiel', true)->count();
        $patientCategories = (int) $categories->where('est_document_patient', true)->count();

        return view('documents.categories', compact(
            'categories',
            'documentsByCategory',
            'totalDocuments',
            'activeCategories',
            'confidentialCategories',
            'patientCategories',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        CategorieDocument::create($validated);

        return redirect()
            ->route('documents.categories')
            ->with('success', 'Categorie de document ajoutee avec succes.');
    }

    public function update(Request $request, CategorieDocument $category): RedirectResponse
    {
        $validated = $this->validateCategory($request, $category);

        $category->update($validated);

        return redirect()
            ->route('documents.categories')
            ->with('success', 'Categorie de document mise a jour avec succes.');
    }

    private function validateCategory(Request $request, ?CategorieDocument $category = null): array
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:categorie_documents,nom' . ($category ? ',' . $category->id : ''),
            'description' => 'nullable|string|max:255',
            'couleur' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icone' => 'nullable|string|max:50',
            'duree_conservation_ans' => 'nullable|integer|min:0|max:99',
            'ordre' => 'nullable|integer|min:0|max:999',
            'actif' => 'nullable|boolean',
            'confidentiel' => 'nullable|boolean',
            'est_document_patient' => 'nullable|boolean',
        ]);

        $validated['couleur'] = $validated['couleur'] ?? '#3b82f6';
        $validated['icone'] = $validated['icone'] ?? 'fas fa-folder';
        $validated['duree_conservation_ans'] = (int) ($validated['duree_conservation_ans'] ?? 10);
        $validated['ordre'] = (int) ($validated['ordre'] ?? 0);
        $validated['actif'] = $request->boolean('actif', true);
        $validated['confidentiel'] = $request->boolean('confidentiel');
        $validated['est_document_patient'] = $request->boolean('est_document_patient');

        return $validated;
    }
}
