<?php

namespace App\Http\Controllers\Company\Absence;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Company\Absence\HolidayService;
use App\Http\Requests\Company\Absence\HolidayRequest;

class HolidayController extends Controller
{
    public function __construct(protected HolidayService $holidayService)
    {
    }

    /**
     * Display a listing of the resource.
     */

    public function index($status)
    {
        try {
            $status = config('absence.'.strtoupper($status));
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->holidayService->getHolidays('', $status),
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



    public function employeeHolidays($employee_id, $status)
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
                    'data'    => $this->holidayService->getOptionsToCreate(getCompanyId())
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
    public function store(HolidayRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Holiday created successfully'),
                    'data'    => $this->holidayService->applyHoliday($request->toArray()),
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
    public function update(HolidayRequest $request, $holidayId)
    {
        try {
            $this->holidayService->updateAppliedHoliday($holidayId, $request->all());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Holiday updated successfully'),
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
            'message' => t('Holiday deleted successfully')
        ]);
    }
}
