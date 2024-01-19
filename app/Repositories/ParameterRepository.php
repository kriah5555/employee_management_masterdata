<?php

namespace App\Repositories;

use App\Interfaces\ParameterRepositoryInterface;
use App\Models\Parameter\Parameter;
use App\Models\Parameter\EmployeeTypeParameter;
use App\Models\Parameter\SectorParameter;
use App\Models\Parameter\EmployeeTypeSectorParameter;
use App\Models\Parameter\CompanyParameter;
use App\Models\Parameter\LocationParameter;
use App\Exceptions\ModelUpdateFailedException;

class ParameterRepository implements ParameterRepositoryInterface
{
    public function getAllParameters()
    {
        return Parameter::all();
    }
    public function getDefaultParameters($type)
    {
        return Parameter::where('type', $type)->get();
    }

    public function getParameterById(string $parameterId): Parameter
    {
        return Parameter::findOrFail($parameterId);
    }

    public function deleteParameter(string $parameterId)
    {
        Parameter::destroy($parameterId);
    }

    public function createParameter(array $parameterDetails): Parameter
    {
        return Parameter::create($parameterDetails);
    }

    public function updateDefaultParameter(Parameter $parameter, array $newDetails): bool
    {
        if ($parameter->update($newDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update parameter');
        }
    }
    public function getEmployeeTypeParameters($employeeTypeId)
    {
        $response = [];
        $employeeTypeParameters = Parameter::where('type', 1)->get();
        foreach ($employeeTypeParameters as $employeeTypeParameter) {
            $parameter = EmployeeTypeParameter::where('parameter_id', $employeeTypeParameter->id)
                ->where('employee_type_id', $employeeTypeId)->get()->first();
            if (!$parameter) {
                $parameter = EmployeeTypeParameter::create([
                    'parameter_id'     => $employeeTypeParameter->id,
                    'employee_type_id' => $employeeTypeId,
                    'value'            => $employeeTypeParameter->value,
                ]);
            }
            $response[] = $parameter;
        }
        return $response;
    }
    public function getSectorParameters($sectorId)
    {
        $response = [];
        $sectorParameters = Parameter::where('type', 2)->get();
        foreach ($sectorParameters as $sectorParameter) {
            $parameter = SectorParameter::where('parameter_id', $sectorParameter->id)
                ->where('sector_id', $sectorId)->get()->first();
            if (!$parameter) {
                $parameter = SectorParameter::create([
                    'parameter_id' => $sectorParameter->id,
                    'sector_id'    => $sectorId,
                    'value'        => $sectorParameter->value,
                ]);
            }
            $response[] = $parameter;
        }
        return $response;
    }
    public function getEmployeeTypeSectorParameters($employeeTypeId, $sectorId)
    {
        $response = [];
        $employeeTypeSectorParameters = Parameter::where('type', 3)->get();
        foreach ($employeeTypeSectorParameters as $employeeTypeSectorParameter) {
            $parameter = EmployeeTypeSectorParameter::where('parameter_id', $employeeTypeSectorParameter->id)
                ->where('employee_type_id', $employeeTypeId)
                ->where('sector_id', $sectorId)->get()->first();
            if (!$parameter) {
                $parameter = EmployeeTypeSectorParameter::create([
                    'parameter_id'     => $employeeTypeSectorParameter->id,
                    'employee_type_id' => $employeeTypeId,
                    'sector_id'        => $sectorId,
                    'value'            => $employeeTypeSectorParameter->value,
                ]);
            }
            $response[] = $parameter;
        }
        return $response;
    }
    public function getDefaultCompanyParameters()
    {
        $response = [];
        $locationParameters = $this->getDefaultParameters(4);
        foreach ($locationParameters as $locationParameter) {
            $response[] = $locationParameter;
        }
        return $response;
    }
    public function getDefaultLocationParameters()
    {
        $response = [];
        $locationParameters = $this->getDefaultParameters(5);
        foreach ($locationParameters as $locationParameter) {
            $response[] = $locationParameter;
        }
        return $response;
    }
    public function formatDefaultParameterCollection($parameters)
    {
        $response = [];
        foreach ($parameters as $parameter) {
            $response[] = $this->formatDefaultParameterDetails($parameter);
        }
        return $response;
    }
    public function formatDefaultParameterDetails($parameter)
    {
        return [
            'id'          => $parameter->id,
            'name'        => $parameter->name,
            'description' => $parameter->description,
            'value'       => $parameter->value,
        ];
    }
    public function formatParameterCollection($parameters)
    {
        $response = [];
        foreach ($parameters as $parameter) {
            $response[] = $this->formatParameterDetails($parameter);
        }
        return $response;
    }
    public function formatParameterDetails($parameter)
    {
        return [
            'id'          => $parameter->id,
            'name'        => $parameter->parameter->name,
            'description' => $parameter->parameter->description,
            'value'       => $parameter->value,
        ];
    }
    public function getEmployeeTypeParameterById(string $parameterId): EmployeeTypeParameter
    {
        return EmployeeTypeParameter::findOrFail($parameterId);
    }
    public function getEmployeeTypeParameter(string $defaultParameterId, $employeeTypeId): EmployeeTypeParameter
    {
        return EmployeeTypeParameter::where('parameter_id', $defaultParameterId)
            ->where('employee_type_id', $employeeTypeId)->get()->firstOrFail();
    }
    public function getSectorParameter(string $defaultParameterId, $sectorId): SectorParameter
    {
        return SectorParameter::where('parameter_id', $defaultParameterId)
            ->where('sector_id', $sectorId)->get()->firstOrFail();
    }
    public function getEmployeeTypeSectorParameter(string $defaultParameterId, $employeeTypeId, $sectorId): EmployeeTypeSectorParameter
    {
        return EmployeeTypeSectorParameter::where('parameter_id', $defaultParameterId)
            ->where('employee_type_id', $employeeTypeId)
            ->where('sector_id', $sectorId)->get()->firstOrFail();
    }
    public function getDefaultCompanyParameterByName(string $defaultParameterId, $employeeTypeId, $sectorId): EmployeeTypeSectorParameter
    {
        return Parameter::where('parameter_id', $defaultParameterId)
            ->where('employee_type_id', $employeeTypeId)
            ->where('sector_id', $sectorId)->get()->firstOrFail();
    }

    public function updateEmployeeTypeParameter(EmployeeTypeParameter $parameter, array $newDetails): bool
    {
        if ($parameter->update($newDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update parameter');
        }
    }
    public function getSectorParameterById(string $parameterId): SectorParameter
    {
        return SectorParameter::findOrFail($parameterId);
    }

    public function updateSectorParameter(SectorParameter $parameter, array $newDetails): bool
    {
        if ($parameter->update($newDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update parameter');
        }
    }
    public function getEmployeeTypeSectorParameterById(string $parameterId): EmployeeTypeSectorParameter
    {
        return EmployeeTypeSectorParameter::findOrFail($parameterId);
    }

    public function updateEmployeeTypeSectorParameter(EmployeeTypeSectorParameter $parameter, array $newDetails): bool
    {
        if ($parameter->update($newDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update parameter');
        }
    }
    public function getParameterByName(string $parameterName): Parameter|null
    {
        return Parameter::where('name', $parameterName)->get()->firstOrFail();
    }
    public function getCompanyParameter(Parameter|EmployeeTypeParameter|SectorParameter|EmployeeTypeSectorParameter $parameter): CompanyParameter|null
    {
        return CompanyParameter::where('parameter_id', $parameter->id)
            ->where('parameter_type', get_class($parameter))->get()->first();
    }
    public function getCompanyLocationParameter(Parameter $parameter, $locationId): LocationParameter|null
    {
        return LocationParameter::where('parameter_id', $parameter->id)
            ->where('location_id', $locationId)->get()->first();
    }
    public function getCompanyParameterByName(string $parameterName): CompanyParameter|null
    {
        return CompanyParameter::where('parameter_name', $parameterName)->get()->first();
    }
    public function getLocationParameterByName(string $locationId, string $parameterName): LocationParameter|null
    {
        return LocationParameter::where('location_id', $locationId)
            ->where('parameter_name', $parameterName)->get()->first();
    }

}
