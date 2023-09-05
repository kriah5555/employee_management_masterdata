<?php

namespace App\Http\Controllers\Sector;

use App\Models\Sector\Sector;
use App\Http\Rules\Sector\SectorRequest;
use App\Services\SectorService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SectorController extends Controller
{
    public function __constructprotected (SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->sectorService->index(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->sectorService->create()
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SectorRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Sector created successfully',
                'data'    => $this->sectorService->store($request->validated())
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->sectorService->show($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function edit($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->sectorService->edit($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SectorRequest $request, Sector $sector)
    {
        $this->sectorService->update($sector, $request->validated());
        $sector->refresh();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Sector updated successfully',
                'data'    => $sector,
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        $sector->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Sector deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }
}