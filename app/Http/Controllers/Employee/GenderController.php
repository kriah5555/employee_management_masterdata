<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\Employee\GenderService;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\Employee\GenderRequest;
use App\Models\Employee\Gender;
use Illuminate\Http\Request;


class GenderController extends Controller
{
    protected $genderService;

    public function __construct(GenderService $genderService)
    {
        $this->genderService = $genderService;
    }

    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->genderService->index(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->genderService->show($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function edit($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->genderService->edit($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function store(GenderRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Gender created successfully',
                'data'    => $this->genderService->store($request->validated())
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function update(GenderRequest $request, Gender $gender)
    {
        if ($this->genderService->update($gender, $request->validated())) {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Gender updated successfully',
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

    public function destroy(Gender $gender)
    {
        if ($this->genderService->delete($gender)) {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Gender deleted',
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