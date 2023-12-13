<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanningRequest;
use App\Http\Requests\UpdatePlanningRequest;
use App\Models\Planning\PlanningBase;
use App\Services\Planning\PlanningService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class PlanningController extends Controller
{
    public function __construct(protected PlanningService $planningService)
    {}

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
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
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
            $input = $request->only(['locations', 'workstations', 'employee_types', 'year']);
            $data = $this->planningService->getMonthlyPlanningService($input['year'], $input['locations'], $input['workstations'], $input['employee_types']);

            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Monthly planning',
                    'data'    => $data
                ],
                JsonResponse::HTTP_OK,
            );
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get weekly planning info.
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getWeeklyPlanning(Request $request)
    {
        $input = $output = [];
        try {
            $input = $request->only(['locations', 'workstations', 'employee_types', 'week', 'year']);
            $output = $this->planningService->getWeeklyPlanningService($input['locations'], $input['workstations'], $input['employee_types'], $input['week'], $input['year']);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'weekly planning response',
                    'data'    => $output
                ],
                JsonResponse::HTTP_OK,
            );
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
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
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }  
    }

    /**
     * Get employee in create planning.
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getEmployeeList(Request $request)
    {
        $input = $data = [];
        try {
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
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
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
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
