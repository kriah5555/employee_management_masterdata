<?php

namespace App\Repositories;

use App\Interfaces\ParameterRepositoryInterface;
use App\Models\Parameter\Parameter;
use App\Models\Parameter\EmployeeTypeParameter;
use App\Models\Parameter\SectorParameter;
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

    public function updateEmployeeTypeParameter(EmployeeTypeParameter $parameter, array $newDetails): bool
    {
        if ($parameter->update($newDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update parameter');
        }
    }
    public function getSectorParameters($sectorId)
    {
        $response = [];
        $sectorParameters = Parameter::where('type', 2)->get();
        foreach ($sectorParameters as $sectorParameter) {
            $parameter = SectorParameter::firstOrCreate([
                'parameter_id' => $sectorParameter->id,
                'sector_id'    => $sectorId,
            ]);
            dd($parameter->wasRecentlyCreated);
            // $parameter->value =
            if (!$parameter) {
                $parameter = SectorParameter::create([
                    'parameter_id'     => $sectorParameter->id,
                    'employee_type_id' => $sectorId,
                    'value'            => $sectorParameter->value,
                ]);
            }
            $response[] = $this->formatParameterDetails($parameter);
        }
        return $response;
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
    // public function updateEmployeeTypeParameter(array $values): bool
    // {
    //     $parameter = Parameter::where('name', $values['name'])->get()->first();
    //     $employeeTypeParameter = EmployeeTypeParameter::firstOrCreate([
    //         'parameter_id'     => $parameter->id,
    //         'employee_type_id' => $values['employee_type_id'],
    //     ]);
    //     dd($employeeTypeParameter);
    //     if ($values['use_default']) {
    //         EmployeeTypeParameter::where('parameter_id', $parameter->id)
    //             ->where('employee_type_id', $values['employee_type_id'])->delete();
    //     } else {
    //         $employeeTypeParameter = EmployeeTypeParameter::firstOrCreate([
    //             'parameter_id'     => $parameter->id,
    //             'employee_type_id' => $values['employee_type_id'],
    //         ]);
    //         dd($employeeTypeParameter);
    //         $employeeTypeParameter->value = $values['value'];
    //         $employeeTypeParameter->save();
    //     }
    //     return true;
    // }
}
