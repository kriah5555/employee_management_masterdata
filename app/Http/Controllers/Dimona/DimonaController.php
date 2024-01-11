<?php

namespace App\Http\Controllers\Dimona;

use App\Http\Controllers\Controller;
use App\Services\Dimona\DimonaBaseService;
use Illuminate\Http\Request;

// use Illuminate\Http\Request;

class DimonaController extends Controller
{
 
    private $dimonaBaseService;
    public function __construct()
    {
        $this->dimonaBaseService = new DimonaBaseService();
    }

    public function sendDimonaByPlan(Request $request, $planId)
    {
        $companyId = $request->header('Company-Id');
        // $dimonaType = $planId = $timeRegistrationId = '';
        $data = NULL;
        try {
            $data = $this->dimonaBaseService->initiateDimonaByPlanService($companyId, $planId);
        } catch(\Exception $e) {
            return response()->json(
                [
                    'file' => $e->getFile(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
        }

        return $data;
    }

    public function sendDimonaByEmployeeContract($dimonaType, $employeeContract)
    {
        $data = NULL;

        try {
            $data = $this->dimonaBaseService->initiateDimonaByContract($dimonaType, $employeeContract);
            return response()->json($data);
        }
        catch(\Exception $e) {
            return response()->json(
                [
                    'file' => $e->getFile(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ], 
                500
            );
        }
    }


    public function updateDimonaStatus(Request $request)
    {
        $data = '';
        try {
            $data = $request->all();
            $data = $this->dimonaBaseService->updateDimonaStatusService($data);

        } catch (\Exception $e) {
            return response()->json(
                [
                    'file' => $e->getFile(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
        }
        return $data;
    }
}