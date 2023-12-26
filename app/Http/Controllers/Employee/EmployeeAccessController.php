<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Employee\EmployeeAccessService;

class EmployeeAccessController extends Controller
{
    public function __construct(protected EmployeeAccessService $employee_access_service)
    {

    }

    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    // 'data'    => $this->companyService->getCompanies(),
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

    public function create()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    // 'data'    => $this->companyService->getCompanies(),
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

    public function store(Request $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    // 'data'    => $this->companyService->getCompanies(),
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
    
    public function show(string $employee_profile_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employee_access_service->getEmployeeRolesPermissions($employee_profile_id, getCompanyId())
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
    
    public function update(Request $request, $employee_profile_id)
    {
        try {
            $this->employee_access_service->updateEmployeeRolesAndPermissions($employee_profile_id, $request->all(), getCompanyId());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Roles and Permissions updated successfully')
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
    
    public function destroy(string $id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    // 'data'    => $this->companyService->getCompanies(),
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
}
