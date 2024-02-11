<?php

namespace App\Repositories\MatchStanding;

use App\Models\MatchStandings;
use App\Repositories\BaseRepository;

class MatchStandingRepository extends BaseRepository implements MatchStandingRepositoryInterface {

    public function __construct()
    {
        parent::__construct(MatchStandings::class);
    }
}
