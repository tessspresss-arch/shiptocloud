<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'Cabinet Médical',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Nom de l\'application',
                'description' => 'Nom affiché de l\'application',
                'is_public' => true,
            ],
            [
                'key' => 'timezone',
                'value' => 'Europe/Paris',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Fuseau horaire',
                'description' => 'Fuseau horaire par défaut',
                'is_public' => true,
            ],
            [
                'key' => 'language',
                'value' => 'fr',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Langue',
                'description' => 'Langue par défaut de l\'application',
                'is_public' => true,
            ],

            // Cabinet Settings
            [
                'key' => 'cabinet_name',
                'value' => 'Cabinet Médical Central',
                'type' => 'string',
                'category' => 'cabinet',
                'label' => 'Nom du cabinet',
                'description' => 'Nom officiel du cabinet médical',
                'is_public' => true,
            ],
            [
                'key' => 'cabinet_address',
                'value' => '',
                'type' => 'string',
                'category' => 'cabinet',
                'label' => 'Adresse',
                'description' => 'Adresse complète du cabinet',
                'is_public' => true,
            ],
            [
                'key' => 'cabinet_phone',
                'value' => '',
                'type' => 'string',
                'category' => 'cabinet',
                'label' => 'Téléphone',
                'description' => 'Numéro de téléphone principal',
                'is_public' => true,
            ],
            [
                'key' => 'cabinet_email',
                'value' => '',
                'type' => 'string',
                'category' => 'cabinet',
                'label' => 'Email',
                'description' => 'Adresse email de contact',
                'is_public' => true,
            ],

            // User Settings
            [
                'key' => 'default_user_role',
                'value' => 'user',
                'type' => 'string',
                'category' => 'users',
                'label' => 'Rôle par défaut',
                'description' => 'Rôle attribué aux nouveaux utilisateurs',
                'is_public' => false,
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'category' => 'users',
                'label' => 'Longueur minimale mot de passe',
                'description' => 'Nombre minimum de caractères pour les mots de passe',
                'is_public' => false,
            ],

            // Médecins Settings
            [
                'key' => 'default_consultation_duration',
                'value' => '30',
                'type' => 'integer',
                'category' => 'medecins',
                'label' => 'Durée consultation par défaut',
                'description' => 'Durée par défaut des consultations en minutes',
                'is_public' => true,
            ],
            [
                'key' => 'working_hours_start',
                'value' => '08:00',
                'type' => 'string',
                'category' => 'medecins',
                'label' => 'Heure début travail',
                'description' => 'Heure de début des consultations',
                'is_public' => true,
            ],
            [
                'key' => 'working_hours_end',
                'value' => '18:00',
                'type' => 'string',
                'category' => 'medecins',
                'label' => 'Heure fin travail',
                'description' => 'Heure de fin des consultations',
                'is_public' => true,
            ],

            // Notification Settings
            [
                'key' => 'email_reminders',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'notifications',
                'label' => 'Rappels par email',
                'description' => 'Activer les rappels automatiques par email',
                'is_public' => false,
            ],
            [
                'key' => 'sms_reminders',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'notifications',
                'label' => 'Rappels par SMS',
                'description' => 'Activer les rappels automatiques par SMS',
                'is_public' => false,
            ],

            // Security Settings
            [
                'key' => 'session_timeout',
                'value' => '7200',
                'type' => 'integer',
                'category' => 'security',
                'label' => 'Timeout session',
                'description' => 'Durée d\'inactivité avant déconnexion automatique (secondes)',
                'is_public' => false,
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'category' => 'security',
                'label' => 'Tentatives de connexion max',
                'description' => 'Nombre maximum de tentatives de connexion avant blocage',
                'is_public' => false,
            ],
            [
                'key' => 'audit.retention_days',
                'value' => '365',
                'type' => 'integer',
                'category' => 'audit',
                'label' => 'Rétention logs audit (jours)',
                'description' => 'Nombre de jours de conservation des logs audit',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
