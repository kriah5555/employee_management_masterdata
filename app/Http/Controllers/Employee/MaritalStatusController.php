<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\Employee\MaritalStatusService;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\Employee\MaritalStatusRequest;
use App\Models\Employee\MaritalStatus;
use Illuminate\Http\Request;


class MaritalStatusController extends Controller
{
    protected $maritalStatusService;

    public function __construct(MaritalStatusService $maritalStatusService)
    {
        $this->maritalStatusService = $maritalStatusService;
    }

    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->maritalStatusService->index(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->maritalStatusService->show($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function edit($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->maritalStatusService->edit($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function store(MaritalStatusRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Marital status created successfully',
                'data'    => $this->maritalStatusService->store($request->validated())
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function update(MaritalStatusRequest $request, MaritalStatus $maritalStatus)
    {
        if ($this->maritalStatusService->update($maritalStatus, $request->validated())) {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Marital status updated successfully',
                ],
                JsonResponse::HTTP_OK,
            );
        } else {
            return returnResponse(
                [
                    'success' => false,
                    'message' => 'Failed to update',
                ],
                JsonResponse::HTTP_OK,
            );
        }
    }

    public function destroy(MaritalStatus $maritalStatus)
    {
        if ($this->maritalStatusService->delete($maritalStatus)) {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Marital status deleted',
                ],
                JsonResponse::HTTP_OK,
            );
        } else {
            return returnResponse(
                [
                    'success' => false,
                    'message' => 'Failed to delete',
                ],
                JsonResponse::HTTP_OK,
            );
        }
    }
}