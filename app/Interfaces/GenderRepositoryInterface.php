<?php

namespace App\Interfaces;

interface GenderRepositoryInterface
{
    public function getAllGenders();

    public function getGenderById(string $genderId);

    public function deleteGender(string $genderId);

    public function createGender(array $genderDetails);

    public function updateGender(string $genderId, array $newDetails);
}