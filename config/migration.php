<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Manual Migration Feature Flags
    |--------------------------------------------------------------------------
    |
    | This configuration controls the gradual migration from Filament to the
    | manual implementation. Toggle modules here to switch between systems.
    |
    */

    'enabled' => true,

    'route_prefix' => 'app',

    'modules' => [
        'dashboard' => true,
        'inventory' => true,
        'clinical' => true,
        'finance' => true,
        'settings' => true,
        'management' => true,
    ],
];
