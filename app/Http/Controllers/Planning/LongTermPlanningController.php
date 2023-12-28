<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Models\Planning\LongTermPlanning;
use App\Services\Planning\LongTermPlanningService;
use App\Services\Planning\PlanningService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Rules\BelgiumCurrencyFormatRule;
use Exception;


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
                'end_date'                     => 'after:start_date|date_format:d-m-Y',
                'repeating_week'               => 'required|integer',
                'location_id'                  => [
                    'required',
                    'integer',
                    Rule::exists('locations', 'id'),
                ],
                'workstation_id'               => [
                    'required',
                    'integer',
                    Rule::exists('workstations', 'id'),
                ],
                'function_id'                  => [
                    'required',
                    'integer',
                    Rule::exists('master.function_titles', 'id'),
                ],
                'plannings.*'                  => 'required|array',
                'plannings.*.*.day'            => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'plannings.*.*.start_time'     => 'required|date_format:H:i',
                'plannings.*.*.end_time'       => 'required|date_format:H:i',
                'plannings.*.*.contract_hours' => [
                    'required',
                    'string',
                    new BelgiumCurrencyFormatRule
                ],
                'auto_renew'                   => 'required|boolean',
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
            $values = $validator->validated();

            $values['start_date'] = $startDate = date('Y-m-d', strtotime($values['start_date']));
            $values['end_date'] = $endDate = array_key_exists('end_time', $values) ? date('Y-m-d', strtotime($values['end_date'])) : date('Y-m-d', strtotime($values['start_date'] . '+1 year'));
            $plans = LongTermPlanning::where('employee_profile_id', $values['employee_id'])
                ->where('location_id', $values['location_id'])
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where(function ($query) use ($startDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $startDate);
                    })->orWhere(function ($query) use ($endDate) {
                        $query->where('start_date', '<=', $endDate)
                            ->where('end_date', '>=', $endDate);
                    })->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '>=', $startDate)
                            ->where('end_date', '<=', $endDate);
                    });
                })
                ->get();
            if (!$plans->isEmpty()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => 'Dates overlapping with other long term plannings',
                    ],
                    JsonResponse::HTTP_OK,
                );
            }
            $this->longTermPlanningService->storeLongTermPlannings($values);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Long term plannings saved',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getEmployeeLongTermPlannings()
    {

        try {
            $rules = [
                'employee_id' => [
                    'required',
                    'integer',
                    Rule::exists('employee_profiles', 'id'),
                ],
            ];

            $customMessages = [
                'employee_id.required' => 'Please select employee',
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
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->longTermPlanningService->getEmployeeLongTermPlannings($validator->validated()['employee_id']),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
