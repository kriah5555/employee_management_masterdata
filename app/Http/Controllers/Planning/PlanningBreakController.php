<?php

namespace App\Http\Controllers\Planning;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Planning\PlanningBreakService;
use App\Http\Requests\Planning\PlanningBreakRequest;
use App\Http\Requests\Planning\EmployeeBreakRequest;

class PlanningBreakController extends Controller
{
    public function __construct(
        protected PlanningBreakService $planningBreakService
    ) {
    }

    public function startBreak(PlanningBreakRequest $request) # manager flow
    {
        try {
            $input = $request->validated();
            $input['started_by'] = Auth::guard('web')->user()->id;
            $this->planningBreakService->startBreak($input);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Break started'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_OK,
            );
        }
    }

    public function stopBreak(PlanningBreakRequest $request) # manager flow
    {
        try {
            $input = $request->validated();
            $input['ended_by'] = Auth::guard('web')->user()->id;
            $this->planningBreakService->stopBreak($input);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Break stopped'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_OK,
            );
        }
    }

    public function startBreakByEmployee(EmployeeBreakRequest $request)
    {
        try {
            $this->planningBreakService->startBreak($request->all());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Break started'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_OK,
            );
        }
    }

    public function stopBreakByEmployee(EmployeeBreakRequest $request)
    {
        try {
            $this->planningBreakService->stopBreak($request->all());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Break stopped'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_OK,
            );
        }
    }
}
