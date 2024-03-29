<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Services\Planning\UurroosterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Planning\UurroosterRequest;


class UurroosterController extends Controller
{
    public function __construct(protected UurroosterService $uurroosterService)
    {
    }

    public function getUurroosterData(UurroosterRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->uurroosterService->getUurroosterData($request->all())
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getActivatedUurroosterData(Request $request)
    {
        try {
            $rules = [
                'date'        => [
                    'required',
                    'date_format:d-m-Y',
                ],
                'location_id' => [
                    'required',
                    'integer',
                ],
                'access_key'  => [
                    'required',
                    'string',
                ],
            ];

            $customMessages = [
                'dashboard_token.required' => 'Dashboard token required',
            ];

            $validator = Validator::make(request()->all(), $rules, $customMessages);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }
            $input = $request->only(['date', 'location_id', 'dashboard_token']);
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->uurroosterService->getUurroosterData($input)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
