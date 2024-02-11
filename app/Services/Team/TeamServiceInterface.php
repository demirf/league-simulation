<?php

namespace App\Services\Team;

use App\Services\BaseServiceInterface;

interface TeamServiceInterface extends BaseServiceInterface {
    public function getTeamPower($teamStat): float;
}
