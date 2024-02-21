<?php

namespace App\Http\Controllers\Planning;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Planning\PlanningService;
use App\Services\Contract\ContractService;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Planning\PlanningStartStopService;
use App\Http\Requests\Planning\StopPlanByManagerRequest;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Http\Requests\Planning\StartPlanByManagerRequest;
use App\Http\Requests\Planning\StopPlanByEmployeeRequest;
use App\Http\Requests\Planning\StartPlanByEmployeeRequest;

class PlanningStartStopController extends Controller
{
    public function __construct(
        protected PlanningService $planningService,
        protected PlanningStartStopService $planningStartStopService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function startPlanByManager(StartPlanByManagerRequest $request)
    {
        try {
            $input = $request->validated();
            $input['started_by'] = Auth::guard('web')->user()->id;
            $this->planningStartStopService->startPlanByManager($input);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Plan started'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function startPlanByEmployee(StartPlanByEmployeeRequest $request)
    {
        
        try {
            $input               = $request->validated();
            $input['started_by'] = $input['user_id'] = Auth::id();
            $time                = date('H:i');
            $input['start_time'] = $time;

            $plan                        = $this->planningStartStopService->getPlanByQrCode($input['QR_code'], $input['user_id'], $time, $time)->first();
            $plan_daily_renewal_contract = $plan->employeeType->contractTypes->where('contract_renewal_type_id', config('constants.DAILY_CONTRACT_RENEWAL_ID'))->first();
            if (($plan->contract_status != config('contracts.SIGNED') || empty($plan->contracts)) && $plan_daily_renewal_contract) { # if contract not generated or if the contract is unsigned
                $qr_data           = decodeData($input['QR_code']);
                
                $contract          = $plan->contracts()->exists() ?  $plan->contracts->first() : app(ContractService::class)->generateEmployeePlanContract($plan->employee_profile_id, $plan_daily_renewal_contract->id, config('contracts.CONTRACT_STATUS_UNSIGNED'), $plan->id, $qr_data['company_id'] ); # if contract exists use that else generate new contract and use that

                return response()->json([   
                        'success'             => false,
                        'message'             => t('Please sign contract and scan qr code to start your plan.'),
                        'sign_contract'       => 1, # 0-> not signed contract,  1-> signed contract,
                        'forgot_to_stop_plan' => false, # false => has forgotten to stop the plan,
                        'contract_pdf'        => env('CONTRACTS_URL') . '/' . $contract->files->file_path,
                        'company_id'          => $qr_data['company_id'],
                        'plan_id'             => $plan->id,
                    ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        
            }
            $this->planningStartStopService->startPlanByEmployee($input);
            return returnResponse(
                [
                    'success' => true,
                    'sign_contract' => 0,
                    'message' => 'Plan started'
                ],
                JsonResponse::HTTP_OK,
            );

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function stopPlanByManager(StopPlanByManagerRequest $request)
    {
        try {
            $input = $request->validated();
            $input['ended_by'] = Auth::guard('web')->user()->id;
            $this->planningStartStopService->stopPlanByManager($input);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Plan stopped'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function stopPlanByEmployee(StopPlanByEmployeeRequest $request)
    {
        try {
            $input              = $request->validated();
            $input['ended_by']  = $input['user_id'] = Auth::guard('web')->user()->id;
            $time               = date('H:i');
            $input['stop_time'] = $time;
            
            $qr_data = decodeData($input['QR_code']);
        
            setTenantDBByCompanyId($qr_data['company_id']);
            
            $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId($input['user_id']);
            
            $plans            = app(PlanningRepository::class)->getStartedPlanForEmployee($employee_profile->id, $qr_data['location_id']);

            if ($plans->count()) {
                $endTime         = Carbon::parse($plans->first()->end_date_time);
                $now             = Carbon::now();
                $hoursDifference = $now->diffInHours($endTime);

                if (strtotime(date('Y-m-d H:i')) > strtotime($plans->first()->end_date_time) && $hoursDifference > config('constants.PLANNING_STOP_MAX_TIME')) {
                    return response()->json([   
                        'success'             => false,
                        'message'             => t('Cannot stop plan, please enter stop time.'),
                        'forgot_to_stop_plan' => true,
                    ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            $this->planningStartStopService->stopPlanByEmployee($input);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Plan stopped'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function stopForgotPlanByEmployee(Request $request)
    {
        try {
            $input              = $request->all();
            $input['ended_by']  = $input['user_id'] = Auth::guard('web')->user()->id;
            
            $qr_data = decodeData($input['QR_code']);
        
            setTenantDBByCompanyId($qr_data['company_id']);
            
            $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId($input['user_id']);
            
            $plans            = app(PlanningRepository::class)->getStartedPlanForEmployee($employee_profile->id, $qr_data['location_id']);

            $startTime = Carbon::parse($plans->first()->start_date_time);
            $now       = Carbon::now();

            $hoursDifference = $now->diffInHours($startTime);
            if (!($hoursDifference > config('constants.PLANNING_STOP_MAX_TIME'))) {
                return response()->json([   
                    'success'             => false,
                    'message'             => t('No forgotten plan to stop stop plan, please enter stop time.'),
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            $this->planningStartStopService->stopPlanByEmployee($input);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Plan stopped'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getDayPlanningToStartAndStop(Request $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningStartStopService->getDayPlanningToStartAndStop($request->location_id)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
