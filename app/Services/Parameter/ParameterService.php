<?php

namespace App\Services\Parameter;

use App\Models\Parameter\Parameter;
use Exception;
use App\Repositories\ParameterRepository;

class ParameterService
{
    protected $parameterRepository;

    public function __construct(ParameterRepository $parameterRepository)
    {
        $this->parameterRepository = $parameterRepository;
    }

    public function getDefaultParameters($values)
    {
        return $this->formatDefaultParameterDetails($this->parameterRepository->getDefaultParameters($values['type']));
    }
    public function formatDefaultParameterDetails($parameters)
    {
        $response = [];
        foreach ($parameters as $parameter) {
            $response[] = [
                'id'          => $parameter->id,
                'name'        => $parameter->name,
                'description' => $parameter->description,
                'value'       => $parameter->value,
            ];
        }
        return $response;
    }
    public function updateDefaultParameter($parameterId, $values)
    {
        $parameter = $this->parameterRepository->getParameterById($parameterId);
        return $this->parameterRepository->updateDefaultParameter($parameter, $values);
    }
    public function getEmployeeTypeParameters($employeeTypeId)
    {
        return $this->parameterRepository->getEmployeeTypeParameters($employeeTypeId);
    }
    public function updateEmployeeTypeParameter($parameterId, $values)
    {
        $parameter = $this->parameterRepository->getEmployeeTypeParameterById($parameterId);
        return $this->parameterRepository->updateEmployeeTypeParameter($parameter, $values);
    }
    public function getSectorParameters($sectorId)
    {
        return $this->parameterRepository->getSectorParameters($sectorId);
    }
}
