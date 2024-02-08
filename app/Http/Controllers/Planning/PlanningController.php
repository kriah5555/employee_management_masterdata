<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Services\Planning\PlanningService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Planning\GetWeeklyPlanningRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Planning\GetDayPlanningRequest;
use Exception;


class PlanningController extends Controller
{
    public function __construct(protected PlanningService $planningService)
    {
    }

    /**
     * Planning overview options function
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getPlanningOverviewOptions(Request $request)
    {
        try {
            $companyId = $request->header('company-id');
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Planning options',
                    'data'    => $this->planningService->getPlanningOverviewFilterService($companyId),
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

    /**
     * Get monthly planning data
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getMonthlyPlanning(Request $request)
    {
        try {
            $rules = [
                'location'         => [
                    'required',
                    'integer',
                    Rule::exists('locations', 'id'),
                ],
                'workstations'     => 'array',
                'workstations.*'   => [
                    'required',
                    'integer',
                    Rule::exists('workstations', 'id'),
                ],
                'employee_types'   => 'array',
                'employee_types.*' => [
                    'required',
                    'integer',
                    Rule::exists('employee_types', 'id'),
                ],
                'month'            => 'required|min:1|max:12',
                'year'             => 'required|digits:4',
            ];

            $customMessages = [
                'employee_id.required'    => 'Please select employee',
                'employee_id.integer'     => 'Employee ID must be an integer.',
                'date.integer'            => 'Date required.',
                'year.digits'             => 'Incorrect year format.',
                'workstations.*.exists'   => 'Invalid workstation selected',
                'employee_types.*.exists' => 'Invalid employee type selected'
            ];

            $validator = Validator::make(request()->all(), $rules, $customMessages);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }
            $input = $request->only(['location', 'workstations', 'employee_types', 'month', 'year']);
            $data = $this->planningService->getMonthlyPlanningService($input['year'], $input['month'], $input['location'], $input['workstations'], $input['employee_types']);

            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Monthly planning',
                    'data'    => $data
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

    /**
     * Get weekly planning info.
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getWeeklyPlanning(GetWeeklyPlanningRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningService->getWeeklyPlanningService($request->input('location'), $request->input('workstations'), $request->input('employee_types'), $request->input('week'), $request->input('year'))
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

    /**
     * Get day planning
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getDayPlanning(GetDayPlanningRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningService->getDayPlanningService(
                        $request->input('location'),
                        $request->input('workstations'),
                        $request->input('employee_types'),
                        date('Y-m-d', strtotime($request->input('date'))),
                        $request->input('employee_profile_id'),
                    )
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

    public function getDayPlanningMobile(GetDayPlanningRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningService->getDayPlanningMobileService(
                        $request->input('location'),
                        $request->input('workstations'),
                        $request->input('employee_types'),
                        date('Y-m-d', strtotime($request->input('date'))),
                        $request->input('employee_profile_id'),
                    )
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

    public function getEmployeeDayPlanning($employee_profile_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningService->getPlans(date('d-m-Y'), date('d-m-Y'), '', '', '', $employee_profile_id, ['workStation', 'employeeProfile.user', 'employeeType', 'functionTitle'])
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

    /**
     * Get employee planning options by workstation and employeeId.
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function planningCreateOptions(Request $request)
    {
        $input = $data = [];
        try {
            $input = $request->only(['workstation', 'employee_id']);
            $data = $this->planningService->planningCreateOptionsService($input['workstation'], $input['employee_id']);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee options',
                    'data'    => $data
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

    public function getPlanDetails($planId)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningService->getPlanningDetailsForDayOverview($planId)
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

    public function getPlansForAbsence(Request $request)
    {
        try {
            $rules = [
                'dates'               => ['bail', 'required', 'array',],
                'employee_profile_id' => ['bail', 'required', 'integer'],
                'dates.*'             => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT')
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

            if (isset($request_data['dates']['from_date']) && (!isset($request_data['dates']['to_date']))) {
                throw new Exception('dates.to date field is required');
            } elseif (isset($request_data['dates']['from_date']) && isset($request_data['dates']['to_date'])) {
                $request_data['dates'] = getDatesArray($request_data['dates']['from_date'], $request_data['dates']['to_date']);
            }

            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningService->getPlansForAbsence($request_data['dates'], $request->employee_profile_id)
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

    /**
     * Get weekly planning info.
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function getWeeklyPlanningForEmployee(GetWeeklyPlanningRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningService->getWeeklyPlanningForEmployee($request->input('employee_profile_id'), $request->input('location'), $request->input('workstations'), $request->input('employee_types'), $request->input('week'), $request->input('year'), $request->input('workstation_id'))
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

    public function getPlansToSendDimona(Request $request)
    {
        $rules = [
            'location_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('locations', 'id'),
            ],
            'date'        => 'date_format:d-m-Y|after_or_equal:' . now()->format('Y-m-d')
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
                    'data'    => $this->planningService->getPlansToSendDimona($validator->validated())
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
