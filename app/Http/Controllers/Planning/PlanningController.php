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
    
    // public function getWeeklyPlanning()
    // {

    // }

    public function getPlanningOverviewOptions(Request $request)
    {
        try {
            $companyId = $request->header('company-id');
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Monthly planning',
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

    public function getWeeklyPlanning(Request $request)
    {
        $input = $output = [];
        try {
            $input  = $request->only(['locations', 'workstations', 'employee_types', 'week', 'year']);
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

    public function getDayPlanning(Request $request)
    {
        $input = $request->only(['locations', 'workstations', 'employee_types', 'date']);
        return $this->planningService->getDayPlanningService($input['locations'], $input['workstations'], $input['employee_types'], $input['week'], $input['year']);
    }

    public function getEmployeeList()
    {

    }
}
