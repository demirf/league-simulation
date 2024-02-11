<?php

namespace App\Repositories\Tournament;

use App\Models\Tournament;
use App\Repositories\BaseRepository;

class TournamentRepository extends BaseRepository implements TournamentRepositoryInterface {

    public function __construct()
    {
        parent::__construct(Tournament::class);
    }
}
