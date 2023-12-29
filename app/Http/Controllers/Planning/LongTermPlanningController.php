<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Models\Planning\LongTermPlanning;
use App\Services\Employee\EmployeeContractService;
use App\Services\Planning\LongTermPlanningService;
use App\Services\Planning\PlanningService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Rules\BelgiumCurrencyFormatRule;
use Exception;
use App\Http\Requests\LongTermPlannings\LongTermPlanningRequest;


class LongTermPlanningController extends Controller
{
    public function __construct(
        protected PlanningService $planningService,
        protected LongTermPlanningService $longTermPlanningService,
        protected EmployeeContractService $employeeContractService
    ) {
    }

    public function store(LongTermPlanningRequest $request)
    {
        try {
            $this->longTermPlanningService->storeLongTermPlanning($request->all());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Long term plannings saved',
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

    public function getEmployeeLongTermPlannings($employeeProfileId)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->longTermPlanningService->getEmployeeLongTermPlannings($employeeProfileId),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function destroy($longTermPlanningId)
    {
        try {
            $this->longTermPlanningService->deleteLongTermPlanning($longTermPlanningId);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Long term plannings deleted',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    public function show($longTermPlanningId)
    {
        try {

            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->longTermPlanningService->getLongTermPlanningDetails($longTermPlanningId)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    public function update(LongTermPlanningRequest $request, $longTermPlanningId)
    {
        try {
            $this->longTermPlanningService->updateLongTermPlanning($longTermPlanningId, $request->all());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Long term plannings updated',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function create()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Planning options',
                    'data'    => $this->planningService->getPlanningOverviewFilterService(getCompanyId()),
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
