<?php

namespace App\Http\Controllers\Planning;

use App\Services\Planning\PlanningService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Planning\PlanningMobileService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Planning\SwitchPlanningRequest;
use Illuminate\Validation\Rule;


class PlanningMobileController extends Controller
{
    public function __construct(
        protected PlanningMobileService $planningMobileService,
        protected PlanningService $planningService,
    ) {
    }

    public function getEmployeeWeeklyPlanning(Request $request)
    {
        try {
            $rules = [
                'week' => 'required|integer',
                'year' => 'required|digits:4',
            ];

            $validator = Validator::make(request()->all(), $rules, []);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => false,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }

            $userId = Auth::guard('web')->user()->id;
            $company_ids = getUserCompanies($userId);
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningMobileService->getWeeklyPlanningService('', '', '', $request->input('week'), $request->input('year'), $company_ids, $userId)
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

    public function getEmployeeDatesPlanning(Request $request)
    {
        try {
            $rules = [
                'dates'   => 'array|required',
                'dates.*' => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
            ];

            $validator = Validator::make(request()->all(), $rules, []);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => false,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }

            $userId = Auth::guard('web')->user()->id;
            $company_ids = getUserCompanies($userId);
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningMobileService->getDatesPlanningService($company_ids, $userId, $request->input('dates'))
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

    public function getEmployeePlanningStatus()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningMobileService->getUserPlanningStatus(Auth::id())
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
    public function getEmployeesToSwitchPlan(SwitchPlanningRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningMobileService->getEmployeesToSwitchPlan($request->validated())
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

    public function getEmployeeWorkedHours(Request $request)
    {
        try {
            $rules = [
                'from_date' => 'required|' . 'date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
                'to_date'   => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT') . '|after_or_equal:from_date',
            ];


            $validator = Validator::make(request()->all(), $rules, []);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => false,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }

            $userId = Auth::guard('web')->user()->id;
            // $userId = 4;
            $company_ids = getUserCompanies($userId);
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningMobileService->getEmployeeWorkedHours($company_ids, $userId, $request->input('from_date'), $request->input('to_date'))
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

    public function getDayPlansManager(Request $request)
    {
        $rules = [
            'location_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('locations', 'id'),
            ],
            'date'        => 'date_format:d-m-Y'
        ];

        $request_data = request()->all();

        $validator = Validator::make($request_data, $rules, []);
        if ($validator->fails()) {
            return returnResponse(
                [
                    'success' => true,
                    'message' => $validator->errors()->all()
                ],
                JsonResponse::HTTP_BAD_REQUEST,
            );
        }
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningService->getDayPlansManager($validator->validated())
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
