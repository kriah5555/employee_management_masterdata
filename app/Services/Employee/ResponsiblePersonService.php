<?php

namespace App\Services\Employee;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Company\Employee\EmployeeProfile;
use App\Repositories\Employee\ResponsiblePersonRepository;
use App\Services\Employee\EmployeeService;
use App\Models\User\CompanyUser;

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
            $company_user = CompanyUser::where(['user_id' => $employeeProfile->user->id])->get()->first();
            if ($company_user->roles[0]->name == 'employee') {
              continue;
	    }
	    if ($company_user->roles->count() == 1) {
                $role = config('roles_permissions.RESPONSIBLE_PERSON_ROLES')[$company_user->roles[0]->name];
            } elseif ($company_user->roles[0]->name == 'employee') {
                $role = config('roles_permissions.RESPONSIBLE_PERSON_ROLES')[$company_user->roles[1]->name];
            } else {
                $role = config('roles_permissions.RESPONSIBLE_PERSON_ROLES')[$company_user->roles[0]->name];
            }
            $responsiblePersons[] = [
                'id'                     => $employeeProfile->id,
                'full_name'              => $employeeProfile->user->userBasicDetails->first_name . ' ' . $employeeProfile->user->userBasicDetails->last_name,
                'social_security_number' => $employeeProfile->user->social_security_number,
                'role'                   => $role,
                'user_id'                => $employeeProfile->user->id,
            ];
        }
        return $responsiblePersons;
    }

    public function getCompanyResponsiblePersonOptions($company_id)
    {
        try {
            return $this->getCompanyResponsiblePersons($company_id);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getResponsiblePersonListForChat($companyIds)
    {
        try {
            $responsiblePersons = [];

            foreach ($companyIds as $companyId) {
                connectCompanyDataBase($companyId);
                $companyResponsiblePersons = $this->responsiblePersonRepository->getCompanyResponsiblePersons($companyId);
                
                foreach ($companyResponsiblePersons as $companyResponsiblePerson) {
                    $responsiblePersons[$companyResponsiblePerson->user_id] = [
                        'user_id'       => $companyResponsiblePerson->user_id,
                        'user_name'     => $companyResponsiblePerson->user->username,
                        'employee_name' => $companyResponsiblePerson->full_name,
                    ];
                }
            }

            return array_values($responsiblePersons);
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
