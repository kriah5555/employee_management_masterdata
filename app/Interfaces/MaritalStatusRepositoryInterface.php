<?php

namespace App\Interfaces;

interface MaritalStatusRepositoryInterface
{
    public function getAllMaritalStatuses();

    public function getMaritalStatusById(string $maritalStatusId);

    public function deleteMaritalStatus(string $maritalStatusId);

    public function createMaritalStatus(array $maritalStatusDetails);

    public function updateMaritalStatus(string $maritalStatusId, array $newDetails);
}