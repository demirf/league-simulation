<?php

namespace App\Services\MatchStanding;

use App\Services\BaseServiceInterface;

interface MatchStandingServiceInterface extends BaseServiceInterface {
    public function update($id, $data);
}
