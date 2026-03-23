<?php

return [
    'engine' => env('MEDICAL_AI_ENGINE', 'openai_responses'),
    'history_limit' => (int) env('MEDICAL_AI_HISTORY_LIMIT', 8),
    'fallback' => [
        'enabled' => filter_var(env('MEDICAL_AI_FALLBACK_ENABLED', true), FILTER_VALIDATE_BOOL),
    ],
    'openai' => [
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'project' => env('OPENAI_PROJECT'),
        'model' => env('MEDICAL_AI_OPENAI_MODEL', 'gpt-5-mini'),
        'timeout' => (int) env('MEDICAL_AI_OPENAI_TIMEOUT', 30),
    ],
];
