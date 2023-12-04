<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\MealVoucherService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\MealVoucherRequest;
use App\Models\MealVoucher;


class MealVoucherController extends Controller
{

    public function __construct(protected MealVoucherService $mealVoucherService)
    {
    }

    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->mealVoucherService->index(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->mealVoucherService->show($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function edit($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->mealVoucherService->edit($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function store(MealVoucherRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'MealVoucher created successfully',
                'data'    => $this->mealVoucherService->store($request->validated())
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function update(MealVoucherRequest $request, MealVoucher $mealVoucher)
    {
        if ($this->mealVoucherService->update($mealVoucher, $request->validated())) {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'MealVoucher updated successfully',
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

    public function destroy(MealVoucher $mealVoucher)
    {
        if ($this->mealVoucherService->delete($mealVoucher)) {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'MealVoucher deleted',
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