<?php

namespace App\Services\MatchStanding;

use App\Repositories\MatchStanding\MatchStandingRepositoryInterface;
use App\Services\BaseService;

class MatchStandingService extends BaseService implements MatchStandingServiceInterface {
    public function __construct(MatchStandingRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }
}
