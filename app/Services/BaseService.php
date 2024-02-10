<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BaseService implements BaseServiceInterface {
    public function __construct($model) {
        $this->model = $model;
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function allBy($parameters): Collection {
        return $this->model->allBy($parameters);
    }

    public function create($data): Model {
        return $this->model->create($data);
    }
}
