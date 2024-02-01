<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\Company\DashboardAccessService;
use Exception;

class DashboardAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $dashboardService;

    public function __construct(DashboardAccessService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getDashboardAccessKeyForCompany()
    {
        try {
            $companyId = getCompanyId();
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'access_key' => $this->dashboardService->getDashboardAccessKeyForCompany($companyId)
                    ]
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
    public function getDashboardAccessKeyForLocation($locationId)
    {
        try {
            $companyId = getCompanyId();
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'access_key' => $this->dashboardService->getDashboardAccessKeyForLocation($companyId, $locationId)
                    ]
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
    public function revokeDashboardAccessKey($access_key)
    {
        try {
            $this->dashboardService->deleteDashboardAccessKey($access_key);
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Dashboard access revoked for all devices')
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
    public function validateCompanyDashboardAccessKey($access_key)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'access' => $this->dashboardService->validateCompanyDashboardAccessKey($access_key)
                    ]
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
    public function validateLocationDashboardAccessKey($access_key)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'access' => $this->dashboardService->validateLocationDashboardAccessKey($access_key)
                    ]
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
