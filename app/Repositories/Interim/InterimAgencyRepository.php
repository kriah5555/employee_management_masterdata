<?php

namespace App\Repositories\Interim;

use App\Interfaces\Interim\InterimAgencyRepositoryInterface;
use App\Models\Interim\InterimAgency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;

class InterimAgencyRepository implements InterimAgencyRepositoryInterface
{
    public function getInterimAgencies(): Collection
    {
        return InterimAgency::all();
    }
    public function getActiveInterimAgencies(): Collection
    {
        return InterimAgency::where('status', '=', true)->get();
    }

    public function getInterimAgencyById(string $interimAgencyId, array $relations = []): Collection|Builder|InterimAgency
    {
        return InterimAgency::with($relations)->findOrFail($interimAgencyId);
    }

    public function deleteInterimAgency(InterimAgency $interimAgency): bool
    {
        if ($interimAgency->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete interim agency');
        }
    }

    public function createInterimAgency(array $details): InterimAgency
    {
        return InterimAgency::create($details);
    }

    public function updateInterimAgency(InterimAgency $interimAgency, array $updatedDetails)
    {
        if ($interimAgency->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update interim agency');
        }
    }
    public function updateLinkedCompanies(InterimAgency $interimAgency, array $companies)
    {
        return $interimAgency->companies()->sync($companies ?? []);
    }
}
