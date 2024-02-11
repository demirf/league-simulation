<?php

namespace App\Services\Tournament;

use App\Repositories\Tournament\TournamentRepositoryInterface;
use App\Services\BaseService;
use App\Services\Match\MatchServiceInterface;
use Illuminate\Database\Eloquent\Model;

class TournamentService extends BaseService implements TournamentServiceInterface {
    protected MatchServiceInterface $matchService;

    public function __construct(TournamentRepositoryInterface $repository, MatchServiceInterface $matchService)
    {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->matchService = $matchService;
    }

    public function create($data): Model
    {
        $result = $this->repository->create($data);
        $this->matchService->createMatches($result->id);

        return $result;
    }
}
