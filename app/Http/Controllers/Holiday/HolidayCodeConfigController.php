<?php

namespace App\Http\Controllers\Holiday;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Holiday\HolidayCodeService;
use App\Http\Requests\Holiday\HolidayCodeConfigRequest;
use App\Models\Company\Company;
use Illuminate\Http\JsonResponse;
use App\Services\CompanyService;

class HolidayCodeConfigController extends Controller
{
    protected $company_service;

    public function __construct(protected HolidayCodeService $holiday_code_service)
    {
        $this->company_service = app(CompanyService::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holiday_code_service->getHolidayCodesWithStatusForCompany(getCompanyId())
            ],
            JsonResponse::HTTP_OK,
        );
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
    public function store(HolidayCodeConfigRequest $request)
    {
        $this->holiday_code_service->updateHolidayCodesToCompany(getCompanyId(), $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => t('Holiday code config updated successfully'),
            ],
            JsonResponse::HTTP_OK,
        );
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
    public function edit()
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
