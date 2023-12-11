<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventDetailsRequest;
use App\Http\Requests\UpdateEventDetailsRequest;
use App\Models\EventDetails;

class EventDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventDetailsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EventDetails $eventDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventDetailsRequest $request, EventDetails $eventDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventDetails $eventDetails)
    {
        //
    }
}
