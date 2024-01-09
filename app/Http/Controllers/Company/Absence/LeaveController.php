<?php

namespace App\Http\Controllers\Company\Absence;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Absence\HolidayRequest;
use App\Services\Company\Absence\LeaveService;
use App\Http\Requests\Company\Absence\AbsenceChangeReportingManagerRequest;

class LeaveController extends Controller
{
    public function __construct(protected LeaveService $leave_service)
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
                    'data'    => $this->leave_service->getLeaves($status),
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

    public function changeLeaveManager(AbsenceChangeReportingManagerRequest $request)
    {
        try {
            $this->leave_service->updateResponsiblePerson($request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Manager updated successfully'),
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

    public function updateLeaveStatus($leave_id, $status)
    {
        try {
            $status = config('absence.'.strtoupper($status));
            $this->leave_service->updateLeaveStatus($leave_id, $status);
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Leave status updated successfully'),
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
                    'data'    => $this->leave_service->getOptionsToCreate(getCompanyId())
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
                    'message' => t('Leave created successfully'),
                    'data'    => $this->leave_service->applyLeave($request_data)
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
    public function show($leaveId)
    {
        $leave_details = $this->leave_service->getLeaveById($leaveId, ['absenceDates', 'absenceHours.holidayCode']);
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $leave_details
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
    public function update(Request $request, $leaveId)
    {
        try {
            $this->leave_service->updateApprovedLeave($leaveId, $request->all());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Leave updated successfully'),
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
    public function destroy($leaveId)
    {
        $this->leave_service->deleteLeave($leaveId);
        return response()->json([
            'success' => true,
            'message' => t('Leave deleted successfully')
        ]);
    }
}
