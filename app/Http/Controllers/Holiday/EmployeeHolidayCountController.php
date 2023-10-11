<?php

namespace App\Http\Controllers\Holiday;

use App\Services\Holiday\EmployeeHolidayCountService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\Holiday\EmployeeHolidayCountRequest;

class EmployeeHolidayCountController extends Controller
{
    public function __construct(protected EmployeeHolidayCountService $employeeHolidayCountService)
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeHolidayCountRequest $request)
    {
        $this->employeeHolidayCountService->create($request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => t('Employee holiday count updated successfully'),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($employee_holiday_count_id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeHolidayCountService->getEmployeeCountHistory($employee_holiday_count_id),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($employee_id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeHolidayCountService->getOptionsToEdit($employee_id)
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeHolidayCountRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
