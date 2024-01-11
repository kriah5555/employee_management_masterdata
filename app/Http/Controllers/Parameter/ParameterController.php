<?php

namespace App\Http\Controllers\Parameter;

use App\Http\Controllers\Controller;
use App\Models\Parameter\Parameter;
use App\Services\Parameter\ParameterService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Parameter\ParameterRequest;

class ParameterController extends Controller
{
    protected $parameterService;

    public function __construct(ParameterService $parameterService)
    {
        $this->parameterService = $parameterService;
    }
    public function getManageParameterOptions()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->parameterService->getManageParameterOptions(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function getDefaultParameters(ParameterRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->parameterService->getDefaultParameters($request->validated()),
            ],
            JsonResponse::HTTP_OK,
        );
    }
    public function updateDefaultParameter(ParameterRequest $request, $parameterId)
    {
        $this->parameterService->updateDefaultParameter($parameterId, $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => 'Parameter updated',
            ],
            JsonResponse::HTTP_OK,
        );
    }
    public function getParameters(ParameterRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->parameterService->getParameters($request->validated()),
            ],
            JsonResponse::HTTP_OK,
        );
    }
    public function updateParameter(ParameterRequest $request, $parameterId)
    {
        $this->parameterService->updateParameter($parameterId, $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => 'Parameter updated',
            ],
            JsonResponse::HTTP_OK,
        );
    }
    public function getSectorParameters(ParameterRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->parameterService->getSectorParameters($request->validated()['sector_id']),
            ],
            JsonResponse::HTTP_OK,
        );
    }
    public function updateSectorParameter(ParameterRequest $request, $parameterId)
    {
        $this->parameterService->updateSectorParameter($parameterId, $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => 'Parameter updated',
            ],
            JsonResponse::HTTP_OK,
        );
    }
}
