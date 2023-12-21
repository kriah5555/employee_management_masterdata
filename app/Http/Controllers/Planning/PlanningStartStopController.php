<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\Planning\StorePlanningRequest;
use App\Http\Requests\Planning\UpdatePlanningRequest;
use App\Models\Planning\PlanningBase as Planning;
use App\Services\Planning\PlanningService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Http\Requests\Planning\StartPlanByManagerRequest;
use App\Services\Planning\PlanningStartStopService;

class PlanningStartStopController extends Controller
{
    public function __construct(
        protected PlanningService $planningService,
        protected PlanningStartStopService $planningStartStopService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function startPlanByManager(StartPlanByManagerRequest $request)
    {
        try {
            $this->planningStartStopService->startPlanByManager($request->validated());
            $companyId = $request->header('company-id');
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Planning options',
                    'data'    => $this->planningService->getPlanningOverviewFilterService($companyId),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'file'    => $e->getFile(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
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
    public function destroy($plan_id)
    {
        try {
            $planning = $this->planningService->getPlanningById($plan_id);
            $this->planningCreateEditService->deletePlan($planning);
            return returnResponse(
                [
                    'success' => true,
                    'message' => "Plan deleted",
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
    public function deleteWeekPlans()
    {
        try {
            $rules = [
                'employee_id'    => [
                    'required',
                    'integer',
                    Rule::exists('employee_profiles', 'id'),
                ],
                'location_id'    => [
                    'required',
                    'integer',
                    Rule::exists('locations', 'id'),
                ],
                'workstation_id' => [
                    'required',
                    'integer',
                    Rule::exists('workstations', 'id'),
                ],
                'week'           => 'required|min:1|max:53',
                'year'           => 'required|digits:4',
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
            $this->planningCreateEditService->deleteWeekPlans($validator->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => "Plan deleted",
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
}
