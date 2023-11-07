<?php

namespace App\Interfaces\Employee;

interface LongTermEmployeeContractRepositoryInterface
{

    public function getLongTermEmployeeContractById(string $id);

    public function deleteLongTermEmployeeContract(string $id);

    public function createLongTermEmployeeContract(array $details);

    public function updateLongTermEmployeeContract(string $id, array $updatedDetails);
}
