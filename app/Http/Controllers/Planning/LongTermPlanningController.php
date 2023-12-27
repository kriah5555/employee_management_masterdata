<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Services\Planning\LongTermPlanningService;
use App\Services\Planning\PlanningService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Planning\GetWeeklyPlanningRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Planning\GetDayPlanningRequest;
use App\Rules\BelgiumCurrencyFormatRule;


class LongTermPlanningController extends Controller
{
    public function __construct(
        protected PlanningService $planningService,
        protected LongTermPlanningService $longTermPlanningService
    ) {
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
                'employee_id'                  => [
                    'required',
                    'integer',
                    Rule::exists('employee_profiles', 'id'),
                ],
                'start_date'                   => 'required|date_format:d-m-Y',
                'end_date'                     => 'date_format:d-m-Y',
                'repeating_week'               => 'required|integer',
                'plannings.*'                  => 'required|array|min:1',
                'plannings.*.*.day'            => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'plannings.*.*.start_time'     => 'required|date_format:H:i',
                'plannings.*.*.end_time'       => 'required|date_format:H:i',
                'plannings.*.*.contract_hours' => [
                    'required',
                    'string',
                    new BelgiumCurrencyFormatRule
                ],
                'plannings.*.*.location_id'    => [
                    'required',
                    'integer',
                    Rule::exists('locations', 'id'),
                ],
                'plannings.*.*.workstation_id' => [
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
            dd($request->only('employee_id', 'start_date', 'end_date', 'repeating_week', 'plannings'));
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
}
