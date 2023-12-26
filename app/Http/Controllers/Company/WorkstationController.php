<?php

namespace App\Http\Controllers\Company;

use App\Services\WorkstationService;
use App\Http\Requests\Company\WorkstationRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Company\Workstation;
use App\Http\Controllers\Controller;
use Exception;

class WorkstationController extends Controller
{
    public function __construct(protected WorkstationService $workstation_service)
    {
    }

    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->workstation_service->getWorkstationsOfCompany(),
                ],
                JsonResponse::HTTP_OK,
            );
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
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Workstation created successfully',
                    'data'    => $this->workstation_service->create($request->validated()),
                ],
                JsonResponse::HTTP_CREATED,
            );
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
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->workstation_service->getWorkstationDetails($id),
                ],
                JsonResponse::HTTP_OK,
            );
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
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->workstation_service->getOptionsToCreate(getCompanyId()),
                ],
                JsonResponse::HTTP_OK,
            );
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
            $this->workstation_service->updateWorkstation($id, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Workstation updated successfully',
                ],
                JsonResponse::HTTP_OK,
            );
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

    public function destroy($workstation_id)
    {
        try {
            $this->workstation_service->deleteWorkstation($workstation_id);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Workstation deleted successfully'
                ],
                JsonResponse::HTTP_OK,
            );
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
}
