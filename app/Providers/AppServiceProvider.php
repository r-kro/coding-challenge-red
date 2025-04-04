<?php

namespace App\Providers;

use App\Services\ProviderPortal;
use App\Services\MockProviderClient;
use App\Services\MockProviderPortal;
use Illuminate\Support\ServiceProvider;
use App\Services\ProviderClientInterface;
use App\Services\ProviderPortalInterface;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProviderClientInterface::class, function() {
            if (env('PROVIDER_USE_MOCK', true)) {
                return new MockProviderClient();
            }

            return new MockProviderClient(); // TODO: create provider client
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(ProviderPortalInterface::class, function() {
            if (env('PROVIDER_USE_MOCK', true)) {
                return new MockProviderPortal();
            }
    
            return new ProviderPortal();
        });
    }
}
