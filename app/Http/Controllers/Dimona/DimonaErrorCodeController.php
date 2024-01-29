<?php

namespace App\Http\Controllers\Dimona;

use App\Models\Dimona\DimonaErrorCode;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Dimona\DimonaErrorCodeService;
use App\Http\Resources\Dimona\DimonaErrorCodeResource;
use App\Http\Requests\Dimona\DimonaErrorCodeRequest;

class DimonaErrorCodeController extends Controller
{
    protected $dimonaErrorCodeService;

    public function __construct(DimonaErrorCodeService $dimonaErrorCodeService)
    {
        $this->dimonaErrorCodeService = $dimonaErrorCodeService;
    }

    /**
     * API to get list of all dimona error codes
     */
    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => DimonaErrorCodeResource::collection($this->dimonaErrorCodeService->getDimonaErrorCodes()),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
     * Update the existing dimona error code
     */
    public function update(DimonaErrorCodeRequest $request, DimonaErrorCode $dimonaErrorCode)
    {
        try {
            $this->dimonaErrorCodeService->updateDimonaErrorCode($dimonaErrorCode, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Dimona error code updated successfully'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
