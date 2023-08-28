<?php

namespace App\Http\Controllers;

use App\Services\WorkstationService;
use App\Http\Rules\WorkstationRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Workstation;

class WorkstationController extends Controller
{
    protected $workstation_service;

    public function __construct(WorkstationService $workstation_service)
    {
        $this->workstation_service = $workstation_service;
    }

    // public function index()
    // {
    //     try {
    //         $data = $this->workstation_service->getAll();
    //         return response()->json([
    //             'success' => true,
    //             'data' => $data,
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => [$e->getMessage()],
    //         ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }

    public function companyWorkstations($company_id, $status)
    {
        try {
            $data = $this->workstation_service->getAll(['company_id' => $company_id, 'status' => $status]);
            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function locationWorkstations($location_id, $status)
    {
        try {
            $data = $this->workstation_service->getAll(['location_id' => $location_id, 'status' => $status]);
            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function store(WorkstationRequest $request)
    {
        try {
            $location = $this->workstation_service->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Workstation created successfully',
                'data'    => $location,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function show(Workstation $workstation)
    {
        return response()->json([
            'success' => true,
            'data'    => $workstation,
        ]);
    }

    public function update(WorkstationRequest $request, Workstation $workstation)
    {
        try {
            $this->workstation_service->updateWorkstation($workstation, $request->validated());
            $workstation->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Workstation updated successfully',
                'data'    => $workstation,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function destroy(Workstation $workstation)
    {
        $workstation->delete();
        return response()->json([
            'success' => true,
            'message' => 'Workstation deleted successfully'
        ]);
    }
}