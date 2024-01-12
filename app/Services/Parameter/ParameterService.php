<?php

namespace App\Services\Parameter;

use App\Models\Parameter\Parameter;
use Exception;
use App\Repositories\ParameterRepository;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Services\Sector\SectorService;
use App\Services\Company\LocationService;

class ParameterService
{
    protected $parameterRepository;
    protected $employeeTypeService;
    protected $sectorService;
    protected $locationService;

    public function __construct(ParameterRepository $parameterRepository, EmployeeTypeService $employeeTypeService, SectorService $sectorService, LocationService $locationService)
    {
        $this->parameterRepository = $parameterRepository;
        $this->employeeTypeService = $employeeTypeService;
        $this->sectorService = $sectorService;
        $this->locationService = $locationService;
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
    public function getManageParameterOptions()
    {
        $employeeTypes = $this->employeeTypeService->getEmployeeTypesOptions();
        $sectors = $this->sectorService->getActiveSectorsOptions();
        $locations = $this->locationService->getActiveLocationsOptions();
        return [
            'employee_types' => $employeeTypes,
            'sectors'        => $sectors,
            'locations'      => $locations,

        ];
    }
    public function getParameters($values)
    {
        if ($values['type'] == 1) {
            return $this->getEmployeeTypeParameters($values['id']);
        } elseif ($values['type'] == 2) {
            return $this->getSectorParameters($values['id']);
        } elseif ($values['type'] == 3) {
            return $this->parameterRepository->getEmployeeTypeSectorParameters($values['id'], $values['sector_id']);
        }
    }
    public function updateParameter($parameterId, $values)
    {
        if ($values['type'] == 1) {
            $status = $this->updateEmployeeTypeParameter($parameterId, $values);
        } elseif ($values['type'] == 2) {
            $status = $this->updateSectorParameter($parameterId, $values);
        } elseif ($values['type'] == 3) {
            $status = $this->updateEmployeeTypeSectorParameter($parameterId, $values);
        }
        return $status;
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
    public function updateSectorParameter($parameterId, $values)
    {
        $parameter = $this->parameterRepository->getSectorParameterById($parameterId);
        return $this->parameterRepository->updateSectorParameter($parameter, $values);
    }
    public function updateEmployeeTypeSectorParameter($parameterId, $values)
    {
        $parameter = $this->parameterRepository->getEmployeeTypeSectorParameterById($parameterId);
        return $this->parameterRepository->updateEmployeeTypeSectorParameter($parameter, $values);
    }
    public function getCompanyParameters($values)
    {
        $response = [];
        if ($values['type'] == 1) {
            $parameters = $this->getEmployeeTypeParameters($values['id']);
        } elseif ($values['type'] == 2) {
            $parameters = $this->getSectorParameters($values['id']);
        } elseif ($values['type'] == 3) {
            $parameters = $this->parameterRepository->getEmployeeTypeSectorParameters($values['id'], $values['sector_id']);
        } elseif ($values['type'] == 5) {
            $parameters = $this->parameterRepository->getDefaultLocationParameters();
        }
        foreach ($parameters as $parameter) {
            $details = [];
	    $details['id'] = $parameter['name'];
	    $details['name'] = $parameter['name'];
	    $details['description'] = $parameter['description'];
            $details['default_value'] = $parameter['value'];
            if ($values['type'] == 4) {
                $companyParameters = $this->parameterRepository->getCompanyParameterByName($parameter['name']);
            } else {
                $companyParameters = $this->parameterRepository->getLocationParameterByName($parameter['id'], $parameter['name']);
            }
            if ($companyParameters) {
                $details['use_default'] = false;
            } else {
                $details['use_default'] = true;
                $details['value'] = $parameter['value'];
            }
            $response[] = $details;
        }
        return $response;
    }
}
