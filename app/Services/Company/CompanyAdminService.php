<?php

namespace App\Services\Company;

use App\Models\Company\Company;
use App\Services\Company\LocationService;
use App\Interfaces\Services\Company\CompanyLocationServiceInterface;
use App\Services\User\UserService;
use App\Services\Employee\EmployeeService;

class CompanyAdminService implements CompanyLocationServiceInterface
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function createNewResponsiblePerson($values, $company_id)
    {
        try {
            DB::connection('master')->beginTransaction();
            DB::connection('userdb')->beginTransaction();
            $existingEmpProfile = $this->userService->getUserBySocialSecurityNumber($values['social_security_number']);
            if ($existingEmpProfile->isEmpty()) {
                $user = $this->userService->createNewUser($values);
            } else {
                $user = $existingEmpProfile->last();
            }
            $user->assignRole($values['role']);
            $companyUser = $user->companyUser->create([
                'company_id' => $company_id
            ]);
            $companyUser->assignRole($values['role']);
            $employeeProfile = $this->createEmployeeProfile($user, $values);
            $this->createEmployeeSocialSecretaryDetails($employeeProfile, $values);
            DB::connection('master')->commit();
            DB::connection('userdb')->commit();
            return $employeeProfile;
        } catch (Exception $e) {
            DB::connection('master')->rollback();
            DB::connection('userdb')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
