<?php

namespace App\Repositories;

use App\Interfaces\GenderRepositoryInterface;
use App\Models\Employee\Gender;

class GenderRepository implements GenderRepositoryInterface
{
    public function getAllGenders()
    {
        return Gender::all();
    }

    public function getGenderById(string $genderId): Gender
    {
        return Gender::findOrFail($genderId);
    }

    public function deleteGender(string $genderId)
    {
        return Gender::destroy($genderId);
    }

    public function createGender(array $genderDetails): Gender
    {
        return Gender::create($genderDetails);
    }

    public function updateGender(string $genderId, array $newDetails)
    {
        return Gender::whereId($genderId)->update($newDetails);
    }
}