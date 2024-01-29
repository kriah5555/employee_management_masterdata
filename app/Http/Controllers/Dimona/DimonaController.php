<?php

namespace App\Http\Controllers\Dimona;

use App\Http\Controllers\Controller;
use App\Services\Dimona\DimonaBaseService;
use App\Services\Dimona\DimonaSenderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendDimonaByPlanJob;

class DimonaController extends Controller
{

    private $dimonaBaseService;
    public function __construct(DimonaBaseService $dimonaBaseService)
    {
        $this->dimonaBaseService = $dimonaBaseService;
    }

    public function testDimona(Request $request, $planId)
    {
        $companyId = $request->header('Company-Id');

        try {
            $data = $this->dimonaBaseService->initiateDimonaByPlanService($companyId, $planId);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'file'    => $e->getFile(),
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ]
            );
        }
    }

    public function sendDimonaByPlan(Request $request)
    {
        try {
            $rules = [
                'plans' => 'required|array'
            ];
            $validator = Validator::make(request()->all(), $rules);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }
            $data = $validator->validated();
            $companyId = getCompanyId();
            foreach ($data['plans'] as $planId) {
                app(DimonaSenderService::class)->sendDimonaByPlan($companyId, $planId);
                dispatch(new SendDimonaByPlanJob($companyId, $planId));
            }
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Dimona request sent.',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function sendDimonaByEmployeeContract($dimonaType, $employeeContract)
    {
        $data = NULL;

        try {
            $data = $this->dimonaBaseService->initiateDimonaByContract($dimonaType, $employeeContract);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'file'    => $e->getFile(),
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ],
                500
            );
        }
    }

    public function updateDimonaResponse(Request $request)
    {
        try {
            $rules = [
                'unique_id' => 'required|string',
                'data'      => 'required|string'
            ];
            $validator = Validator::make(request()->all(), $rules);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }
            $data = $validator->validated();
            $this->dimonaBaseService->updateDimonaResponse($data['unique_id'], $data['data']);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Dimona request sent.',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
