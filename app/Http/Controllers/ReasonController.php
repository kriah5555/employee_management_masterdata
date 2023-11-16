<?php

namespace App\Http\Controllers;

use App\Models\Reason;
use App\Services\ReasonService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ReasonRequest;

class ReasonController extends Controller
{
    protected $reasonService;

    public function __construct(ReasonService $reasonService)
    {
        $this->reasonService = $reasonService;
    }

    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->reasonService->getReasons(),
                ],
                JsonResponse::HTTP_OK,
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

    public function store(ReasonRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Reason created successfully'),
                    'data'    => $this->reasonService->createReason($request->validated()),
                ],
                JsonResponse::HTTP_CREATED,
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

    public function show($id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->reasonService->getReasonDetails($id),
                ],
                JsonResponse::HTTP_CREATED,
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

    public function create()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'categories' => $this->reasonService->getReasonCategoriesOptions(),
                    ],
                ],
                JsonResponse::HTTP_OK,
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

    public function update(ReasonRequest $request, Reason $reason)
    {
        try {
            $this->reasonService->updateReason($reason, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Reason updated successfully')
                ],
                JsonResponse::HTTP_OK,
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

    public function destroy(Reason $reason)
    {
        try {
            $this->reasonService->deleteReason($reason);
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Reason deleted successfully')
                ],
                JsonResponse::HTTP_OK,
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

    public function getReasonsList(string $category)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->reasonService->getReasonsByCategory($category)
                ],
                JsonResponse::HTTP_OK,
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