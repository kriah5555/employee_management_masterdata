<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Planning\ClonePlanningRequest;
use App\Services\Planning\ClonePlanningService;
use Illuminate\Http\JsonResponse;


class ClonePlanningController extends Controller
{
    public function __construct(protected ClonePlanningService $clonePlanningService)
    {
    }
    public function clonePlanning(ClonePlanningRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => $this->clonePlanningService->clonePlanning($request)
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $e) {
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
