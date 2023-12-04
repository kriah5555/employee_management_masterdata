<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\AvailabilityService;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Company\AvailabilityRequest;

class AvailabilityController extends Controller
{
    public $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function createAvailability(AvailabilityRequest $request)
    {
        try {
            // exit;
            return response()->json([
                'success' => true,
                'message' => $this->availabilityService->createAvailability($request->validated())
            ], JsonResponse::HTTP_OK,);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



public function availableDateAndNOtAvailableDates()
{

    $rules = [
        'employee_id' => 'required|integer',
        'period' => 'required|regex:/^\d{2}-\d{4}$/',
    ];

    $customMessages = [
        'employee_id.required' => 'Employee ID is required.',
        'employee_id.integer' => 'Employee ID must be an integer.',
        'period.required' => 'Date is required.',
        'period.regex' => 'The date should be in the day-month-year format (e.g., 12-2023).',
    ];

    $validator = Validator::make(request()->all(), $rules, $customMessages);

    if ($validator->fails()) {
        $errorMessages = $validator->errors()->all();
        return response()->json(['status' => false, 'message' => $errorMessages[0]], 400);
    }

    try {
        return response()->json([
            'success' => true,
            'available_dates' => $this->availabilityService->avilableDates(request()),
            'notAvailable_dates' => $this->availabilityService->notAvailableDates(request()),
            'date_overview' => $this->availabilityService->dateOverView(request())
        ], JsonResponse::HTTP_OK);
    } catch (\Exception $e) {
        return response()->json([
            "success" => false,
            "message" => $e->getMessage(),
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    public function updateAvailability(AvailabilityRequest $request, $id)
    {

        try {
            return response()->json(
                [
                    'success' => true,
                    'message' => $this->availabilityService->updateAvailability($request->validated(), $id)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteAvailability(Request $request)
    {
        $rules = [
            'availability_id' => ['required','integer'],
        ];

        $customMessages = [
            'availability_id.required' => 'availability_id ID is required.',
            'availability_id.integer' => 'availability_id ID must be an integer.',
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            return response()->json(['status' => false, 'message' => $errorMessages[0]], 400);
        }

        try {
            return response()->json(
                [
                    'success' => true,
                    'message' => $this->availabilityService->deleteAvailability($request)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
