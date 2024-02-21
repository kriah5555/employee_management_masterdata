<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\Employee\EmployeeService;

class EmployeeSignatureController extends Controller
{
    public function __construct(protected EmployeeService $employee_service)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user_id = Auth::guard('web')->user()->id;
            $rules = [
                'signature_data' => [
                    'required',
                    'string',
                ],
            ];
            $validator = Validator::make(request()->all(), $rules, []);

            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }

            $employee_profile = getEmployeeProfileIdByUserIdCompanyId($user_id, getCompanyId());
            if ($employee_profile) {
                $this->employee_service->createEmployeeSignature($employee_profile->id, $request->all());
                return returnResponse(
                    [
                        'success' => true,
                        'message' => 'Employee signature added successfully',
                    ],
                    JsonResponse::HTTP_OK,
                );
            } else {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => 'Employee profile not found',
                    ],
                    JsonResponse::HTTP_OK,
                );
            }
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

    /**
     * Display the specified resource.
     */
    public function show()
    {
        try {
            $user_id = Auth::guard('web')->user()->id;
            $employee_profile = getEmployeeProfileIdByUserIdCompanyId($user_id, getCompanyId());
            
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employee_service->getEmployeeSignature($employee_profile->id),
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
