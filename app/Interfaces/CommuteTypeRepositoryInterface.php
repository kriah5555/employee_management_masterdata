<?php

namespace App\Interfaces;

interface CommuteTypeRepositoryInterface
{
    public function getAllCommuteTypes();

    public function getCommuteTypeById(string $commuteTypeId);

    public function deleteCommuteType(string $commuteTypeId);

    public function createCommuteType(array $commuteTypeDetails);

    public function updateCommuteType(string $commuteTypeId, array $newDetails);
}