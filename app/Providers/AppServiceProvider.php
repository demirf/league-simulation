<?php

namespace App\Providers;

use App\Repositories\Team\TeamRepository;
use App\Repositories\Team\TeamRepositoryInterface;
use App\Services\Team\TeamService;
use App\Services\Team\TeamServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(TeamServiceInterface::class, TeamService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
