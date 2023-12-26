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


class LongTermPlanningController extends Controller
{
    public function __construct(protected PlanningService $planningService)
    {
    }

    /**
     * Get monthly planning data
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     */
    public function storeLongTermPlanning(Request $request)
    {
        try {
            $rules = [
                'employee_id'                => [
                    'required',
                    'integer',
                    Rule::exists('employee_profiles', 'id'),
                ],
                'start_date'                 => 'required|date_format:d-m-Y',
                'end_date'                   => 'date_format:d-m-Y',
                'function_id'                => [
                    'required',
                    'integer',
                    Rule::exists('function_titles', 'id'),
                ],
                'repeating_week'             => 'required|integer',
                'plannings.*'                => 'required|array',
                'plannings.*.start_time'     => 'required|date_format:H:i',
                'plannings.*.end_time'       => 'required|date_format:H:i',
                'plannings.*.contract_hours' => 'required|integer',
                'plannings.*.location_id'    => [
                    'required',
                    'integer',
                    Rule::exists('locations', 'id'),
                ],
                'plannings.*.workstation_id' => [
                    'required',
                    'integer',
                    Rule::exists('workstations', 'id'),
                ],
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
            dd($request->validated());
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
                        date('Y-m-d', strtotime($request->input('date')))
                    )
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getPlanDetails($planId)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningService->getPlanningById($planId)
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
