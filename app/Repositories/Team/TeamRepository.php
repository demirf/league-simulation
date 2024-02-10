<?php

namespace App\Repositories\Team;

use App\Models\Team;
use App\Repositories\BaseRepository;

class TeamRepository extends BaseRepository implements TeamRepositoryInterface {

    public function __construct()
    {
        parent::__construct(Team::class);
    }
}
