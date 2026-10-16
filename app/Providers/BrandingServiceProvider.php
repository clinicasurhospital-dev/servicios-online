<?php

namespace App\Providers;

use App\Domain\Branding\BrandingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class BrandingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BrandingService::class, function () {
            return new BrandingService();
        });
    }

    public function boot(): void
    {
        $branding = $this->app->make(BrandingService::class);

        // Share branding service globally with all Blade views
        View::share('branding', $branding);
    }
}
