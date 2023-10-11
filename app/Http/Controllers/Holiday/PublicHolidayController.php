<?php

namespace App\Http\Controllers\Holiday;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\Holiday\PublicHolidayRequest;
use App\Services\Holiday\PublicHolidayService;
use App\Models\Holiday\PublicHoliday;

class PublicHolidayController extends Controller
{
    public function __construct(protected PublicHolidayService $public_holiday_service)
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
                'data'    => $this->public_holiday_service->getAll()
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->public_holiday_service->getOptionsToCreate(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PublicHolidayRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Public holiday created successfully',
                'data'    => $this->public_holiday_service->create($request->validated()),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(PublicHoliday $public_holiday)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $public_holiday,
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($public_holiday_id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->public_holiday_service->getOptionsToEdit($public_holiday_id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PublicHolidayRequest $request, PublicHoliday $public_holiday)
    {
        
        return returnResponse(
            [
                'success' => true,
                'message' => 'Public holiday updated successfully',
                'data'    => $this->public_holiday_service->update($public_holiday, $request->validated()),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PublicHoliday $public_holiday)
    {
        $public_holiday->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Public holiday deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }
}
