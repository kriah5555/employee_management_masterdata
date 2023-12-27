<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Services\Planning\PlanningShiftsService;
use Illuminate\Http\JsonResponse;
use \Illuminate\Http\Request;
use App\Rules\BelgiumCurrencyFormatRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlanningShiftController extends Controller
{
    public function __construct(protected PlanningShiftsService $planningShiftsService)
    {
    }

    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningShiftsService->getPlanningShifts(),
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
    public function storePlanningShifts(Request $request)
    {
        try {
            $rules = [
                'location_id'             => [
                    'required',
                    'integer',
                    Rule::exists('locations', 'id'),
                ],
                'workstation_id'          => [
                    'required',
                    'integer',
                    Rule::exists('workstations', 'id'),
                ],
                'shifts'                  => 'array',
                'shifts.*.start_time'     => 'required|date_format:H:i',
                'shifts.*.end_time'       => 'required|date_format:H:i',
                'shifts.*.contract_hours' => [
                    'required',
                    'string',
                    new BelgiumCurrencyFormatRule
                ],
            ];

            $customMessages = [
                'employee_id.required' => 'Please select employee',
                'employee_id.integer'  => 'Employee ID must be an integer.',
                'date.integer'         => 'Date required.',
                'date.date'            => 'Incorrect date format.',
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
            $this->planningShiftsService->storePlanningShifts($validator->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Planning shifts updated',
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
    public function createShiftPlan(Request $request)
    {
        try {
            $rules = [
                'location_id'    => [
                    'required',
                    'integer',
                    Rule::exists('locations', 'id'),
                ],
                'workstation_id' => [
                    'required',
                    'integer',
                    Rule::exists('workstations', 'id'),
                ],
                'employee_id'    => [
                    'required',
                    'integer',
                    Rule::exists('employee_profiles', 'id'),
                ],
                'shift_id'       => [
                    'required',
                    'integer',
                    Rule::exists('planning_shifts', 'id'),
                ],
                'date'           => 'required|date_format:d-m-Y',
            ];

            $customMessages = [
                'employee_id.required' => 'Please select employee',
                'employee_id.integer'  => 'Employee ID must be an integer.',
                'date.integer'         => 'Date required.',
                'date.date'            => 'Incorrect date format.',
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
            $this->planningShiftsService->createShiftPlan($validator->validated());
            return returnResponse(
                [
                    'success'      => true,
                    'plan_created' => true,
                    'message'      => 'Plan created',
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
