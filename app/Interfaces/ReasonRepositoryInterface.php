<?php

namespace App\Interfaces;

use App\Models\Reason;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface ReasonRepositoryInterface
{
    public function getReasons(): Collection;

    public function getActiveReasons(): Collection;

    public function getReasonById(string $reasonId, array $relations = []): Collection|Builder|Reason;

    public function deleteReason(Reason $reason): bool;

    public function createReason(array $details): Reason;

    public function updateReason(Reason $reason, array $updatedDetails): bool;

    public function updateReasonContractTypes(Reason $reason, array $contractTypes);
}