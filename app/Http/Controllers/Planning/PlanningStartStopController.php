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
            $input['user_id'] = Auth::id();
            $input['started_by'] = $input['user_id'];
            $this->planningStartStopService->startPlanByEmployee($input);
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
            $input['user_id'] = Auth::id();
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
