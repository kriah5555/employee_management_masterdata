<?php

namespace App\Services\Employee;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Company\Employee\EmployeeProfile;
use App\Repositories\Employee\ResponsiblePersonRepository;
use App\Services\Employee\EmployeeService;

class ResponsiblePersonService
{
    public function __construct(protected ResponsiblePersonRepository $responsiblePersonRepository, protected EmployeeService $employee_service)
    {

    }

    public function getCompanyResponsiblePersons($company_id)
    {
        try {
            return $this->formatCompanyResponsiblePersons($this->responsiblePersonRepository->getCompanyResponsiblePersons($company_id));
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function formatCompanyResponsiblePersons($employeeProfiles)
    {
        $responsiblePersons = [];
        foreach ($employeeProfiles as $employeeProfile) {
            if ($employeeProfile->roles->count() == 1) {
                $role = config('roles_permissions.RESPONSIBLE_PERSON_ROLES')[$employeeProfile->roles[0]->name];
            } elseif ($employeeProfile->roles[0]->name == 'employee') {
                $role = config('roles_permissions.RESPONSIBLE_PERSON_ROLES')[$employeeProfile->roles[1]->name];
            } else {
                $role = config('roles_permissions.RESPONSIBLE_PERSON_ROLES')[$employeeProfile->roles[0]->name];
            }
            $responsiblePersons[] = [
                'id'                     => $employeeProfile->user->employeeProfileForCompany->id,
                'full_name'              => $employeeProfile->user->userBasicDetails->first_name . ' ' . $employeeProfile->user->userBasicDetails->last_name,
                'social_security_number' => $employeeProfile->user->social_security_number,
                'role'                   => $role
            ];
        }
        return $responsiblePersons;
    }

    public function getCompanyResponsiblePersonOptions($company_id)
    {
        try {
            return $this->responsiblePersonRepository->getCompanyResponsiblePersonOptions($company_id);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getResponsiblePersonDetails($employeeProfileId, $company_id)
    {
        try {
            return $this->formatResponsiblePersonDetails($this->responsiblePersonRepository->getResponsiblePersonDetails($employeeProfileId, $company_id));
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function formatResponsiblePersonDetails($responsiblePerson)
    {
        $roles = $responsiblePerson->user->companyUserByCompanyId(getCompanyId())->roles;
        if ($roles->count() == 1) {
            $role = $roles[0]->name;
        } elseif ($roles[0]->name == 'employee') {
            $role = $roles[1]->name;
        } else {
            $role = $roles[0]->name;
        }
        return [
            'first_name'             => $responsiblePerson->user->userBasicDetails->first_name,
            'last_name'              => $responsiblePerson->user->userBasicDetails->last_name,
            'social_security_number' => $responsiblePerson->user->social_security_number,
            'email'                  => $responsiblePerson->user->userContactDetails->email,
            'phone_number'           => $responsiblePerson->user->userContactDetails->phone_number,
            'date_of_birth'          => $responsiblePerson->user->userBasicDetails->date_of_birth
                ? date('d-m-Y', strtotime($responsiblePerson->user->userBasicDetails->date_of_birth))
                : null,
            'role'                   => [
                'value' => $role,
                'label' => config('roles_permissions.RESPONSIBLE_PERSON_ROLES')[$role]
            ]
        ];
    }

    public function createResponsiblePerson($details, $company_id)
    {
        try {
            return $this->responsiblePersonRepository->createResponsiblePerson($details, $company_id);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateResponsiblePerson($responsible_person_id, $responsible_person_details, $company_id)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            DB::connection('master')->beginTransaction();
            DB::connection('userdb')->beginTransaction();

                $data = $this->responsiblePersonRepository->updateResponsiblePerson($responsible_person_id, $responsible_person_details, $company_id);

            DB::connection('tenant')->commit();
            DB::connection('master')->commit();
            DB::connection('userdb')->commit();
            return $data;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            DB::connection('master')->rollback();
            DB::connection('userdb')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteResponsiblePerson($responsible_person_id, $company_id)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            DB::connection('userdb')->beginTransaction();

            $this->responsiblePersonRepository->deleteResponsiblePerson($responsible_person_id, $company_id);

            DB::connection('tenant')->commit();
            DB::connection('userdb')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->beginTransaction();
            DB::connection('userdb')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToCreateResponsiblePersons()
    {
        try {
            return [
                'roles' => getValueLabelOptionsFromConfig('roles_permissions.RESPONSIBLE_PERSON_ROLES')
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
