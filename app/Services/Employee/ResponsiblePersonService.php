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
            return $this->responsiblePersonRepository->getCompanyResponsiblePersons($company_id);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
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

    public function getResponsiblePersonById($user_id, $company_id)
    {
        try {
            $responsible_person = $this->responsiblePersonRepository->getResponsiblePersonById($user_id, $company_id);
            return $responsible_person;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
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
            DB::connection('userdb')->beginTransaction();

                $data = $this->responsiblePersonRepository->updateResponsiblePerson($responsible_person_id, $responsible_person_details, $company_id);

            DB::connection('tenant')->commit();
            DB::connection('userdb')->commit();
            return $data;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
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
            return ;
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
