<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => false,
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'receipts' => [
            'driver' => 'local',
            'root' => storage_path('app/private/receipts'),
            'serve' => false,
            'visibility' => 'private',
            'throw' => false,
        ],

        'medical_records' => [
            'driver' => 'local',
            'root' => storage_path('app/private/medical_records'),
            'serve' => false,
            'visibility' => 'private',
            'throw' => false,
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
