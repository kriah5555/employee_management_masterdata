<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Employee\EmployeeService;
use App\Services\Employee\EmployeeIdCardService;

class EmployeeIdCardController extends Controller
{
    public function __construct(
        protected EmployeeIdCardService $employeeIdCardService
    )
    {
        
    }
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
        try {

            $rules = [
                'employee_profile_id' => [
                    'required',
                    'integer',
                    Rule::exists('tenant.employee_profiles', 'id'),
                ],
                'id_card_front' => 'nullable',
                'id_card_back'  => 'nullable',
            ];
            
            $validator = Validator::make($request->all(), $rules, []);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }
            $this->employeeIdCardService->saveEmployeeIdCard($request->all());

            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Employee Id card added successfully.')
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
     * Display the specified resource.
     */
    public function show(EmployeeIdCard $employeeIdCard)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeIdCard $employeeIdCard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeeIdCard $employeeIdCard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeIdCard $employeeIdCard)
    {
        //
    }
}
