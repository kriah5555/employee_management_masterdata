<?php

namespace App\Http\Controllers\Employee;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Employee\EmployeeMobileService;

class EmployeeMobileController extends Controller
{

    public function __construct(
        protected EmployeeMobileService $employeeService,
    ) {
    }

    public function getEmployeeList()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeService->getEmployeeMobileOptions()
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
