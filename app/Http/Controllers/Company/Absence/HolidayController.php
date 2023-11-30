<?php

namespace App\Http\Controllers\Company\Absence;

use Illuminate\Http\Request;
use App\Services\Company\Absence\HolidayService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Absence\HolidayRequest;

class HolidayController extends Controller
{
    public function __construct(protected HolidayService $holidayService)
    {
    }

    /**
     * Display a listing of the resource.
     */

    public function index($employee_id, $status)
    {
        try {
            $status = config('absence.'.strtoupper($status));
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->holidayService->getHolidays($employee_id, $status),
                ],
                JsonResponse::HTTP_OK,
            );
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

    public function updateHolidayStatus($holiday_id, $status)
    {
        try {
            $status = config('absence.'.strtoupper($status));
            $this->holidayService->updateHolidayStatus($holiday_id, $status);
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Holiday status updated successfully'),
                ],
                JsonResponse::HTTP_CREATED,
            );
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    // 'data'    => $this->holidayService->getOptionsToCreate()
                ],
                JsonResponse::HTTP_CREATED,
            );
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request_data = $request->all();
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Holiday created successfully'),
                    'data'    => $this->holidayService->applyHoliday($request_data)
                ],
                JsonResponse::HTTP_CREATED,
            );
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
     * Display the specified resource.
     */
    public function show($holidayId)
    {
        $holiday_details = $this->holidayService->getHolidayById($holidayId, ['absenceDates', 'absenceHours.holidayCode']);
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $holiday_details
                ],
                JsonResponse::HTTP_CREATED,
            );
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $holidayId)
    {
        try {
            $this->holidayService->updateAppliedHoliday($holidayId, $request->all());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Location updated successfully'),
                ],
                JsonResponse::HTTP_CREATED,
            );
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
    public function destroy($holidayId)
    {
        $this->holidayService->deleteHoliday($holidayId);
        return response()->json([
            'success' => true,
            'message' => t('Location deleted successfully')
        ]);
    }
}
