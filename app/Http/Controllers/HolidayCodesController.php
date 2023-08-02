<?php

namespace App\Http\Controllers;

use App\Models\HolidayCodes;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\HolidayCodeRequest;

class HolidayCodesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = HolidayCodes::all();
        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HolidayCodeRequest $request)
    {
        try {
            $holidayCodes = HolidayCodes::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Holiday code created successfully',
                'data'    => $holidayCodes,
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
            $holiday_code->update($request->validated());
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
