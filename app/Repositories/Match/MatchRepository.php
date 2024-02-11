<?php

namespace App\Repositories\Match;

use App\Models\Matches;
use App\Repositories\BaseRepository;

class MatchRepository extends BaseRepository implements MatchRepositoryInterface {

    public function __construct()
    {
        parent::__construct(Matches::class);
    }
}
