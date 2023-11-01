<?php

namespace App\Interfaces\Interim;

use App\Models\Interim\InterimAgency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface InterimAgencyRepositoryInterface
{
    public function getInterimAgencies(): Collection;

    public function getActiveInterimAgencies(): Collection;

    public function getInterimAgencyById(string $interimAgencyId): Collection|Builder|InterimAgency;

    public function deleteInterimAgency(InterimAgency $interimAgency): bool;

    public function createInterimAgency(array $details): InterimAgency;

    public function updateInterimAgency(InterimAgency $interimAgency, array $updatedDetails);
}
