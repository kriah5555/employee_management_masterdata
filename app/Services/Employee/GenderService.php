<?php

namespace App\Services\Employee;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\GenderRepository;
use App\Models\Employee\Gender;

class GenderService
{
    protected $genderRepository;

    public function __construct(GenderRepository $genderRepository)
    {
        $this->genderRepository = $genderRepository;
    }
    /**
     * Function to get all the employee types
     */
    public function index()
    {
        return $this->genderRepository->getAllGenders();
    }

    public function show(string $genderId)
    {
        return $this->genderRepository->getGenderById($genderId);
    }

    public function edit(string $genderId)
    {
        return [
            'details' => $this->show($genderId)
        ];
    }

    public function store(array $values): Gender
    {
        return $this->genderRepository->createGender($values);
    }

    public function update(Gender $gender, array $values)
    {
        return $this->genderRepository->updateGender($gender->id, $values);
    }

    public function delete(Gender $gender)
    {
        return $this->genderRepository->deleteGender($gender->id);
    }
}