<?php

/*
 * Public business details shown in the footer and on the contact page.
 * Leave a value empty and the UI hides it, so the site never shows a made-up
 * phone number or a social icon that goes nowhere.
 */
return [
    // Port of the Vite dev server, allowed by the Content-Security-Policy in local development only.
    'vite_dev_port' => (int) env('VITE_DEV_PORT', 5173),

    'contact' => [
        'email' => env('CATERINGKU_CONTACT_EMAIL'),
        'phone' => env('CATERINGKU_CONTACT_PHONE'),
        'address' => env('CATERINGKU_CONTACT_ADDRESS'),
        'hours' => env('CATERINGKU_CONTACT_HOURS'),
    ],

    'social' => array_filter([
        'instagram' => env('CATERINGKU_INSTAGRAM_URL'),
        'whatsapp' => env('CATERINGKU_WHATSAPP_URL'),
        'telegram' => env('CATERINGKU_TELEGRAM_URL'),
        'x' => env('CATERINGKU_X_URL'),
    ]),
];
