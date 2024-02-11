<?php

namespace App\Services\Match;

use App\Services\BaseServiceInterface;

interface MatchServiceInterface extends BaseServiceInterface
{
    public function createMatches($tournamentId): array;
    public function getAll($tournamentId): array;
}
