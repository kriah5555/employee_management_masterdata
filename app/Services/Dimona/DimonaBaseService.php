<?php

namespace App\Services\Dimona;

use App\Models\Dimona\DimonaDeclaration;
use App\Models\Dimona\DimonaErrorCode;
use App\Models\Planning\PlanningBase;
use App\Models\Company\Employee\EmployeeContract;
use App\Models\Company\Company;
use App\Models\Dimona\Dimona;
use App\Services\Dimona\RequestDimona;
use Carbon\Carbon;
use Illuminate\Support\Str;

//$uuid = Str::uuid();
class DimonaBaseService
{
    protected $company;
    protected $employeeContract;
    protected $planningBase;
    protected $requestDimona;
    protected $dimona;

    public function __construct()
    {
        $this->company = new Company();
        $this->planningBase = new PlanningBase();
        $this->employeeContract = new EmployeeContract();
        $this->requestDimona = new RequestDimona();
        $this->dimona = new Dimona();
    }


    //employee category: 1 -> Long term, 2-> Day contract, 3 -> External
    public function initiateDimonaByPlanService($companyId, $plannings, $type = '')
    {
        $response = [];
        foreach ($plannings as $plan) {
            $dimona = ['unique_id' => Str::uuid()];
            $this->setCompanyData($companyId, $dimona);
            $dimonaDetails = $this->setEmployeeAndPlanningData($plan, $type, $dimona);
            $this->createDimonaRecords($dimonaDetails, $dimona);
            $response[] = $this->requestDimona->sendDimonaRequest($dimona);
        }

        return $response;
    }

    public function getPlanningById($planId)
    {
        $relations = [
            'employeeType',
            'employeeType.dimonaConfig',
            'employeeProfile',
            'employeeProfile.user',
            'employeeProfile.user.userBasicDetails',
            'timeRegistrations',
            'planningDimona',
            'planningDimona.dimonaBase',
            'planningDimona.dimonaBase.dimonaDetails.dimonaResponse'
        ];
        return $this->planningBase->with($relations)->findOrFail($planId)->toArray();
    }

    public function setCompanyData($companyId, &$dimona)
    {
        $companyData = $this->company->where('id', $companyId)->get()->toArray();
        $companyData = reset($companyData);
        return $dimona += [
            'company_id'         => $companyData['id'],
            'company_name'       => $companyData['company_name'],
            'company_vat_number' => getVatNumberFormat($companyData['vat_number']),
            'company_rsz_number' => getRSZNumberFormat($companyData['rsz_number']),
            'company_username'   => $companyData['username'],
            'company_oauth'      => $companyData['oauth_key'],
        ];
    }

    public function getEmployeeLongTermDetails()
    {

    }

    public function setEmployeeAndPlanningData($plan, $type, &$dimona)
    {
        $hours = null;
        $plan = $this->getPlanningById($plan);
        $employeeDetails = $plan['employee_profile'] ?? [];
        $employeeType = $plan['employee_type'] ?? [];
        $timeRegistraion = $plan['time_registrations'] ?? [];
        $dimonaDetails = $plan['planning_dimona'] ?? [];

        // plan details.
        $dimona['plan_id'] = $plan['id'];

        //Employee details.
        if (count($employeeDetails) > 0) {
            $dimona['employee_name'] = $employeeDetails['full_name'];
            $dimona['user_id'] = $employeeDetails['user_id'];
            $dimona['social_security_number'] = getRSZNumberFormat($employeeDetails['user']['social_security_number'] ?? '');
        }

        //Employee type.
        if (count($employeeType) > 0) {
            $dimona['employee_type'] = $employeeType['name'];
            $dimona['employee_type_code'] = $employeeType['dimona_code'] ?? '';
            $dimona['employee_type_category'] = $employeeType['employee_type_category_id'];
            $dimona['dimona_catagory'] = $employeeType['dimona_config']['dimona_type_id'] ?? '';
            if ($dimona['employee_type_category'] == 1) {
                // $this->getEmployeeLongTermDetails();
            }
        }

        //Check for the Dimona type and timings.
        if (is_null($type)) {
            if (count($dimonaDetails) == 0) {
                $dimona_type = 'IN';
            } else {
                $dimona_type = 'UPDATE';
            }
        } else {
            $dimona_type = 'CANCEL';
        }

        if (count($timeRegistraion) > 0) {
            //Student dimona.
            if ($dimona['dimona_catagory'] == 1) {
                $dimona += $this->getWorkedHours($timeRegistraion, $plan);
            }

            //Flexi dimona.
            if ($dimona['dimona_catagory'] == 2 || $dimona['employee_type_category'] == 1) {
                $lastTimeregistraion = end($timeRegistraion);
                $dimona['start_date_time'] = $lastTimeregistraion['actual_start_time'];
                $dimona['end_date_time'] = $lastTimeregistraion['actual_end_time'] ?? addHours($plan['end_date_time'], 1);
                $dimona['hours'] = timeDifferenceinHours($dimona['start_date_time'], $dimona['end_date_time']);
            }

            //OTH Dimona.
            if ($dimona['dimona_catagory'] == 2) {
                $dimona['start_date_time'] = $lastTimeregistraion['start_date_time'];
                $dimona['end_date_time'] = $lastTimeregistraion['actual_end_time'] ?? addHours($plan['end_date_time'], 1);
                $dimona['hours'] = timeDifferenceinHours($dimona['start_date_time'], $dimona['end_date_time']);
            }

            if (count($timeRegistraion) == 0) {

            }
        } elseif (count($dimonaDetails) == 0 || count($dimonaDetails) > 0) {
            $hours = timeDifferenceinHours($plan['start_date_time'], $plan['end_date_time']);
            $dimona += [
                'start_date_time' => $plan['start_date_time'],
                'end_date_time'   => $plan['end_date_time'],
                'hours'           => $hours,
            ];
        }
        $dimona['type'] = $dimona_type;

        return $dimonaDetails;
    }

    public function getWorkedHours($timeRegistraion, $plan)
    {
        $start_date_time = $end_date_time = $hours = null;
        foreach ($timeRegistraion as $time_registration) {
            if ($time_registration['status'] == 1) {
                $start_date_time = $time_registration['actual_start_time'];
                $end_date_time = $time_registration['actual_end_time'] ?? addHours($plan['end_date_time'], 0);
                $hours += timeDifferenceinHours($start_date_time, $end_date_time);
            }
        }

        return ['start_date_time' => $start_date_time, 'end_date_time' => $end_date_time, 'hours' => $hours];
    }

    public function createDimonaRecords($dimonaDetails, $dimona)
    {
        if (count($dimonaDetails) == 0) {
            $dimonaBaseRecord = $this->dimona->create([
                'unique_id'    => $dimona['unique_id'],
                'dimona_code'  => $dimona['employee_type_code'],
                'employee_id'  => $dimona['user_id'],
                'employee_rsz' => $dimona['social_security_number'],
                'status'       => 1,
            ]);

            $dimonaBaseRecord->planningDimona()->create(['planning_base_id' => $dimona['plan_id']]);
            $dimonaBaseRecord->dimonaDetails()->create(
                [
                    'dimona_type'     => $dimona['type'],
                    'start_date_time' => $dimona['start_date_time'],
                    'end_date_time'   => $dimona['end_date_time'],
                ]
            );
        } else {
            $lastDimona = end($dimonaDetails);
            $dimonaBaseRecord = $this->dimona->find($lastDimona['id']);
            $dimonaBaseRecord->dimonaDetails()->create(
                [
                    'dimona_type'     => $dimona['type'],
                    'start_date_time' => $dimona['start_date_time'],
                    'end_date_time'   => $dimona['end_date_time'],
                ]
            );
        }
    }

    // public function getDimonaBase
    public function updateDimonaStatusService($data)
    {

    }


    public function getContractDetails($contractId)
    {
        $relations = [
            'employeeType',
            'employeeProfile',
            'employeeProfile.user.userBasicDetails',
        ];
        $this->employeeContract->with($relations)->findOrFail($contractId);
    }


    public function initiateDimonaByContract($type, $contract)
    {

    }

    public function updateDimonaResponse($uniqueId, $response)
    {
        $dimonaDeclaration = DimonaDeclaration::where('unique_id', $uniqueId)->first();
        if ($response['status']) {
            $body = $response['body'];
            if ($body['declarationStatus']['result'] == 'A') {
                $dimonaDeclaration->dimona_declartion_status = 'success';
                $dimonaDeclaration->dimona->dimona_period_id = $body['declarationStatus']['dimonaPeriodId'];
                $dimonaDeclaration->dimona->save();
                $dimonaDeclaration->save();
                if ($dimonaDeclaration->dimona->type == 'long_term') {
                    $longTermContract = $dimonaDeclaration->dimona->employeeContract->longTermEmployeeContract;
                    $longTermContract->dimona_period_id = $body['declarationStatus']['dimonaPeriodId'];
                    $longTermContract->save();
                }
            } elseif ($body['declarationStatus']['result'] == 'B') {
                $dimonaDeclaration->dimona_declartion_status = 'failed';
                $dimonaDeclaration->save();
                foreach ($body['declarationStatus']['anomaliesCollection'] as $dimonaError) {
                    $dimonaErrorCode = DimonaErrorCode::where('error_code', $dimonaError['errorId'])->first();
                    if (!$dimonaErrorCode) {
                        $dimonaErrorCode = DimonaErrorCode::create([
                            'error_code'  => $dimonaError['errorId'],
                            'description' => $dimonaError['labelNl'],
                        ]);
                    }
                    $dimonaDeclaration->dimonaDeclarationErrors()->create([
                        'dimona_error_code_id' => $dimonaErrorCode->id
                    ]);
                }
            }
        } else {
            $dimonaDeclaration->dimona_declartion_status = 'failed';
            $dimonaDeclaration->save();
            $dimonaErrorCode = DimonaErrorCode::where('error_code', '00000-001')->first();
            if (!$dimonaErrorCode) {
                $dimonaErrorCode = DimonaErrorCode::create([
                    'error_code'  => '00000-001',
                    'description' => 'No response received',
                ]);
            }
            $dimonaDeclaration->dimonaDeclarationErrors()->create([
                'dimona_error_code_id' => $dimonaErrorCode->id
            ]);
        }
    }
}
