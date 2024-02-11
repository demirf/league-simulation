<?php

namespace App\Services\Match;

use App\Enums\MatchStatus;
use App\Repositories\Match\MatchRepositoryInterface;
use App\Services\BaseService;
use App\Services\MatchStanding\MatchStandingServiceInterface;
use App\Services\Team\TeamServiceInterface;

class MatchService extends BaseService implements MatchServiceInterface
{
    protected TeamServiceInterface $teamService;
    protected MatchStandingServiceInterface $matchStandingService;
    public function __construct(MatchRepositoryInterface $repository, TeamServiceInterface $teamService, MatchStandingServiceInterface $matchStandingService)
    {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->teamService = $teamService;
        $this->matchStandingService = $matchStandingService;
    }

    public function createMatches($tournamentId): array
    {
        $teams = $this->teamService->all();
        $fixture = $this->createFixture($teams->toArray(), $tournamentId);

        foreach ($teams as $team) {
            $this->matchStandingService->create([
                'team_id' => $team->id,
                'tournament_id' => $tournamentId,
                'team_name' => $team->name,
                'points' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'win' => 0,
                'draw' => 0,
                'loss' => 0,
            ]);
        }

        return $fixture;
    }

    private function createFixture(array $teams, int $tournamentId): array
    {
        $totalRounds = count($teams) - 1;
        $schedule = [];

        for ($round = 1; $round <= $totalRounds; $round++) {
            $playedMatches = [];

            foreach ($teams as $key => $team1) {
                $key2 = ($key + $round) % count($teams);
                $team2 = $teams[$key2];

                if (!isset($playedMatches[$key]) && !isset($playedMatches[$key2])) {
                    if ($round % 2 === 0) {
                        $this->createMatch($team1, $team2, $round, $tournamentId);
                    } else {
                        $this->createMatch($team2, $team1, $round, $tournamentId);
                    }

                    $schedule[$round][$key][$key2] = true;
                    $schedule[$round][$key2][$key] = true;
                    $playedMatches[$key] = true;
                    $playedMatches[$key2] = true;
                }
            }

            array_push($teams, array_shift($teams));
        }

        return $schedule;
    }

    private function createMatch($homeTeam, $awayTeam, $round, $tournamentId) {
        $this->repository->create([
            'home_team_id' => $homeTeam['id'],
            'away_team_id' => $awayTeam['id'],
            'week' => $round,
            'status' => MatchStatus::Pending,
            'tournament_id' => $tournamentId
        ]);
    }


}
