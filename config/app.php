<?php

use Illuminate\Support\Facades\Facade;

return [
    'name' => env('APP_NAME', 'Gestão Processos'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'Europe/Lisbon'),
    'locale' => env('APP_LOCALE', 'pt'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'pt'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'pt_PT'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
    ],

    /*
    | Conta fixa CEO: não pode alterar o papel nem desativar (evita perder acesso).
    | Definir FIXED_CEO_USER_ID (id do user) e/ou FIXED_CEO_EMAIL (email).
    */
    'fixed_ceo_user_id' => env('FIXED_CEO_USER_ID') ? (int) env('FIXED_CEO_USER_ID') : null,
    'fixed_ceo_email' => env('FIXED_CEO_EMAIL'),
];
