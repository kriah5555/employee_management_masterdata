<?php

namespace App\Http\Controllers\HolidayCode;

use App\Models\HolidayCodes;
use App\Services\HolidayCode\HolidayCodeService;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\HolidayCodeRequest;
use App\Http\Controllers\Controller;

class HolidayCodesController extends Controller
{
    public function __construct(protected HolidayCodeService $holiday_code_service)
    {
        $this->holiday_code_service = $holiday_code_service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holiday_code_service->getAll()
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HolidayCodeRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holiday_code_service->create($request->validated()),
                'message' => 'Holiday code created successfully',
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(HolidayCodes $holiday_code)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $holiday_code,
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HolidayCodeRequest $request, HolidayCodes $holiday_code)
    {
        dd(url()->current());
        $this->holiday_code_service->update($holiday_code, $request->validated());
        $holiday_code->refresh();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Holiday code updated successfully',
                'data'    => $holiday_code,
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HolidayCodes $holiday_code)
    {
        $holiday_code->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Holiday code deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }
}