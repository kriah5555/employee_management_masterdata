<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\Employee\CommuteTypeService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Employee\CommuteTypeRequest;
use App\Models\CommuteType;


class CommuteTypeController extends Controller
{
    protected $commuteTypeService;

    public function __construct(CommuteTypeService $commuteTypeService)
    {
        $this->commuteTypeService = $commuteTypeService;
    }

    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->commuteTypeService->index(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->commuteTypeService->show($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function edit($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->commuteTypeService->edit($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function store(CommuteTypeRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Commute type created successfully',
                'data'    => $this->commuteTypeService->store($request->validated())
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function update(CommuteTypeRequest $request, CommuteType $commuteType)
    {
        if ($this->commuteTypeService->update($commuteType, $request->validated())) {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Commute type updated successfully',
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

    public function destroy(CommuteType $commuteType)
    {
        if ($this->commuteTypeService->delete($commuteType)) {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Commute type deleted',
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
