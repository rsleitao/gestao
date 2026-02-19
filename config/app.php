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
];
