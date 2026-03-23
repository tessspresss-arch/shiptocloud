<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'envoi de SMS
    |
    */

    'sms' => [
        'enabled' => env('SMS_ENABLED', false),
        'provider' => env('SMS_PROVIDER', 'twilio'),
        'default_hours' => env('SMS_DEFAULT_HOURS', 24),

        // Twilio Configuration
        'twilio' => [
            'account_sid' => env('TWILIO_ACCOUNT_SID'),
            'auth_token' => env('TWILIO_AUTH_TOKEN'),
            'from_number' => env('TWILIO_FROM_NUMBER'),
        ],

        // AWS SNS Configuration
        'aws' => [
            'key' => env('AWS_SMS_KEY'),
            'secret' => env('AWS_SMS_SECRET'),
            'region' => env('AWS_SMS_REGION', 'eu-west-1'),
        ],
    ],
];
