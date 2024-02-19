<?php

namespace App\Repositories;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    protected Model $model;
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function find($id, $with = []): Model
    {
        return $this->model::with($with)->findOrFail($id);
    }

    public function get($with = []): Collection
    {
        return $this->model::with($with)->get();
    }

    public function getByConditions(array $conditions = [], array $with = [], array $has = []): Collection
    {
        return $this->model::getByConditions($conditions, $with, $has);
    }

    public function create($data): Model
    {
        return $this->model::create($data);
    }

    public function update($object, array $updatedDetails): bool
    {
        if ($object->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update employee type');
        }
    }

    public function delete($object): bool
    {
        if ($object->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete employee type');
        }
    }

    public function getActive(): Collection
    {
        return $this->model::getActive();
    }
}
