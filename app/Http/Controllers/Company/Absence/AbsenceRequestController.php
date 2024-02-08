<?php

namespace App\Http\Controllers\Company\Absence;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Company\Absence\AbsenceRequestService;

class AbsenceRequestController extends Controller
{
    public function __construct(protected AbsenceRequestService $absenceRequestService)
    {
    }

    public function employeeLeaveRequest(Request $request)
    {
        try {
            $rules = [
                'plan_id' => [
                    'required',
                    'numeric',
                    Rule::unique('absence_requests')->where(function ($query) {
                        $query->where('status', true)->whereNull('deleted_at');
                    }),
                ],                
                'reason'  => 'nullable',
                'file'    => 'nullable',
            ];

            $messages = [
                'plan_id.unique' => 'Already requested leave for plan.',
            ];
    
            $validator = Validator::make(request()->all(), $rules, $messages);
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
                    'data'    => $this->absenceRequestService->employeeLeaveRequest($request->all()),
                    'message' => 'Leave requested successfully',
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
