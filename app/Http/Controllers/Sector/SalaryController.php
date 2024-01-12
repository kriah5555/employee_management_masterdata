<?php

namespace App\Http\Controllers\Sector;

use App\Services\Sector\SectorSalaryService;
use App\Services\Sector\SectorService;
use App\Http\Requests\UpdateMinimumSalariesRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\BelgiumCurrencyFormatRule;
use Illuminate\Validation\Rule;

class SalaryController extends Controller
{
    public function __construct(protected SectorSalaryService $sectorSalaryService, protected SectorService $sectorService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function getMinimumSalaries($sector_id)
    {
        try {
            $salary_type = $this->getSalaryTypeFromPath(request()->getPathInfo());
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->sectorSalaryService->getMinimumSalariesBySectorId($sector_id, $salary_type),
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

    private function getSalaryTypeFromPath($path)
    {
        if (str_contains($path, "/monthly-minimum-salaries/")) {
            return config('constants.MONTHLY_SALARY');
        } elseif (str_contains($path, "/hourly-minimum-salaries/")) {
            return config('constants.HOURLY_SALARY');
        }

        return ''; // Default if no match is found
    }

    /**
     * Display a listing of the resource.
     */
    public function updateMinimumSalaries(UpdateMinimumSalariesRequest $request, $id)
    {
        try {
            $salary_type = $this->getSalaryTypeFromPath($request->getPathInfo());
            $this->sectorSalaryService->updateMinimumSalaries($id, $request->validated()['salaries'], $salary_type);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Minimum salaries updated'
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

    public function salaryIncrementCalculation(Request $request)
    {
        try {
            $rules = [
                'sector_id'   => [
                    'required',
                    'integer',
                    Rule::exists('sectors', 'id'),
                ],
                'coefficient' => [
                    'required',
                    new BelgiumCurrencyFormatRule,
                ],
                'type'        => [
                    'required',
                    'integer',
                    'in:1,2'
                ],
            ];
            $validator = Validator::make(request()->all(), $rules);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }
            $values = $validator->validated();
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->sectorSalaryService->salaryIncrementCalculation($values)
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

    public function undoIncrementedMinimumSalaries($sector_id)
    {
        try {
            $salary_type = $this->getSalaryTypeFromPath(request()->getPathInfo());
            $status = $this->sectorSalaryService->undoIncrementedMinimumSalaries($sector_id, $salary_type);

            if ($status == 'success' || empty($status)) {
                $message = t('Minimum salaries reverted successfully');
            } else {
                $message = $status;
            }
            return returnResponse(
                [
                    'success' => true,
                    'message' => $message
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
}
