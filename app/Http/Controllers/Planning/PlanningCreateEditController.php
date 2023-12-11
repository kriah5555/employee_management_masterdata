<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanningRequest;
use App\Http\Requests\UpdatePlanningRequest;
use App\Models\PlanningBase as Planning;
use App\Services\PlanningCreateEditService;

class PlanningCreateEditController extends Controller
{
    public function __construct(protected PlanningCreateEditService $planningService)
    {}

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

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlanningRequest $request)
    {
        //
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
