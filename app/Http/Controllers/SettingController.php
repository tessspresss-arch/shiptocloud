<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        // Get all settings
        $allSettings = Setting::getAll();

        // Organize settings for display
        $settings = [
            'general' => [],
            'cabinet' => [],
            'users' => [],
            'medecins' => [],
            'notifications' => [],
            'security' => [],
        ];

        return view('parametres.index', compact('settings', 'allSettings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'required|in:string,integer,boolean,float,json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->settings as $settingData) {
                Setting::set(
                    $settingData['key'],
                    $settingData['value'],
                    $settingData['type'],
                    $settingData['category'] ?? 'general'
                );
            }

            // Clear cache to ensure fresh data
            Setting::clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Paramètres sauvegardés avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset settings to defaults
     */
    public function reset(Request $request)
    {
        try {
            // This would reset to default values - implement based on seeder
            // For now, just clear all settings
            Setting::query()->delete();
            Cache::forget('all_settings');

            // Re-seed default settings
            $this->seedDefaultSettings();

            return response()->json([
                'success' => true,
                'message' => 'Paramètres réinitialisés avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réinitialisation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Seed default settings
     */
    private function seedDefaultSettings()
    {
        $defaultSettings = [
            // General
            ['key' => 'app_name', 'value' => 'Cabinet Médical', 'type' => 'string', 'category' => 'general', 'label' => 'Nom de l\'application', 'description' => 'Nom affiché de l\'application'],
            ['key' => 'timezone', 'value' => 'Europe/Paris', 'type' => 'string', 'category' => 'general', 'label' => 'Fuseau horaire', 'description' => 'Fuseau horaire par défaut'],
            ['key' => 'language', 'value' => 'fr', 'type' => 'string', 'category' => 'general', 'label' => 'Langue', 'description' => 'Langue par défaut de l\'application'],

            // Cabinet
            ['key' => 'cabinet_name', 'value' => 'Cabinet Médical Central', 'type' => 'string', 'category' => 'cabinet', 'label' => 'Nom du cabinet', 'description' => 'Nom officiel du cabinet médical'],
            ['key' => 'cabinet_address', 'value' => '', 'type' => 'string', 'category' => 'cabinet', 'label' => 'Adresse', 'description' => 'Adresse complète du cabinet'],
            ['key' => 'cabinet_phone', 'value' => '', 'type' => 'string', 'category' => 'cabinet', 'label' => 'Téléphone', 'description' => 'Numéro de téléphone principal'],
            ['key' => 'cabinet_email', 'value' => '', 'type' => 'string', 'category' => 'cabinet', 'label' => 'Email', 'description' => 'Adresse email de contact'],

            // Users
            ['key' => 'default_user_role', 'value' => 'user', 'type' => 'string', 'category' => 'users', 'label' => 'Rôle par défaut', 'description' => 'Rôle attribué aux nouveaux utilisateurs'],
            ['key' => 'password_min_length', 'value' => '8', 'type' => 'integer', 'category' => 'users', 'label' => 'Longueur minimale mot de passe', 'description' => 'Nombre minimum de caractères pour les mots de passe'],

            // Médecins
            ['key' => 'default_consultation_duration', 'value' => '30', 'type' => 'integer', 'category' => 'medecins', 'label' => 'Durée consultation par défaut', 'description' => 'Durée par défaut des consultations en minutes'],
            ['key' => 'working_hours_start', 'value' => '08:00', 'type' => 'string', 'category' => 'medecins', 'label' => 'Heure début travail', 'description' => 'Heure de début des consultations'],
            ['key' => 'working_hours_end', 'value' => '18:00', 'type' => 'string', 'category' => 'medecins', 'label' => 'Heure fin travail', 'description' => 'Heure de fin des consultations'],

            // Notifications
            ['key' => 'email_reminders', 'value' => '1', 'type' => 'boolean', 'category' => 'notifications', 'label' => 'Rappels par email', 'description' => 'Activer les rappels automatiques par email'],
            ['key' => 'sms_reminders', 'value' => '0', 'type' => 'boolean', 'category' => 'notifications', 'label' => 'Rappels par SMS', 'description' => 'Activer les rappels automatiques par SMS'],

            // Security
            ['key' => 'session_timeout', 'value' => '7200', 'type' => 'integer', 'category' => 'security', 'label' => 'Timeout session', 'description' => 'Durée d\'inactivité avant déconnexion automatique (secondes)'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'category' => 'security', 'label' => 'Tentatives de connexion max', 'description' => 'Nombre maximum de tentatives de connexion avant blocage'],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::set($setting['key'], $setting['value'], $setting['type'], $setting['category']);
        }
    }
}
