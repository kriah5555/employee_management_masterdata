<?php

namespace App\Http\Controllers\Planning;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\Planning\EmployeeSwitchPlanningService;
use App\Http\Requests\Planning\EmployeeSwitchPlanningRequest;
use App\Http\Requests\Planning\UpdateSwitchPlanningStatusRequest;

class EmployeeSwitchPlanningController extends Controller
{
    public function __construct(
        protected EmployeeSwitchPlanningService $employeeSwitchPlanningService
    )
    {

    }
    public function getEmployeeListForSwitchingPlan(Request $request)
    {
        try {
            $rules = [
                'company_id' => [
                    'required',
                    'integer',
                    Rule::exists('master.companies', 'id')->whereNull('deleted_at')->where('status', true),
                ],
                'plan_id' => [
                    'bail',
                    'integer',
                    'required',
                    Rule::exists('tenant.planning_base', 'id')->whereNull('deleted_at')->where('status', true),
                ]
            ];
            setTenantDBByCompanyId($request->company_id);

            $validator = Validator::make($request->all(), $rules, []);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }

            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeSwitchPlanningService->getEmployeesToSwitchPlan($request->plan_id),
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

    public function requestToSwitchPlan(EmployeeSwitchPlanningRequest $request)
    {
        try {
            $this->employeeSwitchPlanningService->createSwitchPlanRequest($request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Request sent',
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

    public function getAllEmployeeRequestsForSwitchPlan()
    {
        try {
            $company_ids = getUserCompanies(Auth::guard('web')->user()->id);
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeSwitchPlanningService->getAllEmployeeRequestsForSwitchPlan(Auth::guard('web')->user()->id, $company_ids),
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

    public function updateSwitchPlanStatus(UpdateSwitchPlanningStatusRequest $request)
    {
        try {
            $company_ids = getUserCompanies(Auth::guard('web')->user()->id);
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeSwitchPlanningService->getAllEmployeeRequestsForSwitchPlan(Auth::guard('web')->user()->id, $company_ids),
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
