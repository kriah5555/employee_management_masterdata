<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Models\DashboardAccess;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\Company\DashboardAccessService;
use App\Http\Requests\Company\DashboardAccessRequest;

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

    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->dashboardService->getDashboardAccess(request()),
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(DashboardAccessRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Dashboard Access created successfully'),
                    'data'    => $this->dashboardService->createDashboardAccess($request->all())
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
    public function show(Request $dashboardAccess)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DashboardAccess $dashboardAccess)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DashboardAccess $dashboardAccess)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($unique_key)
    {
        $this->dashboardService->deleteDashboardAccess($unique_key);
        return response()->json([
            'success' => true,
            'message' => t('Dashboard Access deleted successfully')
        ]);
    }
}
