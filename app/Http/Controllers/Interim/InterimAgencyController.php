<?php

namespace App\Http\Controllers\Interim;

use App\Models\Interim\InterimAgency;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\Interim\InterimAgencyRequest;
use App\Services\Interim\InterimAgencyService;
use App\Http\Controllers\Controller;
class InterimAgencyController extends Controller
{
    public function __construct(protected InterimAgencyService $interim_agency_service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->interim_agency_service->getAll(['address']),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->interim_agency_service->getOptionsToCreate(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InterimAgencyRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Interim agency created successfully',
                'data'    => $this->interim_agency_service->create($request->validated()),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(InterimAgency $interimAgency)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $interimAgency,
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($interim_agency_id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->interim_agency_service->getOptionsToEdit($interim_agency_id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InterimAgencyRequest $request, InterimAgency $interimAgency)
    {
        $this->interim_agency_service->update($interimAgency, $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => 'Interim agency updated successfully',
                'data'    => $this->interim_agency_service->getOptionsToEdit($interim_agency_id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InterimAgency $interimAgency)
    {
        $interimAgency->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Interim agency deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }
}
