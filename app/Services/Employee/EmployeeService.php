<?php

namespace App\Services\Employee;

use App\Models\Employee\EmployeeContract;
use App\Models\Employee\EmployeeProfile;
use App\Models\Employee\LongTermEmployeeContract;
use App\Models\EmployeeType\EmployeeType;
use App\Models\User\CompanyUser;
use App\Models\User\UserBasicDetails;
use App\Models\User\UserContactDetails;
use App\Repositories\Employee\EmployeeFunctionDetailsRepository;
use App\Repositories\Employee\EmployeeSocialSecretaryDetailsRepository;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Models\User\User;
use App\Repositories\Employee\EmployeeBenefitsRepository;
use App\Repositories\Company\LocationRepository;

use App\Models\EmployeeFunction\FunctionTitle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


class EmployeeService
{

    public function __construct(
        protected EmployeeProfileRepository $employeeProfileRepository,
        protected EmployeeBenefitsRepository $employeeBenefitsRepository,
        protected EmployeeSocialSecretaryDetailsRepository $employeeSocialDetailsRepository,
        protected EmployeeFunctionDetailsRepository $employeeFunctionDetailsRepository,
        protected LocationRepository $locationRepository,
        protected UserService $userService,
    ) {
    }
    /**
     * Function to get all the employee types
     */
    public function index()
    {
        $response = [];
        $employees = $this->employeeProfileRepository->getAllEmployeeProfiles();
        foreach ($employees as $employee) {
            $employee->user;
            $employee->user->userBasicDetails;
            $employee->user->UserContactDetails;
            $currentDate = Carbon::now();

            $employeeContracts = $employee->employeeContracts->filter(function ($employeeContract) use ($currentDate) {
                return Carbon::parse($employeeContract->start_date)->lessThanOrEqualTo($currentDate) &&
                    (is_null($employeeContract->start_date) || Carbon::parse($employeeContract->end_date)->greaterThanOrEqualTo($currentDate));
            });
            if ($employeeContracts->isNotEmpty()) {
                $currentContract = $employeeContracts->first();
                if (!array_key_exists($currentContract->employeeType->id, $response)) {
                    $response[$currentContract->employeeType->id] = [
                        'employee_type' => $currentContract->employeeType->name,
                        'employees'     => []
                    ];
                }
                $response[$currentContract->employeeType->id]['employees'][] = $employee;
            } else {
                if (!array_key_exists(999, $response)) {
                    $response[999] = [
                        'employee_type' => 'No contracts',
                        'employees'     => []
                    ];
                }
                $response[999]['employees'][] = $employee;
            }
        }
        return array_values($response);
    }

    public function getEmployeeDetails(string $employeeProfileId)
    {
        return $this->employeeProfileRepository->getEmployeeProfileById($employeeProfileId, [
            'user',
            'user.userBasicDetails',
            'user.userContactDetails',
            'user.userFamilyDetails',
            'user.userBankAccount',
            'employeeSocialSecretaryDetails'
        ]);
    }

    public function createNewEmployee($values, $company_id)
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
            $this->createCompanyUser($user, $company_id);
            $employeeProfile = $this->createEmployeeProfile($user, $values);
            $this->createEmployeeSocialSecretaryDetails($employeeProfile, $values);
            $this->createEmployeeContract($employeeProfile, $values);
            // $user->assignRole('employee');
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

    public function createCompanyUser(User $user, $company_id)
    {
        CompanyUser::create([
            'user_id'    => $user->id,
            'company_id' => $company_id
        ]);
    }

    public function createEmployeeProfile(User $user, $values)
    {
        $values['user_id'] = $user->id;
        return $this->employeeProfileRepository->createEmployeeProfile($values);
    }
    public function createEmployeeSocialSecretaryDetails(EmployeeProfile $employeeProfile, $values)
    {
        $values['employee_profile_id'] = $employeeProfile->id;
        return $this->employeeSocialDetailsRepository->createEmployeeSocialSecretaryDetails($values);
    }

    public function createEmployeeContract($employeeProfile, $values)
    {
        $contractDetails = $values['employee_contract_details'];
        $functionDetails = $values['employee_function_details'];
        $contractDetails['employee_profile_id'] = $employeeProfile->id;
        $contractDetails['weekly_contract_hours'] = str_replace(',', '.', $contractDetails['weekly_contract_hours']);
        $employeeType = EmployeeType::findOrFail($contractDetails['employee_type_id']);
        $employeeContract = EmployeeContract::create($contractDetails);
        if ($employeeType->employeeTypeCategory->id == 1) {
            $contractDetails['employee_contract_id'] = $employeeContract->id;
            LongTermEmployeeContract::create($contractDetails);
        }

        foreach ($functionDetails as $function) {
            $this->createEmployeeFunctionDetails($employeeContract, $function);
        }
    }
    public function createEmployeeFunctionDetails(EmployeeContract $employeeContract, $values)
    {
        $values['employee_contract_id'] = $employeeContract->id;
        return $this->employeeFunctionDetailsRepository->createEmployeeFunctionDetails($values);
    }

    public function createUser($values)
    {
        $username = $values['first_name'] . $values['last_name'];
        $username = strtolower(str_replace(' ', '', $username));
        $password = ucfirst($username) . '$';
        $saveValues = [
            'username'               => generateUniqueUsername($username),
            'password'               => Hash::make($password),
            'social_security_number' => $values['social_security_number'],
        ];
        return User::create($saveValues);
    }

    public function getSubTypeOptions()
    {
        return config('constants.SUB_TYPE_OPTIONS');
    }

    public function getScheduleTypeOptions()
    {
        return config('constants.SCHEDULE_TYPE_OPTIONS');
    }

    public function getEmploymentTypeOptions()
    {
        return config('constants.EMPLOYMENT_TYPE_OPTIONS');
    }

    public function getEmployeeSalaryTypeOptions()
    {
        return config('constants.EMPLOYEE_SALARY_TYPE_OPTIONS');
    }

    function getSalary($employee_type_id, $function_title_id = '', $experience_in_months = '')
    {
        try {
            $employeeType = $this->employeeTypeService->model::findOrFail($employee_type_id);
            $salary_type = $employeeType->salary_type;

            $minimumSalary = 0;
            $salaryTypeLabel = null;

            if (!empty($salary_type) && array_key_exists($salary_type, config('constants.SALARY_TYPES'))) {
                // Retrieve the FunctionTitle based on its ID
                $functionTitle = FunctionTitle::findOrFail($function_title_id);
                $functionCategory = $functionTitle->functionCategory;

                if ($functionCategory && $functionCategory->sector) {
                    $sectorSalarySteps = $functionCategory->sector->salaryConfig->salarySteps()
                        ->where('from', '<=', $experience_in_months)
                        ->where('to', '>=', $experience_in_months)
                        ->get();

                    if ($sectorSalarySteps->isNotEmpty()) {
                        $function_category_number = $functionCategory->category;

                        if ($salary_type === 'min') {
                            $function_category_number = $function_category_number;
                        } elseif ($salary_type === 'min1') {
                            $function_category_number -= 1;
                        } elseif ($salary_type === 'min2') {
                            $function_category_number -= 2;
                        } elseif ($salary_type === 'flex') {
                            $function_category_number = 999;
                        }

                        $function_category_number = max(1, $function_category_number);

                        $minimumSalaries = $sectorSalarySteps->first()->minimumSalary
                            ->where('category_number', $function_category_number);

                        if ($minimumSalaries->isNotEmpty()) {
                            $minimumSalary = $minimumSalaries->first()->salary;
                        }
                    }
                }
            }

            return [
                'minimumSalary' => $minimumSalary,
                'salary_type'   => [
                    'value' => $salary_type,
                    'label' => config('constants.SALARY_TYPES')[$salary_type],
                ],
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
