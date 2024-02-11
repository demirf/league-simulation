<?php

namespace App\Services\Team;

use App\Repositories\Team\TeamRepositoryInterface;
use App\Services\BaseService;

class TeamService extends BaseService implements TeamServiceInterface {
    public function __construct(TeamRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    public function getTeamPower($teamStat): float {
        $defaultTeamPower = 50;

        return $defaultTeamPower + $teamStat->win * 3 - $teamStat->loss * 3 + $teamStat->draw * 1;
    }
}
