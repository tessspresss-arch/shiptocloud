<?php

namespace App\Services;

use App\Models\SMSLog;
use App\Models\SMSReminder;
use Illuminate\Support\Facades\Log;

class SMSService
{
    /**
     * Send SMS message
     */
    public static function send($telephone, $message, $type = 'reminder', $patientId = null, $createBy = null)
    {
        try {
            $provider = config('services.sms.provider', 'twilio');
            $enabled = config('services.sms.enabled', false);

            if (!$enabled) {
                self::logSMS($telephone, $message, $type, 'echec', 'SMS désactivé', $patientId, $createBy);
                throw new \Exception('Service SMS désactivé');
            }

            // Appel au provider
            $result = match ($provider) {
                'twilio' => self::sendViaTwilio($telephone, $message),
                'aws-sns' => self::sendViaAWS($telephone, $message),
                default => throw new \Exception("Provider SMS inconnu: $provider")
            };

            self::logSMS($telephone, $message, $type, 'envoye', null, $patientId, $createBy, $result['id'] ?? null);

            return $result;
        } catch (\Exception $e) {
            self::logSMS($telephone, $message, $type, 'echec', $e->getMessage(), $patientId, $createBy);
            Log::error('Erreur envoi SMS', [
                'telephone' => $telephone,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Send via Twilio
     */
    private static function sendViaTwilio($telephone, $message)
    {
        $accountSid = config('services.sms.twilio_account_sid');
        $authToken = config('services.sms.twilio_auth_token');
        $fromNumber = config('services.sms.twilio_from_number');

        if (!$accountSid || !$authToken || !$fromNumber) {
            throw new \Exception('Configuration Twilio incomplète');
        }

        try {
            $client = new \Twilio\Rest\Client($accountSid, $authToken);
            
            $response = $client->messages->create(
                $telephone,
                [
                    'from' => $fromNumber,
                    'body' => $message
                ]
            );

            return [
                'id' => $response->sid,
                'statut' => $response->status
            ];
        } catch (\Twilio\Exceptions\TwilioException $e) {
            throw new \Exception('Erreur Twilio: ' . $e->getMessage());
        }
    }

    /**
     * Send via AWS SNS
     */
    private static function sendViaAWS($telephone, $message)
    {
        $awsKey = config('services.sms.aws_key');
        $awsSecret = config('services.sms.aws_secret');
        $awsRegion = config('services.sms.aws_region', 'eu-west-1');

        if (!$awsKey || !$awsSecret) {
            throw new \Exception('Configuration AWS incomplète');
        }

        try {
            $snsClient = new \Aws\Sns\SnsClient([
                'version' => 'latest',
                'region' => $awsRegion,
                'credentials' => [
                    'key' => $awsKey,
                    'secret' => $awsSecret,
                ]
            ]);

            $result = $snsClient->publish([
                'Message' => $message,
                'PhoneNumber' => $telephone,
            ]);

            return [
                'id' => $result['MessageId'],
                'statut' => 'sent'
            ];
        } catch (\Exception $e) {
            throw new \Exception('Erreur AWS SNS: ' . $e->getMessage());
        }
    }

    /**
     * Log SMS
     */
    private static function logSMS($telephone, $message, $type, $statut, $error = null, $patientId = null, $createdBy = null, $providerId = null)
    {
        SMSLog::create([
            'telephone' => $telephone,
            'message' => $message,
            'type' => $type,
            'statut' => $statut,
            'provider' => config('services.sms.provider', 'twilio'),
            'provider_message_id' => $providerId,
            'erreur_details' => $error,
            'patient_id' => $patientId,
            'created_by' => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * Send reminder for appointment
     */
    public static function sendReminder(SMSReminder $reminder)
    {
        try {
            $reminder->load('patient', 'rendezvous');

            $message = $reminder->message_template ?? self::getDefaultReminderMessage($reminder);

            self::send(
                $reminder->telephone,
                $message,
                'reminder',
                $reminder->patient_id
            );

            $reminder->update([
                'statut' => 'envoye',
                'date_envoi_reelle' => now(),
                'provider' => config('services.sms.provider', 'twilio')
            ]);

            return true;
        } catch (\Exception $e) {
            $reminder->update([
                'statut' => 'echec',
                'code_erreur' => 'SEND_ERROR',
                'erreur_message' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get default reminder message
     */
    private static function getDefaultReminderMessage(SMSReminder $reminder)
    {
        $patient = $reminder->patient;
        $rendezvous = $reminder->rendezvous;

        return sprintf(
            "Bonjour %s, rappel de votre rendez-vous le %s à %s. Confirmez votre présence ou annulez.",
            $patient->prenom ?? 'Monsieur/Madame',
            $rendezvous->date_heure->format('d/m/Y'),
            $rendezvous->date_heure->format('H:i')
        );
    }

    /**
     * Process pending reminders
     */
    public static function processPending()
    {
        $pending = SMSReminder::aEnvoyer()->get();

        $successful = 0;
        $failed = 0;

        foreach ($pending as $reminder) {
            if (self::sendReminder($reminder)) {
                $successful++;
            } else {
                $failed++;
            }
        }

        return [
            'successful' => $successful,
            'failed' => $failed,
            'total' => $successful + $failed
        ];
    }
}


