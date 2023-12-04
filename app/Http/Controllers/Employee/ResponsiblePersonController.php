<?php

namespace App\Http\Controllers\Employee;

use App\Http\Requests\Employee\ResponsiblePersonRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Employee\ResponsiblePersonService;

class ResponsiblePersonController extends Controller
{
    public function __construct(protected ResponsiblePersonService $responsible_person_service)
    {

    }
    
    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->responsible_person_service->getCompanyResponsiblePersons(getCompanyId()),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
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
    public function store(ResponsiblePersonRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->responsible_person_service->createResponsiblePerson($request->validated(), getCompanyId()),
                    'message' => 'Responsible person created successfully',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $user_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->responsible_person_service->getResponsiblePersonById($user_id, getCompanyId()),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ResponsiblePersonRequest $request, string $user_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->responsible_person_service->updateResponsiblePerson($user_id, $request->validated(), getCompanyId()),
                    'message' => 'Responsible person updated successfully',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $user_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->responsible_person_service->deleteResponsiblePerson($user_id, getCompanyId()),
                    'message' => 'Responsible person updated successfully',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
