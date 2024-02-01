<?php

namespace App\Http\Controllers\Dimona;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dimona\DimonaOverviewService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class DimonaOverviewController extends Controller
{
    public function __construct(protected DimonaOverviewService $dimonaOverviewService)
    {
    }

    public function getDimonaOverview(Request $request)
    {
        $rules = [
            'from_date' => 'required|date_format:d-m-Y',
            'to_date'   => 'required|date_format:d-m-Y|after_or_equal:from_date',
            'type'      => 'required|string|in:all,plan,long_term,flex_check'
        ];
        $validator = Validator::make(request()->all(), $rules);
        if ($validator->fails()) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $validator->errors()->all()
                ],
                JsonResponse::HTTP_BAD_REQUEST,
            );
        }
        $data = $validator->validated();
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->dimonaOverviewService->getDimonaOverview($data['from_date'], $data['to_date'], $data['type']),
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
    public function getDimonaDetails($dimonaId)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->dimonaOverviewService->getDimonaDetails($dimonaId),
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
