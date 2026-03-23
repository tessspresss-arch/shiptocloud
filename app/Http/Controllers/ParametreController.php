<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\ModeleCertificat;
use App\Models\ModeleOrdonnance;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = Setting::all()->pluck('valeur', 'cle')->toArray();
        
        return view('parametres.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nom_cabinet' => 'required|string|max:255',
            'adresse_cabinet' => 'required|string|max:255',
            'telephone_cabinet' => 'required|string|max:20',
            'email_cabinet' => 'required|email',
            'siren' => 'nullable|string|max:14',
            'siret' => 'nullable|string|max:14',
            'ape' => 'nullable|string|max:5',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sms_api_key' => 'nullable|string',
            'sms_api_secret' => 'nullable|string',
            'sms_provider' => 'nullable|in:twilio,aws-sns,other',
            'sms_enabled' => 'boolean',
            'sms_default_hours' => 'nullable|integer|min:1|max:72',
        ]);

        foreach ($validated as $key => $value) {
            if ($key !== 'logo') {
                Setting::updateOrCreate(
                    ['cle' => $key],
                    ['valeur' => $value]
                );
            }
        }

        // Gérer l'upload du logo
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('logo', 'public');
            Setting::updateOrCreate(
                ['cle' => 'logo'],
                ['valeur' => $path]
            );
        }

        return back()->with('success', 'Paramètres mis à jour avec succès');
    }

    /**
     * Manage users
     */
    public function users(Request $request)
    {
        $users = User::paginate(20);
        return view('parametres.users.index', compact('users'));
    }

    /**
     * Create user
     */
    public function createUser()
    {
        return view('parametres.users.create');
    }

    /**
     * Store user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,medecin,secretaire',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);

        return redirect()->route('parametres.users')->with('success', 'Utilisateur créé avec succès');
    }

    /**
     * Edit user
     */
    public function editUser(User $user)
    {
        return view('parametres.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,medecin,secretaire',
        ]);

        if ($validated['password']) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('parametres.users')->with('success', 'Utilisateur mis à jour avec succès');
    }

    /**
     * Delete user
     */
    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        $user->delete();
        return redirect()->route('parametres.users')->with('success', 'Utilisateur supprimé');
    }

    /**
     * Manage certificate templates
     */
    public function certificats()
    {
        $modeles = ModeleCertificat::paginate(20);
        return view('parametres.certificats.index', compact('modeles'));
    }

    /**
     * Create certificate template
     */
    public function createCertificat()
    {
        return view('parametres.certificats.create');
    }

    /**
     * Store certificate template
     */
    public function storeCertificat(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'type' => 'required|string|max:100',
            'contenu_html' => 'required|string',
            'est_template_general' => 'boolean',
        ]);

        if (!$validated['est_template_general']) {
            $validated['medecin_id'] = auth()->user()->medecin_id;
        }

        $validated['is_actif'] = true;
        ModeleCertificat::create($validated);

        return redirect()->route('parametres.certificats')->with('success', 'Modèle créé');
    }

    /**
     * Edit certificate template
     */
    public function editCertificat(ModeleCertificat $modele)
    {
        return view('parametres.certificats.edit', compact('modele'));
    }

    /**
     * Update certificate template
     */
    public function updateCertificat(Request $request, ModeleCertificat $modele)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'type' => 'required|string|max:100',
            'contenu_html' => 'required|string',
        ]);

        $modele->update($validated);

        return redirect()->route('parametres.certificats')->with('success', 'Modèle mis à jour');
    }

    /**
     * Delete certificate template
     */
    public function destroyCertificat(ModeleCertificat $modele)
    {
        $modele->delete();
        return redirect()->route('parametres.certificats')->with('success', 'Modèle supprimé');
    }

    /**
     * Manage prescription templates
     */
    public function ordonnances()
    {
        $modeles = ModeleOrdonnance::paginate(20);
        return view('parametres.ordonnances.index', compact('modeles'));
    }

    /**
     * Create prescription template
     */
    public function createOrdonnance()
    {
        return view('parametres.ordonnances.create');
    }

    /**
     * Store prescription template
     */
    public function storeOrdonnance(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'contenu_html' => 'required|string',
            'est_template_general' => 'boolean',
        ]);

        if (!$validated['est_template_general']) {
            $validated['medecin_id'] = auth()->user()->medecin_id;
        }

        $validated['is_actif'] = true;
        ModeleOrdonnance::create($validated);

        return redirect()->route('parametres.ordonnances')->with('success', 'Modèle créé');
    }

    /**
     * Edit prescription template
     */
    public function editOrdonnance(ModeleOrdonnance $modele)
    {
        return view('parametres.ordonnances.edit', compact('modele'));
    }

    /**
     * Update prescription template
     */
    public function updateOrdonnance(Request $request, ModeleOrdonnance $modele)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'contenu_html' => 'required|string',
        ]);

        $modele->update($validated);

        return redirect()->route('parametres.ordonnances')->with('success', 'Modèle mis à jour');
    }

    /**
     * Delete prescription template
     */
    public function destroyOrdonnance(ModeleOrdonnance $modele)
    {
        $modele->delete();
        return redirect()->route('parametres.ordonnances')->with('success', 'Modèle supprimé');
    }
}
