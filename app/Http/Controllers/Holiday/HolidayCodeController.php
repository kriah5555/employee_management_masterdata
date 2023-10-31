<?php

namespace App\Http\Controllers\Holiday;

use App\Models\Holiday\HolidayCode;
use App\Services\Holiday\HolidayCodeService;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\Holiday\HolidayCodeRequest;
use App\Http\Controllers\Controller;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Services\CompanyService;

class HolidayCodeController extends Controller
{
    protected $holidayCodeService;

    protected $employeeTypeService;

    protected $companyService;

    public function __construct(HolidayCodeService $holidayCodeService, EmployeeTypeService $employeeTypeService, CompanyService $companyService)
    {
        $this->holidayCodeService = $holidayCodeService;
        $this->employeeTypeService = $employeeTypeService;
        $this->companyService = $companyService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holidayCodeService->getHolidayCodes()
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HolidayCodeRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holidayCodeService->createHolidayCode($request->validated()),
                'message' => 'Holiday code created successfully',
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holidayCodeService->getHolidayCodeDetails($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => [
                    'holiday_type'      => $this->holidayCodeService->getHolidayCodeTypeOptions(),
                    'count_type'        => $this->holidayCodeService->getHolidayCodeCountTypeOptions(),
                    'icon_type'         => $this->holidayCodeService->getHolidayCodeIconTypeOptions(),
                    'employee_category' => $this->employeeTypeService->getEmployeeCategoryOptions(),
                    'employee_types'    => $this->employeeTypeService->getEmployeeTypeOptions(),
                    'contract_type'     => $this->employeeTypeService->getEmployeeContractTypeOptions(),
                    'type'              => $this->holidayCodeService->getHolidayTypeOptions(),
                    'companies'         => $this->companyService->getCompanyOptions(),
                    'holiday_include_options' => $this->holidayCodeService->getCompanyLinkingOptions(),
                ],
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HolidayCodeRequest $request, HolidayCode $holidayCode)
    {
        $this->holidayCodeService->updateHolidayCode($holidayCode, $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => 'Holiday code updated successfully',
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HolidayCode $holiday_code)
    {
        $holiday_code->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Holiday code deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }
}