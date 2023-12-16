<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\Planning\VacancyRequest;
use App\Models\Planning\Vacancy;
use App\Services\Planning\VacancyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    public function __construct(protected VacancyService $vacancyService) {}
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
                    'message' => t('Vacancies options'),
                    'data'    => $this->vacancyService->vacancyOptions($companyId)
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
                    'message' => t('Vacancies created successfully'),
                    'data'    => $this->vacancyService->getVacancies($filters)
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
     * Undocumented function
     *
     * @param  \App\Http\Requests\Planning\VacancyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(VacancyRequest $request)
    {
        $data = $request->validated();
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Vacancies created successfully'),
                    'data'    => $this->vacancyService->createVacancies($data)
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
    public function show($id)
    {
        $vacancy = Vacancy::with('location', 'functions', 'employeeTypes')->findOrFail($id);

        return response()->json($vacancy, 200);
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
        // return response()->json($vacancy, 200);
    }

    public function destroy($id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $vacancy->delete();
        return response()->json(null, 204);
    }
}
