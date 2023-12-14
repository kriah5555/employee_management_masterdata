<?php

namespace App\Interfaces\Planning;

use App\Models\Planning\PlanningBase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface PlanningRepositoryInterface
{
    public function getPlannings(): Collection;

    public function getPlanningById(string $id, array $relations = []): Collection|Builder|PlanningBase;

    public function deletePlanning(PlanningBase $planning): bool;

    public function createPlanning(array $details): PlanningBase;

    public function updatePlanning(PlanningBase $planning, array $updatedDetails): bool;
}
