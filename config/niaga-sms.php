<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Niaga SMS API Configuration
    |--------------------------------------------------------------------------
    */

    'base_url' => env('NIAGA_SMS_BASE_URL', 'https://manage.smsniaga.com'),

    'api_token' => env('NIAGA_SMS_API_TOKEN'),

    'timeout' => env('NIAGA_SMS_TIMEOUT', 30),
    'sender_id' => env('NIAGA_SMS_SENDER_ID'),
];
