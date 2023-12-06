<?php

namespace App\Http\Controllers\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Company\AvailabilityService;
use App\Http\Requests\Company\AvailabilityRequest;

class AvailabilityController extends Controller
{

    public function __construct(protected AvailabilityService $availabilityService)
    {
    }

     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rules = [
            'period' => 'required|regex:/^\d{2}-\d{4}$/',
        ];

        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }
        try {
            return response()->json([
                'success' => true,
                'available_dates' => $this->availabilityService->availableDates(request()),
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(AvailabilityRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Availability created successfully',
                    'data'    => $this->availabilityService->createAvailability($request->validated()),
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (\Exception $e) {
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
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AvailabilityRequest $request, $id)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            return response()->json(
                [
                    'success' => true,
                    'message' => $this->availabilityService->deleteAvailability($id)
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
