<?php

namespace App\Repositories;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\ReasonRepositoryInterface;
use App\Models\Reason;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReasonRepository implements ReasonRepositoryInterface
{
    public function getReasons(): Collection
    {
        return Reason::all();
    }
    public function getActiveReasons(): Collection
    {
        return Reason::where('status', '=', true)->get();
    }
    public function getReasonsByCategory($category): Collection
    {
        return Reason::where('status', '=', true)->where('category', '=', $category)->get();
    }

    public function getReasonById(string $reasonId, array $relations = []): Collection|Builder|Reason
    {
        return Reason::with($relations)->findOrFail($reasonId);
    }

    public function deleteReason(Reason $reason): bool
    {
        if ($reason->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete reason');
        }
    }

    public function createReason(array $details): Reason
    {
        return Reason::create($details);
    }

    public function updateReason(Reason $reason, array $updatedDetails): bool
    {
        if ($reason->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update reason');
        }
    }
    public function updateReasonContractTypes(Reason $reason, array $contractTypes)
    {
        return $reason->contractTypes()->sync($contractTypes ?? []);
    }
}