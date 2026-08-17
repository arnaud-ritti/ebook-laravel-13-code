<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Clock;
use App\Support\SystemClock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as PackageTelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Clock::class, SystemClock::class);

        if (! $this->app->environment('production') && class_exists(PackageTelescopeServiceProvider::class)) {
            $this->app->register(PackageTelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());
    }
}
