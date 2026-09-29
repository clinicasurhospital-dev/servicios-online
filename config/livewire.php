<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Class Namespace
    |---------------------------------------------------------------------------
    |
    | This value sets the root class namespace for Livewire component classes in
    | your application. This value will change where component auto-discovery
    | finds components. It's also referenced by the make:livewire command.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |---------------------------------------------------------------------------
    | View Path
    |---------------------------------------------------------------------------
    |
    | This value is used to specify where Livewire component views are stored.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |---------------------------------------------------------------------------
    | Layout
    |---------------------------------------------------------------------------
    |
    | The view that will be used as the default layout for Livewire components.
    |
    */

    'layout' => 'layouts.app',

    /*
    |---------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |---------------------------------------------------------------------------
    |
    | Livewire allows you to load components lazily. Here you may specify a
    | default view to use as the placeholder while component loads.
    |
    */

    'lazy_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Livewire Assets URL
    |---------------------------------------------------------------------------
    |
    | This value sets the path to Livewire JavaScript assets. Setting this to
    | a relative path ensures assets load properly across all ports and hosts.
    |
    */

    'asset_url' => env('LIVEWIRE_ASSET_URL', null),

    /*
    |---------------------------------------------------------------------------
    | Livewire App URL
    |---------------------------------------------------------------------------
    |
    | This value should match the url of your application. It is used in
    | Livewire to generate URLs to its endpoints.
    |
    */

    'app_url' => null,

    /*
    |---------------------------------------------------------------------------
    | Livewire Update Endpoint
    |---------------------------------------------------------------------------
    |
    | This value sets the path to Livewire update endpoint.
    |
    */

    'update_uri' => '/livewire/update',

    /*
    |---------------------------------------------------------------------------
    | Back Button Cache
    |---------------------------------------------------------------------------
    |
    | This value determines whether the back button cache will be used for
    | pages using Livewire. By default, this is set to "off".
    |
    */

    'back_button_cache' => false,

    /*
    |---------------------------------------------------------------------------
    | Render On Redirect
    |---------------------------------------------------------------------------
    |
    | This value determines whether Livewire will render before redirecting
    | or not.
    |
    */

    'render_on_redirect' => false,

];
