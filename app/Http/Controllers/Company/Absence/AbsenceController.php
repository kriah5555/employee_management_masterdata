<?php

namespace App\Http\Controllers\Company\Absence;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Company\Absence\AbsenceService;

class AbsenceController extends Controller
{
    public function __construct(protected AbsenceService $absenceService)
    {
    }

    /**
     * Display a listing of the resource.
     */

    public function getAbsenceDetailsForWeek(Request $request)
    {
        try {
            $rules = [
                'week' => 'required|integer|min:1|max:53',
                'year' => 'required|date_format:Y',
                'employee_profile_id' => 'nullable',
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
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->absenceService->getAbsenceDetailsForWeek($request->week, $request->year, $request->employee_profile_id),
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
