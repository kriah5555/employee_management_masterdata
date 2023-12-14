<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\Planning\StorePlanningRequest;
use App\Http\Requests\Planning\UpdatePlanningRequest;
use App\Models\Planning\PlanningBase as Planning;
use App\Services\Planning\PlanningCreateEditService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class PlanningCreateEditController extends Controller
{
    public function __construct(protected PlanningCreateEditService $planningCreateEditService)
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
        try {
            $rules = [
                'employee_id' => 'required|integer',
                'date'        => 'required|date',
            ];

            $customMessages = [
                'employee_id.required' => 'Please select employee',
                'employee_id.integer'  => 'Employee ID must be an integer.',
                'date.integer'         => 'Date required.',
                'date.date'            => 'Incorrect date format.',
            ];

            $validator = Validator::make(request()->all(), $rules, $customMessages);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->planningCreateEditService->getEmployeePlanningCreateOptions($validator->validated()),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function savePlans(StorePlanningRequest $request)
    {
        try {
            $this->planningCreateEditService->savePlans($request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => "Plans saved",
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Planning $planning)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Planning $planning)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanningRequest $request, Planning $planning)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Planning $planning)
    {
        //
    }
}
