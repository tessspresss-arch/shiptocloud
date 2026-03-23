<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // ===== GÉNÉRAL =====
            ['key' => 'cabinet_name', 'value' => 'Cabinet Médical SCABINET', 'type' => 'string'],
            ['key' => 'email_principal', 'value' => 'contact@cabinet.com', 'type' => 'string'],
            ['key' => 'phone', 'value' => '+212 6 XX XX XX XX', 'type' => 'string'],
            ['key' => 'timezone', 'value' => 'Africa/Casablanca', 'type' => 'string'],
            ['key' => 'currency', 'value' => 'EUR', 'type' => 'string'],
            ['key' => 'language', 'value' => 'fr', 'type' => 'string'],
            ['key' => 'date_format', 'value' => 'd/m/Y', 'type' => 'string'],

            // ===== CABINET =====
            ['key' => 'cabinet_address', 'value' => '', 'type' => 'string'],
            ['key' => 'cabinet_city', 'value' => '', 'type' => 'string'],
            ['key' => 'cabinet_zip', 'value' => '', 'type' => 'string'],
            ['key' => 'siret', 'value' => '', 'type' => 'string'],
            ['key' => 'tva_number', 'value' => '', 'type' => 'string'],
            ['key' => 'hours_monday', 'value' => '08:00 - 18:00', 'type' => 'string'],
            ['key' => 'hours_tuesday', 'value' => '08:00 - 18:00', 'type' => 'string'],
            ['key' => 'hours_wednesday', 'value' => '08:00 - 18:00', 'type' => 'string'],
            ['key' => 'hours_thursday', 'value' => '08:00 - 18:00', 'type' => 'string'],
            ['key' => 'hours_friday', 'value' => '08:00 - 18:00', 'type' => 'string'],
            ['key' => 'hours_saturday', 'value' => 'Fermé', 'type' => 'string'],

            // ===== COMMUNICATION =====
            ['key' => 'smtp_host', 'value' => 'smtp.gmail.com', 'type' => 'string'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'integer'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'string'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'string'],
            ['key' => 'sms_provider', 'value' => 'twilio', 'type' => 'string'],
            ['key' => 'sms_api_key', 'value' => '', 'type' => 'string'],
            ['key' => 'email_notifications', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'sms_notifications', 'value' => '0', 'type' => 'boolean'],

            // ===== MÉDICAL =====
            ['key' => 'services', 'value' => 'Consultation, Diagnostic, Traitement', 'type' => 'string'],
            ['key' => 'consultation_duration', 'value' => '30', 'type' => 'integer'],
            ['key' => 'rdv_min_gap', 'value' => '15', 'type' => 'integer'],
            ['key' => 'allow_export', 'value' => '1', 'type' => 'boolean'],

            // ===== SÉCURITÉ =====
            ['key' => 'session_timeout', 'value' => '120', 'type' => 'integer'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer'],
            ['key' => 'encrypt_data', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'two_factor_auth', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'hide_sensitive', 'value' => '0', 'type' => 'boolean'],

            // ===== SAUVEGARDES =====
            ['key' => 'backup_frequency', 'value' => 'daily', 'type' => 'string'],
            ['key' => 'backup_time', 'value' => '02:00', 'type' => 'string'],
            ['key' => 'backup_retention', 'value' => '10', 'type' => 'integer'],
            ['key' => 'cloud_provider', 'value' => 'none', 'type' => 'string'],

            // ===== INTÉGRATIONS =====
            ['key' => 'google_maps_key', 'value' => '', 'type' => 'string'],
            ['key' => 'webhook_consultation', 'value' => '', 'type' => 'string'],
            ['key' => 'webhook_payment', 'value' => '', 'type' => 'string'],
            ['key' => 'facebook_url', 'value' => '', 'type' => 'string'],
            ['key' => 'twitter_url', 'value' => '', 'type' => 'string'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }

        $this->command->info('✅ ' . count($defaults) . ' paramètres par défaut créés/mis à jour');
    }
}
