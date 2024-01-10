<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Services\Planning\PlanningService;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Http\Requests\Planning\StartPlanByManagerRequest;
use App\Http\Requests\Planning\StartPlanByEmployeeRequest;
use App\Http\Requests\Planning\StopPlanByEmployeeRequest;
use App\Http\Requests\Planning\StopPlanByManagerRequest;
use App\Services\Planning\PlanningStartStopService;
use Illuminate\Support\Facades\Auth;
use App\Services\Contract\ContractService;

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
            $input = $request->validated();
            // $input['user_id'] = Auth::id();
            $input['user_id']    = $input['user_id'];
            $input['started_by'] = $input['user_id'];

            $plans = $this->planningStartStopService->getPlanByQrCode($input['QR_code'], $input['user_id'], $input['start_time'], $input['start_time'])->first();
            $plan = $plans->first();
            if (($plan->contract_status != config('contracts.SIGNED') || empty($plan->contracts)) && $plan->employeeType->employeeTypeCategory->id == config('constants.DAILY_CONTRACT_ID')) { # if contract not generated or if the contract is unsigned
                $qr_data = decodeData($input['QR_code']);

                $contract = $plan->contracts()->exists() ?  $plan->contracts->first() : $contract = app(ContractService::class)->generateEmployeeContract($plan->employee_profile_id, null, config('contracts.CONTRACT_STATUS_UNSIGNED'), $plan->id, $qr_data['company_id'] = 1); # if contract exists use that else generate new contract and use that

                return response()->json([
                        'success'           => false,
                        'message'           => t('Please sign contract and scan qr code to start your plan.'),
                        'sign_contract'     => 1, # 0-> not signed contract,  1-> signed contract,
                        'contract_pdf'      => env('CONTRACTS_URL') . '/' . $contract->files->file_path,
                        'company_id'        => $qr_data['company_id'],
                        'plan_id'           => $plan->id,
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
            $input = $request->validated();
            // $input['user_id'] = Auth::id();
            $input['user_id'] = $input['user_id'];
            $input['ended_by'] = $input['user_id'];
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
}
