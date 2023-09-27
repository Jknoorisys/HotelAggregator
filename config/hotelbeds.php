<?php

    return [
        'end-point' => env('ENDPOINT'),
        'secure-end-point' => env('SECURE_ENDPOINT'),

        'hotel' => [
            'Api-key' => env('HOTEL_API_KEY'),
            'secret' => env('HOTEL_SECRET'),
        ],

        'activites' => [
            'Api-key' => env('ACTIVITIES_API_KEY'),
            'secret' => env('ACTIVITIES_SECRET'),
        ],

        'transfer' => [
            'Api-key' => env('TRANSFER_API_KEY'),
            'secret' => env('TRANSFER_SECRET'),
        ] 
    ];