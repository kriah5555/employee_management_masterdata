<?php

namespace App\Http\Controllers\Planning;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Planning\PlanningMobileService;


class PlanningMobileController extends Controller
{
    public function __construct(
        protected PlanningMobileService $planningMobileService,
        )
    {
    }

    public function getWeeklyPlanning(Request $request)
    {
        try {
            $rules = [
                'user_id'         => [
                    'bail',
                    'required',
                    'integer',
                    Rule::exists('userdb.users', 'id'),
                ],
                'week' => 'required|integer',
                'year' => 'required|digits:4',
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

            $company_ids = getUserCompanies($request['user_id']);
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningMobileService->getWeeklyPlanningService('', '', '', $request->input('week'), $request->input('year'), $company_ids, $request->input('user_id'))
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
