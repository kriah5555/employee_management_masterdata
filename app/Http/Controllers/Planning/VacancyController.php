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

    public function filterVacancies(&$vacancies, $request)
    {
        // Filter by location
        if ($locationId = $request->get('location_id')) {
            $vacancies->where('location_id', $locationId);
        }

        // Filter by functions
        if ($functionIds = $request->get('functions')) {
            $vacancies->whereHas('functions', function ($query) use ($functionIds) {
                $query->whereIn('id', $functionIds);
            });
        }

        // Filter by employee types
        if ($employeeTypeIds = $request->get('employee_types')) {
            $vacancies->whereHas('employeeTypes', function ($query) use ($employeeTypeIds) {
                $query->whereIn('id', $employeeTypeIds);
            });
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $vacancies->where('status', $status);
        }

        // Filter by start date range
        if ($startDate = $request->get('start_date_from')) {
            $vacancies->where('start_date_time', '>=', $startDate);
        }

        if ($endDate = $request->get('start_date_to')) {
            $vacancies->where('start_date_time', '<=', $endDate);
        }

        // Filter by end date range
        if ($endDate = $request->get('end_date_from')) {
            $vacancies->where('end_date_time', '>=', $endDate);
        }

        if ($endDate = $request->get('end_date_to')) {
            $vacancies->where('end_date_time', '<=', $endDate);
        }
    }

    public function orderVacancies(&$vacancies, $request)
    {
        $orderBy = $request->get('order_by');
        $direction = $request->get('order_direction', 'asc');
    
        if ($orderBy) {
            $vacancies->orderBy($orderBy, $direction);
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
        $vacancies = Vacancy::with('location', 'workstations', 'employeeTypes.employeeType')->get();

        // Filter by parameters
        // $this->filterVacancies($vacancies, $request);

        // Order by
        // $this->orderVacancies($vacancies, $request);

        // Pagination
        // $vacancies = $vacancies->paginate($request->get('per_page') ?? 10);

        return response()->json($vacancies, 200);
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
        // $vacancy = Vacancy::findOrFail($id);

        // $validator = Validator::make($request->all(), );

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 400);
        // }



        return response()->json($vacancy, 200);
    }

    public function destroy($id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $vacancy->delete();
        return response()->json(null, 204);
    }
}
