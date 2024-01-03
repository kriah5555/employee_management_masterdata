<?php

namespace App\Services\Employee;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeType\EmployeeType;
use App\Services\Employee\EmployeeService;
use App\Models\Company\Employee\EmployeeContract;
use App\Models\Company\Employee\EmployeeSalaryDetails;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Repositories\Employee\EmployeeContractRepository;

class EmployeeContractService
{

    public function __construct(
        protected EmployeeService $employeeService,
        protected EmployeeSalaryDetails $employeeSalaryDetails,
        protected EmployeeProfileRepository $employeeProfileRepository,
        protected EmployeeContractRepository $employeeContractRepository,
    ) {
    }

    public function getEmployeeContracts($employee_id)
    {
        $employeeProfile = $this->employeeProfileRepository->getEmployeeProfileById($employee_id, ['employeeContracts']);
        $employeeContracts = [
            'active_contracts'  => [],
            'expired_contracts' => []
        ];

        $employee_contracts = $employeeProfile->employeeContracts;
        foreach ($employee_contracts as $employeeContract) {
            $employeeContract->employeeType;
            $employeeContract->longTermEmployeeContract;
            $contractDetails = $this->formatEmployeeContract($employeeContract);

            if ($employeeContract->end_date == null || strtotime($employeeContract->end_date) > strtotime(date('Y-m-d'))) {
                $employeeContracts['active_contracts'][] = $contractDetails;
            } else {
                $employeeContracts['expired_contracts'][] = $contractDetails;
            }
        }
        return $employeeContracts;
    }

    public function formatEmployeeContract($employeeContract)
    {
        $contractDetails = [
            'id'                        => $employeeContract->id,
            'start_date'                => $employeeContract->start_date,
            'end_date'                  => $employeeContract->end_date,
            'employee_type_id'          => $employeeContract->employeeType->id,
            'employee_type'             => $employeeContract->employeeType->name,
            'long_term'                 => false,
            'employee_function_details' => [],
        ];
        $employee_sub_type = '';
        if ($employeeContract->longTermEmployeeContract()->exists()) {
            $longTermEmployeeContract = $employeeContract->longTermEmployeeContract;
            $employee_sub_type = $longTermEmployeeContract->sub_type ?? null;
            $contractDetails['long_term'] = true;
            $contractDetails['sub_type'] = $employee_sub_type;
            $contractDetails['schedule_type'] = $longTermEmployeeContract->schedule_type ?? null;
            $contractDetails['employment_type'] = $longTermEmployeeContract->employment_type ?? null;
            $contractDetails['weekly_contract_hours'] = $longTermEmployeeContract->weekly_contract_hours;
            $contractDetails['formatted_weekly_contract_hours'] = $longTermEmployeeContract->weekly_contract_hours;
            $contractDetails['work_days_per_week'] = $longTermEmployeeContract->work_days_per_week;
        }
        foreach ($employeeContract->employeeFunctionDetails as $function) {
            $experience_in_months = ($function->salary) ? $function->experience : 0;
            $contractDetails['employee_function_details'][] = [
                'function_details_id' => $function->id,
                'function_title'      => $function->functionTitle->name,
                'function_id'         => $function->functionTitle->id, # function title id
                'salary'              => ($function->salary) ? $function->salary->salary : null,
                'salary_european'     => ($function->salary) ? $function->salary->salary_european : null,
                'experience'          => $experience_in_months,
                'minimum_salary'      => $this->employeeService->getSalary($employeeContract->employee_type_id, $function->functionTitle->id, $experience_in_months, $employee_sub_type),
            ];
        }
        return $contractDetails;
    }

    public function createEmployeeContract($values, $employee_profile_id = '')
    {

        try {
            DB::connection('tenant')->beginTransaction();
            $contractDetails = $values['employee_contract_details'];
            $employee_profile_id = !empty($employee_profile_id) ? $employee_profile_id : $values['employee_profile_id'];
            $contractDetails['employee_profile_id'] = $employee_profile_id;
            $employeeType = EmployeeType::findOrFail($contractDetails['employee_type_id']);
            $employeeContract = $this->employeeContractRepository->createEmployeeContract($contractDetails);
            if ($employeeType->employeeTypeCategory->id == config('constants.LONG_TERM_CONTRACT_ID')) {
                $employeeContract->longTermEmployeeContract()->create($contractDetails);
            }

            $employeeFunctionDetailsData = $values['employee_function_details'];

            foreach ($employeeFunctionDetailsData as $function_details) {
                $function_details['employee_profile_id'] = $employee_profile_id;
                $function_details['salary_id'] = $this->employeeSalaryDetails::create($function_details)->id;
                $employeeContract->employeeFunctionDetails()->create($function_details);
            }

            DB::connection('tenant')->commit();
            return $employeeContract;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateEmployeeContract($values, $employee_contract_id)
    {

        try {
            DB::connection('tenant')->beginTransaction();
            $contractDetails = $values['employee_contract_details'];
            $employeeType = EmployeeType::findOrFail($contractDetails['employee_type_id']);
            $employeeContract = $this->employeeContractRepository->getEmployeeContractById($employee_contract_id);
            $employee_profile_id = $employeeContract->employee_profile_id;

            $this->employeeContractRepository->updateEmployeeContract($employee_contract_id, [
                'start_date'       => $contractDetails['start_date'],
                'end_date'         => $contractDetails['end_date'],
                'employee_type_id' => $contractDetails['employee_type_id'],
            ]); # update contract details

            if ($employeeType->employeeTypeCategory->id == config('constants.LONG_TERM_CONTRACT_ID')) { # update long data if contract cat id long term
                $employeeContract->load('longTermEmployeeContract'); // Load the relationship

                $employeeContract->longTermEmployeeContract()->updateOrCreate(
                    ['id' => optional($employeeContract->longTermEmployeeContract)->id],
                    $contractDetails
                );
            } else {
                $employeeContract->longTermEmployeeContract()->delete();
            }

            $employeeFunctionDetailsData = $values['employee_function_details'];

            $employee_function_detail_ids = [];
            foreach ($employeeFunctionDetailsData as $function_details) {
                $function_details['employee_profile_id'] = $employee_profile_id;

                $existingRecord = $employeeContract->employeeFunctionDetails()
                    ->where('function_id', $function_details['function_id'])
                    ->first();

                if ($existingRecord) {
                    $employee_function_detail_ids[] = $existingRecord->id;
                    $existingRecord->salary()->update(['salary' => $function_details['salary']]);
                } else {
                    $function_details['salary_id'] = $this->employeeSalaryDetails::create($function_details)->id;
                    $employee_function_detail_ids[] = $employeeContract->employeeFunctionDetails()->create($function_details)->id;
                }
            }

            $employee_contracts_to_delete = $employeeContract->employeeFunctionDetails()->whereNotIn('id', $employee_function_detail_ids);

            $employee_contracts_to_delete->each(function ($functionDetail) {
                $functionDetail->salary()->delete();
            });

            $employee_contracts_to_delete->delete(); # Delete records that are not in $employee_function_detail_ids

            DB::connection('tenant')->commit();
            return $employeeContract;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteEmployeeContract($employee_contract_id)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $this->employeeContractRepository->deleteEmployeeContract($employee_contract_id);
            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getActiveContractEmployeesByWeek($weekNumber, $year)
    {
        $weekDates = getWeekDates($weekNumber, $year);
        $startDateOfWeek = reset($weekDates);
        $endDateOfWeek = end($weekDates);

        $contracts = EmployeeContract::with('employeeProfile.user.userBasicDetails')->where(function ($query) use ($startDateOfWeek, $endDateOfWeek) {
            $query->where(function ($query) use ($startDateOfWeek) {
                $query->where('start_date', '<', $startDateOfWeek)
                    ->where(function ($query) use ($startDateOfWeek) {
                        $query->where('end_date', '>', $startDateOfWeek)
                            ->orWhereNull('end_date');
                    });
            })->orWhere(function ($query) use ($endDateOfWeek) {
                $query->where('start_date', '<', $endDateOfWeek)
                    ->where(function ($query) use ($endDateOfWeek) {
                        $query->where('end_date', '>', $endDateOfWeek)
                            ->orWhereNull('end_date');
                    });
            });
        })->get();

        $activeEmployees = [];
        foreach ($contracts as $contract) {
            $activeEmployees[$contract->employeeProfile->id] = [
                'value' => $contract->employeeProfile->id,
                'label' => $contract->employeeProfile->user->userBasicDetails->first_name . ' ' . $contract->employeeProfile->user->userBasicDetails->last_name
            ];
        }
        $activeEmployees = array_values($activeEmployees);
        usort($activeEmployees, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });
        return $activeEmployees;
    }

    public function checkContractExistForLongTermPlanning($employeeProfileId, $startDate, $endDate)
    {
        return EmployeeContract::with('employeeProfile.user.userBasicDetails')
            ->where('employee_profile_id', $employeeProfileId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $startDate)
                        ->where(function ($query) use ($endDate) {
                            $query->where('end_date', '>=', $endDate)
                                ->orWhereNull('end_date');
                        });
                });
            })->first();
    }

    public function getEmployeeWithActiveType($date, $employeeTypeId, $functionId)
    {
        return EmployeeContract::with('employeeProfile.user.userBasicDetails')
            ->where('employee_type_id', $employeeTypeId)
            ->where(function ($query) use ($date) {
                $query->where(function ($query) use ($date) {
                    $query->where('start_date', '<=', $date)
                        ->where(function ($query) use ($date) {
                            $query->where('end_date', '>=', $date)
                                ->orWhereNull('end_date');
                        });
                });
            })->get();
    }
}
