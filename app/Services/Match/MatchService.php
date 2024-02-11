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

    public function getAll(int $tournamentId): array
    {
        $allMatches = $this->repository->allBy(['tournament_id' => $tournamentId]);
        $groupedMatches = [];

        foreach ($allMatches as $match) {
            $groupedMatches[$match->week - 1][] = [
                'week' => $match->week,
                'home_team' => $match->homeTeam->name,
                'home_team_logo' => $match->homeTeam->logo_url,
                'home_team_goals' => $match->home_team_goals,
                'tournament_id' => $tournamentId,
                'away_team' => $match->awayTeam->name,
                'away_team_logo' => $match->awayTeam->logo_url,
                'away_team_goals' => $match->home_team_goals,
                'status' => $match->status,
            ];
        }

        return $groupedMatches;
    }

    public function getMatchStandings(int $tournamentId): array
    {
        $matchStandings = $this->matchStandingService->allBy(['tournament_id' => $tournamentId]);

        return $matchStandings->toArray();
    }

    public function play(int $tournamentId, int $week): bool
    {
        $matches = $this->repository->allBy(['tournament_id' => $tournamentId, 'week' => $week, 'status' => MatchStatus::Pending]);

        foreach ($matches as $match) {
            $defaultWinRate = 25;
            $defaultLoseRate = 25;
            $matchId = $match->id;
            $homeTeamId = $match->home_team_id;
            $awayTeamId = $match->away_team_id;

            $firstTeamStats = $this->matchStandingService->allBy(['team_id' => $homeTeamId])->first();
            $secondTeamStats = $this->matchStandingService->allBy(['team_id' => $awayTeamId])->first();

            $firstTeamPower = $this->teamService->getTeamPower($firstTeamStats);
            $secondTeamPower =  $this->teamService->getTeamPower($secondTeamStats);
            $totalPower = $firstTeamPower + $secondTeamPower;

            $normalizedFirstTeamPower = ($firstTeamPower * 100) / $totalPower;
            $normalizedSecondTeamPower = ($secondTeamPower * 100) / $totalPower;

            if ($normalizedFirstTeamPower > $normalizedSecondTeamPower) {
                $powerDifference = (($normalizedFirstTeamPower - $normalizedSecondTeamPower) * 100 / $normalizedFirstTeamPower);
            } else {
                $powerDifference = (($normalizedSecondTeamPower - $normalizedFirstTeamPower) * 100 / $normalizedSecondTeamPower);
            }

            $rate = $powerDifference / 6;

            $winRate = ($defaultWinRate + $rate * 3) / 500;
            $loseRate = ($defaultLoseRate - $rate) / 500;

            $homeGoal = 0;
            $awayGoal = 0;

            for ($i = 0; $i < 15; $i++) {
                if ((float)rand() / (float)getrandmax() < $winRate) {
                    $homeGoal++;
                }
                if ((float)rand() / (float)getrandmax() < $loseRate) {
                    $awayGoal++;
                }
            }

            $this->repository->update($matchId, [
                'home_team_goals' => $homeGoal,
                'away_team_goals' => $awayGoal,
                'status' => MatchStatus::Complete,
            ]);

            $isWinnerTeamHome = $homeGoal > $awayGoal;
            $isWinnerTeamAway = $awayGoal > $homeGoal;

            $this->matchStandingService->update($firstTeamStats->id, [
                'win' => $homeGoal > $awayGoal ? $firstTeamStats->win + 1 : $firstTeamStats->win,
                'loss' => $homeGoal < $awayGoal ? $firstTeamStats->loss + 1 : $firstTeamStats->loss,
                'draw' => $homeGoal === $awayGoal ? $firstTeamStats->draw + 1 : $firstTeamStats->draw,
                'goals_for' => $firstTeamStats->goals_for + $homeGoal,
                'points' => $isWinnerTeamHome ? $firstTeamStats->points + 3 : ($isWinnerTeamAway ? $firstTeamStats->points : $firstTeamStats->points + 1),
                'goals_against' => $firstTeamStats->goals_against + $awayGoal,
            ]);

            $this->matchStandingService->update($secondTeamStats->id, [
                'win' => $awayGoal > $homeGoal ? $secondTeamStats->win + 1 : $secondTeamStats->win,
                'loss' => $awayGoal < $homeGoal  ? $secondTeamStats->loss + 1 : $secondTeamStats->loss,
                'draw' => $homeGoal === $awayGoal ? $secondTeamStats->draw + 1 : $secondTeamStats->draw,
                'goals_for' => $secondTeamStats->goals_for + $awayGoal,
                'points' => $isWinnerTeamAway ? $secondTeamStats->points + 3 : ($isWinnerTeamHome ? $secondTeamStats->points : $secondTeamStats->points + 1),
                'goals_against' => $secondTeamStats->goals_against + $homeGoal,
            ]);
        }

        return true;
    }

    public function createMatches(int $tournamentId): array
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
