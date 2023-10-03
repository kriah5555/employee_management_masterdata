<?php

namespace App\Http\Controllers\Holiday;

use App\Models\HolidayCodesOfSocialSecretary;
use App\Services\Holiday\HolidayCodesOfSocialSecretaryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\Holiday\HolidayCodesOfSocialSecretaryRequest;

class HolidayCodesOfSocialSecretaryController extends Controller
{
    public function __construct(protected HolidayCodesOfSocialSecretaryService $holidayCodesOfSocialSecretaryService)
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
    public function store(HolidayCodesOfSocialSecretaryRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holidayCodesOfSocialSecretaryService->create($request->validated()),
                'message' => 'Social secretary codes updated successfully',
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(HolidayCodesOfSocialSecretary $holidayCodesOfSocialSecretary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($social_secretary_id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->holidayCodesOfSocialSecretaryService->getOptionsToEdit($social_secretary_id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HolidayCodesOfSocialSecretary $holidayCodesOfSocialSecretary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HolidayCodesOfSocialSecretary $holidayCodesOfSocialSecretary)
    {
        //
    }
}
