<?php

namespace App\Http\Controllers;

use App\Services\WorkstationService;

class WorkstationController extends Controller
{
    protected $workstation_service;

    public function __construct(WorkstationService $workstation_service)
    {
        $this->workstation_service = $workstation_service;
    }

    public function index()
    {
        try {
            // $data = $this->workstation_service->getAllWorkstations();
            return response()->json([
                'success' => true,
                'data' => '$data',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
