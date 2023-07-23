<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;
use App\Http\Requests\SectorRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Sector::all();
        return response()->json($data);
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
            $data = [
                'message' => 'Sector created successfully',
                'data' => $sector,
            ];
            return response()->json($data);
        } catch (Exception $e) {
            $data = [
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ];
            return response()->json($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sector $sector)
    {
        return response()->json($sector);
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
            $data = [
                'message' => 'Sector updated successfully',
                'data' => $sector,
            ];
            return response()->json($data);
        } catch (Exception $e) {
            $data = [
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ];
            return response()->json($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        $sector->delete();
        $data = [
            'message' => 'Sector deleted'
        ];
        return response()->json($data);
    }
}
