<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    public function updateSidebarPreference(Request $request)
    {
        $request->validate([
            'collapsed' => 'required|boolean'
        ]);

        $user = Auth::user();

        if ($user) {
            $user->update([
                'sidebar_collapsed' => $request->collapsed,
                'sidebar_preferences_updated_at' => now()
            ]);

            // Mettre en cache pour la session
            session(['sidebar_collapsed' => $request->collapsed]);

            return response()->json([
                'success' => true,
                'message' => 'Préférence sidebar mise à jour',
                'collapsed' => $request->collapsed
            ]);
        }

        // Pour les utilisateurs non connectés, stocker en session seulement
        session(['sidebar_collapsed' => $request->collapsed]);

        return response()->json([
            'success' => true,
            'message' => 'Préférence sidebar mise à jour (session)',
            'collapsed' => $request->collapsed
        ]);
    }

    public function getUserPreferences()
    {
        $user = Auth::user();
        $preferences = [
            'sidebar_collapsed' => session('sidebar_collapsed',
                $user ? $user->sidebar_collapsed : false)
        ];

        return response()->json($preferences);
    }
}
