<?php

namespace App\Http\Controllers\Holiday;

use App\Models\Holiday\HolidayCode;
use App\Services\Holiday\HolidayCodeService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Holiday\HolidayCodeRequest;
use App\Http\Controllers\Controller;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Services\CompanyService;
use App\Http\Resources\Absence\HolidayCodeResource;
use App\Http\Resources\Absence\HolidayCodeCollection;

class HolidayCodeController extends Controller
{
    public function __construct(
        protected HolidayCodeService $holidayCodeService,
        protected EmployeeTypeService $employeeTypeService,
        protected CompanyService $companyService
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => new HolidayCodeCollection($this->holidayCodeService->getHolidayCodes()),
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
                'data'    => new HolidayCodeResource($this->holidayCodeService->find($id))
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
                    'holiday_type'            => $this->holidayCodeService->getHolidayCodeTypeOptions(),
                    'count_type'              => $this->holidayCodeService->getHolidayCodeCountTypeOptions(),
                    'icon_type'               => $this->holidayCodeService->getHolidayCodeIconTypeOptions(),
                    'employee_category'       => $this->employeeTypeService->getEmployeeCategoryOptions(),
                    'contract_type'           => $this->employeeTypeService->getEmployeeContractTypeOptions(),
                    'type'                    => $this->holidayCodeService->getHolidayTypeOptions(),
                    'companies'               => collectionToValueLabelFormat($this->companyService->getCompanies(), 'id', 'company_name'),
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
