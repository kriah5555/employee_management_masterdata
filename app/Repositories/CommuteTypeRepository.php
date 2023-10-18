<?php

namespace App\Repositories;

use App\Interfaces\CommuteTypeRepositoryInterface;
use App\Models\Employee\CommuteType;

class CommuteTypeRepository implements CommuteTypeRepositoryInterface
{
    public function getAllCommuteTypes()
    {
        return CommuteType::all();
    }

    public function getCommuteTypeById(string $commuteTypeId): CommuteType
    {
        return CommuteType::findOrFail($commuteTypeId);
    }

    public function deleteCommuteType(string $commuteTypeId)
    {
        return CommuteType::destroy($commuteTypeId);
    }

    public function createCommuteType(array $commuteTypeDetails): CommuteType
    {
        return CommuteType::create($commuteTypeDetails);
    }

    public function updateCommuteType(string $commuteTypeId, array $newDetails)
    {
        return CommuteType::whereId($commuteTypeId)->update($newDetails);
    }
}