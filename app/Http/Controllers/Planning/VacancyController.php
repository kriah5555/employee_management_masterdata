<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\Planning\VacancyRequest;
use App\Http\Requests\Planning\VacancyEmployeeRequest;
use App\Models\Planning\Vacancy;
use App\Services\Planning\VacancyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    public function __construct(protected VacancyService $vacancyService)
    {
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $companyId = $request->header('company-id');
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->vacancyService->vacancyOptions($companyId)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->vacancyService->getVacancies($filters)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
     * Undocumented function
     *
     * @param  \App\Http\Requests\Planning\VacancyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(VacancyRequest $request)
    {
        try {
            $this->vacancyService->createVacancies($request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Vacancies created successfully')
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (\Exception $e) {
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $vacancy)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->vacancyService->getVacancyById($vacancy)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VacancyRequest $request, Vacancy $vacancy)
    {
        try {
            $inputData = $request->validated();
            $vacancy->update($request->except('functions', 'employeeTypes'));

            if ($request->has('functions')) {
                $vacancy->functions()->sync($inputData['functions']);
            }

            if ($request->has('employeeTypes')) {
                $vacancy->employeeTypes()->sync($inputData['employeeTypes']);
            }

            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Vacancies updated successfully'),
                    'data'    => $vacancy
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function destroy($id)
    {
        try {
            $vacancy = Vacancy::findOrFail($id);
            $vacancy->delete();

            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Vacancies deleted'),
                    'data'    => $vacancy
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function applyVacancy(VacancyEmployeeRequest $vacancyEmployeeRequest)
    {
        $data = $vacancyEmployeeRequest->validated();
        try {
            if (empty($data['company_id']) || !connectCompanyDataBase($data['company_id'])) {
                throw new \Exception('Company Id is missing.');
            }
            $data['user_id'] = getActiveUser()->id;
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Job applied successfully'),
                    'data'    => $this->vacancyService->applyVacancyService($data)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function respondToVacancy(VacancyEmployeeRequest $vacancyEmployeeRequest)
    {
        $data = $vacancyEmployeeRequest->validated();
        try {
            if (empty($data['id']) || empty($data['responded_by']) || empty($data['request_status'])) {
                throw new \Exception('Some data is vacancy application.');
            }
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Application status updated successfully'),
                    'data'    => $this->vacancyService->replyToVacancyService($data)
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function getEmployeeJobsOverview(Request $request)
    {
        $rules = [
            'user_id' => 'required|integer|exists:company_users,user_id'
        ];
        $messages = [
            'user_id.exists' => 'User id not linked with companies.'
        ];

        $data = $request->all();
        try {
            $data = $request->validate($rules, $messages);
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Employee vacancy overview successfully'),
                    'data'    => $this->vacancyService->getEmployeeOverviewService($data)
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString()
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
