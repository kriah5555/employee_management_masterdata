<?php

namespace App\Http\Controllers\Company\Absence;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Company\Absence\LeaveService;
use App\Services\Company\Absence\AbsenceService;
use App\Http\Requests\Company\Absence\LeaveRequest;
use App\Http\Requests\Company\Absence\AbsenceChangeReportingManagerRequest;
use App\Services\Holiday\HolidayCodeService;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Http\Resources\Absence\HolidayCodeResource;

class LeaveController extends Controller
{
    public function __construct(
        protected LeaveService $leave_service,
        protected HolidayCodeService $holidayCodeService
    ) {
    }

    /**
     * Display a listing of the resource.
     */

    public function index($status)
    {
        try {
            $status = config('absence.' . strtoupper($status));
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

    public function getAllLeavesForMobile(Request $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->leave_service->getLeavesMobile($request->employee_profile_id),
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

    public function updateLeaveStatus(Request $request)
    {
        try {
            $this->leave_service->updateLeaveStatus($request->leave_id, $request->status, $request->reason);
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
                    'data'    => [
                        'leave_codes' => HolidayCodeResource::collection($this->holidayCodeService->getCompanyLeaveCodesTest(getCurrentCompanyId())),
                        'employees'   => app(EmployeeProfileRepository::class)->getEmployeesForHoliday()
                    ]
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
    public function store(LeaveRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Leave created successfully'),
                    'data'    => $this->leave_service->applyLeave($request->validated())
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
    public function addLeave(LeaveRequest $request)
    {
        try {
            $request_name = $request->route()->getName();
            $absence_status = $request_name == 'add-leave' || $request_name == 'update-leave' || $request_name == 'shift-leave' ? config('absence.APPROVE') : config('absence.PENDING');
            $shift_leave = in_array($request_name, ['shift-leave', 'employee-shift-leave']);
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Leave created successfully'),
                    'data'    => $this->leave_service->applyLeave($request->validated(), $absence_status, $shift_leave)
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
        $leave_details = app(AbsenceService::class)->formatAbsenceDataForOverview($this->leave_service->getLeaveById($leaveId, ['absenceDates', 'absenceHours.holidayCode']));
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
    public function update(LeaveRequest $request, $leaveId)
    {
        try {
            $this->leave_service->updateApprovedLeave($leaveId, $request->validated());
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
