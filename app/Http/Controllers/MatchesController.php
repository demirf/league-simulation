<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMatchesRequest;
use App\Http\Requests\UpdateMatchesRequest;
use App\Models\Matches;
use App\Services\Match\MatchServiceInterface;
use Inertia\Inertia;

class MatchesController extends Controller
{
    protected MatchServiceInterface $service;

    public function __construct(MatchServiceInterface $service)
    {
        $this->service = $service;
    }

    public function getAll(int $tournamentId)
    {
        $result = $this->service->getAll($tournamentId);

        return Inertia::render('AllMatches', [
            'allMatches' => $result
        ]);
    }

    public function getByWeek(int $tournamentId, $week)
    {
        $matches = $this->service->allBy(['tournament_id' => $tournamentId, 'week' => $week]);
        $matchStandings = $this->service->getMatchStandings($tournamentId);
        $matchResources = [];

        foreach ($matches as $match) {
            $matchResources[] = [
                'week' => $match->week,
                'home_team' => $match->homeTeam->name,
                'home_team_logo' => $match->homeTeam->logo_url,
                'home_team_goals' => $match->home_team_goals,
                'tournament_id' => $tournamentId,
                'away_team' => $match->awayTeam->name,
                'away_team_logo' => $match->awayTeam->logo_url,
                'away_team_goals' => $match->away_team_goals,
                'status' => $match->status,
            ];
        }

        return Inertia::render('MatchWeek', [
            'matches' => $matchResources,
            'matchStandings' => $matchStandings,
        ]);
    }

    public function play(int $tournamentId, $week)
    {
        $this->service->playWeek($tournamentId, $week);

        return redirect()->route('matches.getByWeek', ['tournamentId' => $tournamentId, 'week' => $week]);
    }

    public function playAll(int $tournamentId)
    {
        $this->service->playAll($tournamentId);
        $lastWeek = 6;

        return redirect()->route('matches.getByWeek', ['tournamentId' => $tournamentId, 'week' => $lastWeek]);
    }
}
