<?php

namespace App\Providers;

use App\Extensions\Repositories\Maps\MapRepositoryInterface;
use App\Extensions\Repositories\Maps\MapStorageRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MapRepositoryInterface::class, MapStorageRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('map_search', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
