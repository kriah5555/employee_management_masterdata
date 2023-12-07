<?php

namespace App\Services\Employee;

use App\Repositories\CommuteTypeRepository;
use App\Models\Company\Employee\CommuteType;

class CommuteTypeService
{
    protected $commuteTypeRepository;

    public function __construct(CommuteTypeRepository $commuteTypeRepository)
    {
        $this->commuteTypeRepository = $commuteTypeRepository;
    }
    /**
     * Function to get all the employee types
     */
    public function index()
    {
        return $this->commuteTypeRepository->getCommuteTypes();
    }

    public function show(string $commuteTypeId)
    {
        return $this->commuteTypeRepository->getCommuteTypeById($commuteTypeId);
    }

    public function edit(string $commuteTypeId)
    {
        return [
            'details' => $this->show($commuteTypeId)
        ];
    }

    public function store(array $values): CommuteType
    {
        return $this->commuteTypeRepository->createCommuteType($values);
    }

    public function update(CommuteType $commuteType, array $values)
    {
        return $this->commuteTypeRepository->updateCommuteType($commuteType->id, $values);
    }

    public function delete(CommuteType $commuteType)
    {
        return $this->commuteTypeRepository->deleteCommuteType($commuteType->id);
    }

    public function getActiveCommuteTypes()
    {
        return $this->commuteTypeRepository->getActiveCommuteTypes();
    }
}
