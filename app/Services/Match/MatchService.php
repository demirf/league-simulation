<?php

namespace App\Services\Match;

use App\Models\Matches;
use App\Repositories\Match\MatchRepositoryInterface;
use App\Services\BaseService;
use App\Services\MatchStanding\MatchStandingServiceInterface;
use App\Services\Team\TeamServiceInterface;
use Illuminate\Support\Arr;

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

    public function playAll(int $tournamentId): void
    {
        $matches = $this->repository->allBy(['tournament_id' => $tournamentId, 'status' => Matches::PENDING]);

        foreach ($matches as $match) {
            $this->play($match->id, $match->week);
        }
    }

    public function playWeek(int $tournamentId, int $week): bool
    {
        $matches = $this->repository->allBy(['tournament_id' => $tournamentId, 'week' => $week, 'status' => Matches::PENDING]);

        foreach ($matches as $match) {
            $this->play($match->id, $week);
        }

        return true;
    }

    public function play($matchId, $week): void {
        $defaultWinRate = 25;
        $defaultLoseRate = 25;
        $match = $this->repository->find($matchId);
        $homeTeamId = $match->home_team_id;
        $awayTeamId = $match->away_team_id;
        $tournamentId = $match->tournament_id;

        $firstTeamStats = $this->matchStandingService->allBy(['team_id' => $homeTeamId, 'tournament_id' => $tournamentId])->first();
        $secondTeamStats = $this->matchStandingService->allBy(['team_id' => $awayTeamId, 'tournament_id' => $tournamentId])->first();

        $firstTeamPower = $this->teamService->getTeamPower($firstTeamStats);
        $secondTeamPower = $this->teamService->getTeamPower($secondTeamStats);
        $totalPower = $firstTeamPower + $secondTeamPower;

        $normalizedFirstTeamPower = ($firstTeamPower * 100) / $totalPower;
        $normalizedSecondTeamPower = ($secondTeamPower * 100) / $totalPower;

        if ($normalizedFirstTeamPower > $normalizedSecondTeamPower) {
            $powerDifference = (($normalizedFirstTeamPower - $normalizedSecondTeamPower) * 100 / $normalizedFirstTeamPower);
        } else {
            $powerDifference = (($normalizedSecondTeamPower - $normalizedFirstTeamPower) * 100 / $normalizedSecondTeamPower);
        }

        $winRate = ($defaultWinRate + $powerDifference * 3) / 250;
        $loseRate = ($defaultLoseRate - $powerDifference) / 250;

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
            'status' => Matches::COMPLETE,
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
            'loss' => $awayGoal < $homeGoal ? $secondTeamStats->loss + 1 : $secondTeamStats->loss,
            'draw' => $homeGoal === $awayGoal ? $secondTeamStats->draw + 1 : $secondTeamStats->draw,
            'goals_for' => $secondTeamStats->goals_for + $awayGoal,
            'points' => $isWinnerTeamAway ? $secondTeamStats->points + 3 : ($isWinnerTeamHome ? $secondTeamStats->points : $secondTeamStats->points + 1),
            'goals_against' => $secondTeamStats->goals_against + $homeGoal,
        ]);
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
            'status' => Matches::PENDING,
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

    public function getEstimations(int $tournamentId, $week): array
    {
        $matches = $this->repository->allBy(['tournament_id' => $tournamentId]);
        $standings = $this->matchStandingService->allBy(['tournament_id' => $tournamentId]);

        $teamPoints = [];
        foreach ($standings as $standing) {
            $teamPoints[$standing['team_id']] = $standing['points'];
        }

        $calculatedPoints = [];
        $probabilities = Matches::PROBABILITIES;

        for ($computedWeek = $week; $computedWeek < self::$TOTAL_ROUND; $computedWeek++) {
            foreach ($probabilities as $firstMatchProbability) {
                foreach ($probabilities as $secondMatchProbability) {
                    if (empty($calculatedPoints[$computedWeek][$firstMatchProbability][$secondMatchProbability])) {
                        $calculatedPoints[$computedWeek][$firstMatchProbability][$secondMatchProbability] = $teamPoints;
                    }
                    $this->updateCalculatePoints($calculatedPoints, $matches, $computedWeek, $firstMatchProbability, $secondMatchProbability, 0);
                    $this->updateCalculatePoints($calculatedPoints, $matches, $computedWeek, $firstMatchProbability, $secondMatchProbability, 1);
                }
            }
        }

        $teamWins = array_combine(array_keys($teamPoints), array_fill(0, count($teamPoints), 0));

        foreach ($calculatedPoints ? Arr::flatten($calculatedPoints, 2) : [$teamPoints] as $item) {
            foreach (array_keys($item, max($item)) as $key) {
                $teamWins[$key] += 1;
            }
        }

        $totalCount = array_sum($teamWins);

        return array_map(function ($teamId, $winCount) use ($totalCount, $standings) {
            $team = collect($standings)->where('team_id', $teamId)->first();
            $teamName = $team['team_name'];

            return [
                'name' => $teamName,
                'percent' => $totalCount ? $winCount * 100 / $totalCount : 0
            ];
        }, array_keys($teamWins), $teamWins);
    }

    private function updateCalculatePoints(&$calculatedPoints, $matches, $computedWeek, $firstMatchProbability, $secondMatchProbability, $index)
    {
        $checkerPossibility = $index ? $secondMatchProbability : $firstMatchProbability;

        foreach ($matches as $match) {
            if ($match->week == $computedWeek) {
                $homeTeamId = $match->home_team_id;
                $awayTeamId = $match->away_team_id;
                if ($checkerPossibility === Matches::WIN) {
                    $calculatedPoints[$computedWeek][$firstMatchProbability][$secondMatchProbability][$homeTeamId] += 3;
                } elseif ($checkerPossibility === Matches::DRAW) {
                    $calculatedPoints[$computedWeek][$firstMatchProbability][$secondMatchProbability][$homeTeamId] += 1;
                    $calculatedPoints[$computedWeek][$firstMatchProbability][$secondMatchProbability][$awayTeamId] += 1;
                } else {
                    $calculatedPoints[$computedWeek][$firstMatchProbability][$secondMatchProbability][$awayTeamId] += 3;
                }
            }
        }
    }
}
