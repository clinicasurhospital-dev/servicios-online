<?php

return [
    /*
    |--------------------------------------------------------------------------
    | White-Label Organization / Clinic Identity
    |--------------------------------------------------------------------------
    |
    | Centralized configuration for the healthcare provider. Allows easy
    | re-branding for future multi-tenant / white-label deployments.
    |
    */

    'client_name' => env('BRAND_NAME', 'SurHospital'),
    'platform_name' => env('BRAND_APP_NAME', 'Nexaris Link'),
    'tagline' => 'Plataforma de Gestión y Agendamiento Médico',
    'slogan' => 'Cuidamos de ti y de tu familia con excelencia médica y calidez humana.',

    /*
    |--------------------------------------------------------------------------
    | Color Palette & Visual Tokens (Hex)
    |--------------------------------------------------------------------------
    */
    'colors' => [
        'primary' => env('BRAND_PRIMARY_COLOR', '#0700FF'),
        'primary_dark' => '#0500C9',
        'primary_soft' => '#EEF0FF',
        'background' => '#F7F8FC',
        'surface' => '#FFFFFF',
        'text_primary' => '#172033',
        'text_secondary' => '#667085',
        'border' => '#E5E7EB',
        'success' => '#12B76A',
        'warning' => '#F79009',
        'error' => '#D92D20',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact & Institutional Details
    |--------------------------------------------------------------------------
    */
    'contact' => [
        'phone' => env('BRAND_PHONE', '+593 4 212 3456'),
        'whatsapp' => env('BRAND_WHATSAPP', '+593 99 999 9999'),
        'email' => env('BRAND_EMAIL', 'contacto@surhospital.com'),
        'appointments_email' => env('BRAND_APPOINTMENTS_EMAIL', 'citas@surhospital.com'),
        'emergency_phone' => '1800-SURHOSPITAL (787-467)',
        'address' => env('BRAND_ADDRESS', 'Av. Carlos Julio Arosemena Km 2.5, Guayaquil, Ecuador'),
        'city' => 'Guayaquil',
        'country' => 'Ecuador',
        'working_hours_display' => 'Lunes a Sábado: 07:00 a 19:00 | Emergencias 24/7',
        'google_maps_url' => 'https://maps.google.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bank Transfer Information
    |--------------------------------------------------------------------------
    */
    'banking' => [
        'bank_name' => 'Banco Bolivariano / Banco Pichincha',
        'account_type' => 'Cuenta Corriente',
        'account_number' => '00192837465',
        'account_holder' => 'SurHospital Clínica y Salud S.A.',
        'identification' => 'RUC: 0992345678001',
        'email_receipts' => 'pagos@surhospital.com',
        'instructions' => 'Por favor realiza la transferencia bancaria por el monto exacto y adjunta tu comprobante. Nuestro equipo validará el pago en un plazo máximo de 2 horas hábiles.',
    ],

    /*
    |--------------------------------------------------------------------------
    | White-Label Display Rules
    |--------------------------------------------------------------------------
    */
    'display' => [
        'public_title' => 'SurHospital — Tu Salud en Manos de Especialistas',
        'patient_title' => 'SurHospital — Portal de Pacientes',
        'admin_title' => 'Nexaris Link | SurHospital',
        'powered_by_text' => 'Powered by Nexaris Link',
        'show_powered_by' => true,
        'minimum_reschedule_hours' => 24, // Regla de negocio para reprogramar
        'appointment_code_prefix' => 'SUR',
    ],
];
