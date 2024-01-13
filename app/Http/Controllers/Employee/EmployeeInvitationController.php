<?php

namespace App\Http\Controllers\Employee;

use App\Http\Requests\Employee\EmployeeInvitationRequest;
use App\Models\Company\Employee\EmployeeInvitation;
use App\Services\Employee\EmployeeInvitationService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;

class EmployeeInvitationController extends Controller
{

    public function __construct(
        protected EmployeeInvitationService $employeeInvitationService
    ) {
    }

    /**
     * API to get the details required for creating an employee type.
     */
    public function store(EmployeeInvitationRequest $request)
    {
        try {
            $this->employeeInvitationService->sendInvitation($request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Invitation sent'
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

    public function validateEmployeeInvitation(EmployeeInvitationRequest $request)
    {
        try {
            $employeeInvitation = $request->all()['employee_invitation'];
            return returnResponse(
                [
                    'success' => true,
                    'data'    => json_decode($employeeInvitation->data)
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

    public function employeeRegistration(EmployeeInvitationRequest $request)
    {
        try {
            $token = $request->validated()['token'];
            $token = decodeData($token);
            $companyId = $token['company_id'];
            $token = $token['token'];
            setTenantDBByCompanyId($companyId);
            $employeeInvitation = EmployeeInvitation::where('token', $token)->get()->first();
            if ($employeeInvitation) {
                if ($employeeInvitation->invitation_status == 1 && strtotime($employeeInvitation->expire_at) > strtotime(date('Y-m-d H:i'))) {
                    return returnResponse(
                        [
                            'success' => true,
                            'data'    => json_decode($employeeInvitation->data)
                        ],
                        JsonResponse::HTTP_OK,
                    );
                } elseif (strtotime($employeeInvitation->expire_at) <= strtotime(date('Y-m-d H:i'))) {
                    throw new AuthenticationException("Link expired");
                } else {
                    throw new AuthenticationException("Invalid token");
                }
            } else {
                throw new AuthenticationException("Invalid token");
            }
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
}
