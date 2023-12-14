<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanningRequest;
use App\Http\Requests\UpdatePlanningRequest;
use App\Models\Planning\PlanningBase;
use App\Services\Planning\PlanningService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Planning\GetWeeklyPlanningRequest;


class PlanningController extends Controller
{
    public function __construct(protected PlanningService $planningService)
    {
    }

    /**
     * Planning overview options function
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getPlanningOverviewOptions(Request $request)
    {
        try {
            $companyId = $request->header('company-id');
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Planning options',
                    'data'    => $this->planningService->getPlanningOverviewFilterService($companyId),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get monthly planning data
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getMonthlyPlanning(Request $request)
    {
        try {
            $input = $request->only(['location', 'workstations', 'employee_types', 'year']);
            $data = $this->planningService->getMonthlyPlanningService($input['year'], $input['location'], $input['workstations'], $input['employee_types']);

            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Monthly planning',
                    'data'    => $data
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get weekly planning info.
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getWeeklyPlanning(GetWeeklyPlanningRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningService->getWeeklyPlanningService($request->input('location'), $request->input('workstations'), $request->input('employee_types'), $request->input('week'), $request->input('year'))
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get day planning
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getDayPlanning(Request $request)
    {
        $input = $data = [];
        try {
            $input = $request->only(['locations', 'workstations', 'employee_types', 'date']);
            $data = $this->planningService->getDayPlanningService($input['locations'], $input['workstations'], $input['employee_types'], $input['date']);

            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Day planning response',
                    'data'    => $data
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get employee planning options by workstation and employeeId.
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function planningCreateOptions(Request $request)
    {
        $input = $data = [];
        try {
            $input = $request->only(['workstation', 'employee_id']);
            $data = $this->planningService->planningCreateOptionsService($input['workstation'], $input['employee_id']);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee options',
                    'data'    => $data
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
