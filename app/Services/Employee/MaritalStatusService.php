<?php

namespace App\Services\Employee;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\MaritalStatusRepository;
use App\Models\Employee\MaritalStatus;

class MaritalStatusService
{
    protected $maritalStatusRepository;

    public function __construct(MaritalStatusRepository $maritalStatusRepository)
    {
        $this->maritalStatusRepository = $maritalStatusRepository;
    }
    /**
     * Function to get all the employee types
     */
    public function index()
    {
        return $this->maritalStatusRepository->getAllMaritalStatuses();
    }

    public function show(string $maritalStatusId)
    {
        return $this->maritalStatusRepository->getMaritalStatusById($maritalStatusId);
    }

    public function edit(string $maritalStatusId)
    {
        return [
            'details' => $this->show($maritalStatusId)
        ];
    }

    public function store(array $values): MaritalStatus
    {
        return $this->maritalStatusRepository->createMaritalStatus($values);
    }

    public function update(MaritalStatus $maritalStatus, array $values)
    {
        return $this->maritalStatusRepository->updateMaritalStatus($maritalStatus->id, $values);
    }

    public function delete(MaritalStatus $maritalStatus)
    {
        return $this->maritalStatusRepository->deleteMaritalStatus($maritalStatus->id);
    }
}