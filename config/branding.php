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

    'client_name' => env('BRAND_NAME', 'Clínica Sur Hospital'),
    'platform_name' => env('BRAND_APP_NAME', 'SurHospital Citas'),
    'tagline' => 'Centro Hospitalario de Alta Especialidad y Agendamiento en Línea',
    'slogan' => 'Proporcionando atención oportuna en salud integral con estándares de calidad, ética profesional y calidez humana.',

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
        'phone' => env('BRAND_PHONE', '042590670'),
        'phone_display' => '042 590670 Ext. 100 / Ext. 0',
        'phone_ext' => 'Ext. 100 / Ext. 0',
        'whatsapp' => env('BRAND_WHATSAPP', '+593 99 778 3576'),
        'whatsapp_display' => '+593 99 778 3576',
        'email' => env('BRAND_EMAIL', 'info@surhospital.net'),
        'careers_email' => 'postulaciones@surhospital.net',
        'appointments_email' => env('BRAND_APPOINTMENTS_EMAIL', 'citas@surhospital.net'),
        'emergency_phone' => '042 590670 (Emergencias 24/7)',
        'address' => env('BRAND_ADDRESS', 'José Mascote 2123, entre Huancavilca y Capitán Nájera, Guayaquil, Ecuador'),
        'address_short' => 'José Mascote 2123, Guayaquil',
        'city' => 'Guayaquil',
        'province' => 'Guayas',
        'country' => 'Ecuador',
        'working_hours_display' => 'Consulta Externa: Lunes a Sábado | Emergencias, Laboratorio e Imágenes: 24 Horas (365 Días)',
        'google_maps_url' => 'https://maps.google.com/?q=Jose+Mascote+2123+Guayaquil+Clinica+Sur+Hospital',
    ],

    /*
    |--------------------------------------------------------------------------
    | Staff & Administrative Notification Channels
    |--------------------------------------------------------------------------
    |
    | Configurable email lists for administrative and reception personnel.
    | Comma-separated lists in .env (e.g. NOTIFICATION_ADMIN_EMAILS=admin@...,director@...)
    |
    */
    'notifications' => [
        'admin_emails' => array_values(array_filter(array_map('trim', explode(',', env('NOTIFICATION_ADMIN_EMAILS', env('BRAND_EMAIL', 'admin@surhospital.com')))))),
        'reception_emails' => array_values(array_filter(array_map('trim', explode(',', env('NOTIFICATION_RECEPTION_EMAILS', env('BRAND_APPOINTMENTS_EMAIL', 'recepcion@surhospital.com')))))),
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
        'account_holder' => 'Clínica Sur Hospital S.A.',
        'identification' => 'RUC: 0992345678001',
        'email_receipts' => 'pagos@surhospital.net',
        'instructions' => 'Por favor realiza la transferencia bancaria por el monto exacto y adjunta tu comprobante. Nuestro equipo validará el pago en un plazo máximo de 2 horas hábiles.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Patient Online Booking Availability Flag
    |--------------------------------------------------------------------------
    |
    | Controls whether the public appointment booking button is visible to
    | general visitors and patients. When false, it is only visible and
    | accessible to Administrator and Receptionist staff.
    |
    */
    'enable_patient_booking' => (bool) env('ENABLE_PATIENT_BOOKING', false),

    /*
    |--------------------------------------------------------------------------
    | White-Label Display Rules
    |--------------------------------------------------------------------------
    */
    'display' => [
        'public_title' => 'SurHospital — Tu Salud en Manos de Especialistas',
        'patient_title' => 'SurHospital — Portal del Paciente',
        'admin_title' => 'SurHospital | Nexaris Link',
        'powered_by_text' => 'Desarrollado por Nexaris Link',
        'show_powered_by' => true,
        'minimum_reschedule_hours' => (int) env('MIN_RESCHEDULE_HOURS', 1),
        'appointment_code_prefix' => 'SUR',
    ],
];
