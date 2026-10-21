<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('files', function () {
            return new class extends \Illuminate\Filesystem\Filesystem {
                public function put($path, $contents, $lock = false)
                {
                    // Fallback to unlocked write if stream doesn't support LOCK_EX (e.g. WSL / UNC paths)
                    $result = @file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
                    if ($result === false) {
                        return file_put_contents($path, $contents);
                    }
                    return $result;
                }
            };
        });

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

        \Illuminate\Support\Facades\Blade::if('canBook', function () {
            return app(\App\Domain\Branding\BrandingService::class)->canShowBookingButton();
        });
    }
}
