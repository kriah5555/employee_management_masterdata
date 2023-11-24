<?php

namespace App\Http\Controllers\Company;

use App\Services\WorkstationService;
use App\Http\Requests\Company\WorkstationRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Company\Workstation;
use App\Http\Controllers\Controller;

class WorkstationController extends Controller
{
    public function __construct(protected WorkstationService $workstation_service)
    {
    }

    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->workstation_service->getWorkstationsOfCompany(),
            ]);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function store(WorkstationRequest $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Workstation created successfully',
                'data'    => $this->workstation_service->create($request->validated(), getCompanyId()),
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function show($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->workstation_service->getWorkstationDetails($id),
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function create()
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->workstation_service->getOptionsToCreate(getCompanyId()),
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function update(WorkstationRequest $request, $id)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Workstation updated successfully',
                'data'    => $this->workstation_service->updateWorkstation($id, $request->validated(), getCompanyId()),
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
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