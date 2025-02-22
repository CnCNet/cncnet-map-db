<?php

namespace App\Providers;

use App\Extensions\Repositories\Maps\MapRepositoryInterface;
use App\Extensions\Repositories\Maps\MapStorageRepository;
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
        //
    }
}
