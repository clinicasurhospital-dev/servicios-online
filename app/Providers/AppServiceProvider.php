<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Payments\Contracts\PaymentGatewayInterface::class,
            \App\Domain\Payments\Gateways\MockOnlinePaymentGateway::class
        );
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Model::unguard();

        \Illuminate\Support\Facades\View::share('branding', $this->app->make(\App\Domain\Branding\BrandingService::class));
    }
}
