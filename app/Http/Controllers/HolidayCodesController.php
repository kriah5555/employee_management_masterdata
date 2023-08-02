<?php

namespace App\Http\Controllers;

use App\Models\HolidayCodes;
use App\Services\HolidayCodeService;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\HolidayCodeRequest;


class HolidayCodesController extends Controller
{
    protected $holiday_code_service;

    public function __construct(HolidayCodeService $holiday_code_service)
    {
        $this->holiday_code_service = $holiday_code_service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->holiday_code_service->getAllHolidayCodes();
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }   
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HolidayCodeRequest $request)
    {
        try {
            $data = $this->holiday_code_service->getAllHolidayCodes($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Holiday code created successfully',
                'data'    => $data,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }    
    }

    /**
     * Display the specified resource.
     */
    public function show(HolidayCodes $holiday_code)
    {
        return response()->json([
            'success' => true,
            'data' => $holiday_code,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HolidayCodeRequest $request, HolidayCodes $holiday_code)
    {
        try {
            $this->holiday_code_service->updateHolidayCode($holiday_code, $request->validated());
            $holiday_code->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Holiday code updated successfully',
                'data' => $holiday_code,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HolidayCodes $holiday_code)
    {
        $holiday_code->delete();
        return response()->json([
            'success' => true,
            'message' => 'Holiday code deleted successfully'
        ]);
    }
}
