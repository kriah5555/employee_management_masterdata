<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use App\Http\Rules\SectorRequest;
use Illuminate\Http\JsonResponse;
class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return api_response(true, 'Sectors received successfully', Sector::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SectorRequest $request)
    {
        try {
            $sector = Sector::create($request->validated());
            $employee_types = $request->validated()['employee_types'];
            $sector->employeeTypes()->sync($employee_types);
            $sector->refresh();
            return api_response(true, 'Sector created successfully', $sector, 201);
        } catch (Exception $e) {
            return api_response(false, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sector $sector)
    {
        if (!$sector) {
            return api_response(false, 'Sector not found', '', 404);
        }
        return api_response(true, 'Sector received successfully', $sector, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SectorRequest $request, Sector $sector)
    {
        try {
            if (!$sector) {
                return api_response(404, 'Sector not found', '', 404);
            }
            if (array_key_exists('employee_types', $request->validated())) {
                $employee_types = $request->validated()['employee_types'];
            } else {
                $employee_types = [];
            }
            $sector->update($request->validated());
            $employee_types = $request->validated()['employee_types'];
            $sector->employeeTypes()->sync($employee_types);
            $sector->refresh();
            return api_response(false, 'Sector updated successfully', $sector, 202);
        } catch (Exception $e) {
            return api_response(false, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        if (!$sector) {
            return api_response(false, 'Sector not found', '', 404);
        }
        $sector->delete();
        return api_response(true, 'Sector deleted', '', 204);
    }
}
