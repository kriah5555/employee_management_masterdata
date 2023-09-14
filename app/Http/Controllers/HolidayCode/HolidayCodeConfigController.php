<?php

namespace App\Http\Controllers\HolidayCode;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\HolidayCode\HolidayCodeService;
use App\Http\Rules\HolidayCode\HolidayCodeConfigRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class HolidayCodeConfigController extends Controller
{
    public function __construct(protected HolidayCodeService $holiday_code_service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HolidayCodeRequest $request, Company $company)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($company_id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holiday_code_service->getHolidayCodesWithStatusForCompany($company_id)
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HolidayCodeConfigRequest $request, $company_id)
    {
        $this->holiday_code_service->updateHolidayCodesToCompany($company_id, $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => t('Holiday code config updated successfully'),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
