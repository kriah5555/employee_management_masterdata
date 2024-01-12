<?php

namespace App\Http\Controllers\Dimona;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Dimona\DimonaService;
use App\Http\Requests\Dimona\EmployeeTypeDimoanConfigurationRequest;

class EmployeeTypeDimoanConfigurationController extends Controller
{
    public function __construct(
        protected DimonaService $dimonaService
    )
    {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $company_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->dimonaService->getDimaonStatusForCompany($company_id)
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeTypeDimoanConfigurationRequest $request, string $company_id)
    {
        try {
            $this->dimonaService->updateEmpTypeDimoanConfigToCompany($company_id, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Employee type Dimona config updated successfully.'),
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
