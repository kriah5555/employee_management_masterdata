<?php

namespace App\Http\Controllers\Holiday;

use App\Models\Holiday\HolidayCodes;
use App\Services\Holiday\HolidayCodeService;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\Holiday\HolidayCodeRequest;
use App\Http\Controllers\Controller;

class HolidayCodesController extends Controller
{
    public function __construct(protected HolidayCodeService $holiday_code_service)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holiday_code_service->getAll(['with' => ['processCountAttribute']])
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

    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holiday_code_service->getOptionsToCreate(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function edit($holiday_code_id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holiday_code_service->getOptionsToEdit($holiday_code_id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HolidayCodeRequest $request, HolidayCodes $holiday_code)
    {
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