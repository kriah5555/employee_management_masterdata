<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimeRegistrationRequest;
use App\Http\Requests\UpdateTimeRegistrationRequest;
use App\Models\TimeRegistration;

class TimeRegistrationController extends Controller
{
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
    public function store(StoreTimeRegistrationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeRegistration $timeRegistration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeRegistration $timeRegistration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimeRegistrationRequest $request, TimeRegistration $timeRegistration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeRegistration $timeRegistration)
    {
        //
    }

    public function startPlan(TimeRegistration $timeRegistration)
    {

    }

    public function stopPlan(UpdateTimeRegistrationRequest $request, TimeRegistration $timeRegistration)
    {

    }

    public function scanQrStartPlan()
    {

    }

    public function scanQrStopPlan()
    {

    }

    public function startBreak()
    {

    }

    public function stopBreak()
    {

    }
}
