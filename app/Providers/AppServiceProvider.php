<?php

namespace App\Providers;

use App\Repositories\Match\MatchRepository;
use App\Repositories\Match\MatchRepositoryInterface;
use App\Repositories\MatchStanding\MatchStandingRepository;
use App\Repositories\MatchStanding\MatchStandingRepositoryInterface;
use App\Repositories\Team\TeamRepository;
use App\Repositories\Team\TeamRepositoryInterface;
use App\Repositories\Tournament\TournamentRepository;
use App\Repositories\Tournament\TournamentRepositoryInterface;
use App\Services\Match\MatchService;
use App\Services\Match\MatchServiceInterface;
use App\Services\MatchStanding\MatchStandingService;
use App\Services\MatchStanding\MatchStandingServiceInterface;
use App\Services\Team\TeamService;
use App\Services\Team\TeamServiceInterface;
use App\Services\Tournament\TournamentService;
use App\Services\Tournament\TournamentServiceInterface;
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

        $this->app->bind(TournamentRepositoryInterface::class, TournamentRepository::class);
        $this->app->bind(TournamentServiceInterface::class, TournamentService::class);

        $this->app->bind(MatchRepositoryInterface::class, MatchRepository::class);
        $this->app->bind(MatchServiceInterface::class, MatchService::class);

        $this->app->bind(MatchStandingRepositoryInterface::class, MatchStandingRepository::class);
        $this->app->bind(MatchStandingServiceInterface::class, MatchStandingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
