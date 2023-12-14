<?php

namespace App\Repositories\Planning;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\Planning\PlanningRepositoryInterface;
use App\Models\Planning\PlanningBase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Company\Company;

class PlanningRepository implements PlanningRepositoryInterface
{
    public function getPlannings(): Collection
    {
        return PlanningBase::all();
    }

    public function getPlanningById(string $id, array $relations = []): Collection|Builder|PlanningBase
    {
        return PlanningBase::with($relations)->findOrFail($id);
    }

    public function deletePlanning(PlanningBase $planning): bool
    {
        if ($planning->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete planning');
        }
    }

    public function createPlanning(array $details): PlanningBase
    {
        return PlanningBase::create($details);
    }

    public function updatePlanning(PlanningBase $planning, array $updatedDetails): bool
    {
        if ($planning->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update planning');
        }
    }
}
