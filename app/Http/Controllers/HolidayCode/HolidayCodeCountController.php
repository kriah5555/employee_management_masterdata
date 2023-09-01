<?php

namespace App\Http\Controllers\HolidayCode;

use App\Models\HolidayCodeCount;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\HolidayCodeCountRequest;
use App\Http\Controllers\Controller;

class HolidayCodeCountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $holiday_code_count = HolidayCodeCount::with('holidayCodes')->get();
        return response()->json([
            'success' => true,
            'data'    => $holiday_code_count,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HolidayCodeCountRequest $request)
    {

        try {
            $holiday_code_count = HolidayCodeCount::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Holiday count created successfully',
                'data'    => $holiday_code_count,
            ]);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }


        return response()->json($holiday_code_count, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(HolidayCodeCount $holiday_code_count)
    {
        $holiday_code_count->holidayCodes;
        return response()->json([
            'success' => true,
            'data'    => $holiday_code_count,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HolidayCodeCountRequest $request, HolidayCodeCount $holiday_code_count)
    {
        try {
            $holiday_code_count->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Holiday count updated successfully',
                'data'    => $holiday_code_count,
            ]);
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
     * Remove the specified resource from storage.
     */
    public function destroy(HolidayCodeCount $holiday_code_count)
    {
        $holiday_code_count->delete();
        return response()->json([
            'success' => true,
            'message' => 'Holiday count deleted successfully'
        ]);
        return response()->json(null, 204);
    }
}