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

    public function getResponsiblePersonList()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => ['responsible_persons' => $this->responsible_person_service->getCompanyResponsiblePersonOptions(getCompanyId())],
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
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->responsible_person_service->getOptionsToCreateResponsiblePersons(),
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
    public function show(string $employeeProfileId)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->responsible_person_service->getResponsiblePersonDetails($employeeProfileId, getCompanyId()),
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
    public function update(ResponsiblePersonRequest $request, string $employeeProfileId)
    {
        try {
            $this->responsible_person_service->updateResponsiblePerson($employeeProfileId, $request->validated(), getCompanyId());
            return returnResponse(
                [
                    'success' => true,
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
                    'message' => 'Responsible person deleted successfully',
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
