<?php

namespace App\Services\Match;

use App\Services\BaseServiceInterface;

interface MatchServiceInterface extends BaseServiceInterface
{
    public function createMatches(int $tournamentId): array;
    public function getAll(int $tournamentId): array;
    public function getMatchStandings(int $tournamentId): array;
    public function play(int $tournamentId, int $week): bool;
}
