<?php

namespace App\Http\Controllers;

use App\Models\Reason;
use App\Services\ReasonService;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\ReasonRequest;

class ReasonController extends Controller
{
    public function __construct(protected ReasonService $reasonService)
    {
    }

    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->reasonService->getAll(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function store(ReasonRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => t('Reason created successfully'),
                'data'    => $this->reasonService->create($request->validated()),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    public function show(Reason $reason)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $reason,
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    public function edit(Reason $reason)
    {
        return returnResponse(
            [
                'success' => true,
                // 'data'    => $this->contractTypeService->edit($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function update(ReasonRequest $request, Reason $reason)
    {
        $this->reasonService->update($reason, $request->validated());

        $reason->update($request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => t('Reason updated successfully'),
                'data'    => $reason,
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function destroy(Reason $reason)
    {
        $reason->delete();
        return response()->json([
            'success' => true,
            'message' => t('Reason deleted successfully')
        ]);
    }
}
