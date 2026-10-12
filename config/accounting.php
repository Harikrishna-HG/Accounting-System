<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business profile
    |--------------------------------------------------------------------------
    |
    | Shown on every invoice. Keep the details in the environment so the same
    | build can be deployed for a different company without a code change.
    |
    */

    'business' => [
        'name' => env('BUSINESS_NAME', env('APP_NAME', 'Accounting System')),
        'address' => env('BUSINESS_ADDRESS', 'Nepal'),
        'phone' => env('BUSINESS_PHONE', ''),
        'email' => env('BUSINESS_EMAIL', ''),
        'pan' => env('BUSINESS_PAN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF fonts
    |--------------------------------------------------------------------------
    |
    | Mukta is bundled because it covers both Devanagari (Nepali dates and
    | names) and Latin text in a single family, so no glyph fallback is needed.
    |
    */

    'fonts' => [
        'family' => 'mukta',
        'regular' => resource_path('fonts/Mukta-Regular.ttf'),
        'bold' => resource_path('fonts/Mukta-Bold.ttf'),
    ],

];
