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

    public static int $TOTAL_ROUND = 6;
    public function __construct(MatchRepositoryInterface $repository, TeamServiceInterface $teamService, MatchStandingServiceInterface $matchStandingService)
    {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->teamService = $teamService;
        $this->matchStandingService = $matchStandingService;
    }

    public function getAll($tournamentId): array {
        $allMatches = $this->repository->allBy(['tournament_id' => $tournamentId]);
        $groupedMatches = [];

        foreach ($allMatches as $match) {
            $groupedMatches[$match->week - 1][] = [
                'week' => $match->week,
                'home_team' => $match->homeTeam->name,
                'home_team_logo' => $match->homeTeam->logo_url,
                'away_team' => $match->awayTeam->name,
                'away_team_logo' => $match->awayTeam->logo_url,
            ];
        }

        return $groupedMatches;
    }

    public function createMatches($tournamentId): array
    {
        $teams = $this->teamService->all();
        $test = $teams->toArray();
        $fixture = $this->schedule($test, $tournamentId);

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

    private function schedule(array $teams, int $tournamentId): array
    {
        $teamCount = count($teams);
        $halfTeamCount = $teamCount / 2;
        $schedule = [];

        for ($round = 1; $round <= self::$TOTAL_ROUND; $round++) {
            for ($key = 0; $key < $halfTeamCount; $key++) {
                $team1 = $teams[$key];
                $team2 = $teams[$key + $halfTeamCount];

                $isEvenRound = $round % 2 === 0;
                $homeTeam = $isEvenRound ? $team1 : $team2;
                $awayTeam = $isEvenRound ? $team2 : $team1;

                $this->createMatch($homeTeam, $awayTeam, $round, $tournamentId);

                $schedule[$round][] = [$homeTeam, $awayTeam];
            }


            $this->rotateTeams($teams);
        }

        return $schedule;
    }

    private function createMatch($homeTeam, $awayTeam, $round, $tournamentId): void
    {
        $this->repository->create([
            'home_team_id' => $homeTeam['id'],
            'away_team_id' => $awayTeam['id'],
            'week' => $round,
            'status' => MatchStatus::Pending,
            'tournament_id' => $tournamentId
        ]);
    }

    private function rotateTeams(array &$items)
    {
        $itemCount = count($items);
        if ($itemCount < 3) {
            return;
        }

        $lastIndex = $itemCount - 1;
        $middleIndex = (int)($itemCount / 2);

        $topRightItem = $items[$middleIndex - 1];
        for ($i = $middleIndex - 1; $i > 0; $i--) {
            $items[$i] = $items[$i - 1];
        }

        $bottomLeftItem = $items[$middleIndex];
        for ($i = $middleIndex; $i < $lastIndex; $i++) {
            $items[$i] = $items[$i + 1];
        }

        $items[1] = $bottomLeftItem;
        $items[$lastIndex] = $topRightItem;
    }
}
