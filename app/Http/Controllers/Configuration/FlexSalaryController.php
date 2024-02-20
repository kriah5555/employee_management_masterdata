<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Configuration\FlexSalaryRequest;
use Illuminate\Http\JsonResponse;
use App\Services\Configuration\FlexSalaryService;

class FlexSalaryController extends Controller
{
    public function __construct(protected FlexSalaryService $flexSalaryService)
    {
        $this->flexSalaryService = $flexSalaryService;
    }

    public function createOrUpdateFlexSalary(FlexSalaryRequest $request)
    {
        try {

            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Configuration creted successfully',
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

    public function getFlexSalaryBykey($key)
    {
        try {

            return returnResponse(
                [
                    'success' => true,
                    'data' => $this->flexSalaryService->getFlexSalaryBykey($key)
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
