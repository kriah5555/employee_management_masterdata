<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;
use App\Http\Rules\SectorRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Services\CommonServices;
use Illuminate\Support\Facades\App;

class SectorController extends Controller
{
    protected $api_service;

    public function __construct()
    {
        $this->api_service = App::make(CommonServices::class);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->api_service->api_response(200, 'Sectors received successfully', Sector::all());
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
            return $this->api_service->api_response(201, 'Sector created successfully', $sector);
        } catch (Exception $e) {
            return $this->api_service->api_response(400, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sector $sector)
    {
        return $this->api_service->api_response(200, 'Sector received successfully', $sector);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SectorRequest $request, Sector $sector)
    {
        try {
            if (array_key_exists('employee_types', $request->validated())) {
                $employee_types = $request->validated()['employee_types'];
            } else {
                $employee_types = [];
            }
            $sector->update($request->validated());
            $employee_types = $request->validated()['employee_types'];
            $sector->employeeTypes()->sync($employee_types);
            $sector->refresh();
            return $this->api_service->api_response(202, 'Sector updated successfully', $sector);
        } catch (Exception $e) {
            return $this->api_service->api_response(400, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        $sector->delete();
        return $this->api_service->api_response(204, 'Sector deleted');
    }
}
