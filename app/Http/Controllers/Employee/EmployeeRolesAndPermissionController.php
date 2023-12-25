<?php

namespace App\Http\Controllers\Employee;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Employee\EmployeeRolesAndPermissionService;

class EmployeeRolesAndPermissionController extends Controller
{
    public function __construct(
        protected EmployeeRolesAndPermissionService $employeeRolesAndPermissionService,
    ) {
    }
    
    public function store()
    {
    }

    /**
     * API to get the employee type details.
     */
    public function show($employee_profile_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeRolesAndPermissionService->getEmployeeRolesPermissions($employee_profile_id, getCompanyId())
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function create()
    {
       
    }

    public function destroy()
    {
       
    }

    public function update(Request $request, $employee_profile_id)
    {
        try {
            $this->employeeRolesAndPermissionService->updateEmployeeRolesAndPermissions($employee_profile_id, $request->all(), getCompanyId());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Roles and Permissions updated successfully')
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
