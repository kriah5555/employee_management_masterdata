<?php

namespace App\Http\Controllers\Employee;

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Employee\ImportEmployeeService;

class ImportEmployeeController extends Controller
{
    public function __construct(
        protected ImportEmployeeService $importEmployeeService,
    )
    {
    }
    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->importEmployeeService->getImportEmployeeFiles(),
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

    public function downloadImportEmployeeSampleFile()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->importEmployeeService->downloadSampleXlFile(),
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

    public function store(Request $request)
    {
        try {
            $rules = [
                'file' => 'required|file|mimes:xls,xlsx',
            ];

            $validator = Validator::make($request->all(), $rules, []);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }

            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee import will be completed shortly.',
                    'data'    => $this->importEmployeeService->createImportEmployeeFile($request->all()),
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
