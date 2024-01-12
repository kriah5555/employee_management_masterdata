<?php

namespace App\Interfaces;

use App\Models\Parameter\Parameter;

interface ParameterRepositoryInterface
{
    public function getAllParameters();

    public function getParameterById(string $parameterId);

    public function deleteParameter(string $parameterId);

    public function createParameter(array $parameterDetails);

    public function updateDefaultParameter(Parameter $parameter, array $newDetails);
}
