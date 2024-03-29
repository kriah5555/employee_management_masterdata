<?php

namespace App\Services\EmployeeFunction;

use Illuminate\Support\Facades\DB;
use App\Models\EmployeeFunction\FunctionCategory;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Models\Company\Company;
use Exception;
use App\Repositories\EmployeeFunction\FunctionCategoryRepository;
use App\Repositories\EmployeeFunction\FunctionTitleRepository;

class FunctionService
{
    protected $functionCategoryRepository;
    protected $functionTitleRepository;
    public function __construct(FunctionCategoryRepository $functionCategoryRepository, FunctionTitleRepository $functionTitleRepository)
    {
        $this->functionCategoryRepository = $functionCategoryRepository;
        $this->functionTitleRepository = $functionTitleRepository;
    }
    public function getFunctionCategories()
    {
        return $this->functionCategoryRepository->getFunctionCategories();
    }

    public function createFunctionCategory($values)
    {
        return $this->functionCategoryRepository->createFunctionCategory($values);
    }

    public function getFunctionCategoryDetails($id)
    {
        return $this->functionCategoryRepository->getFunctionCategoryById($id, ['sector']);
    }

    public function updateFunctionCategory(FunctionCategory $functionCategory, $values)
    {
        return $this->functionCategoryRepository->updateFunctionCategory($functionCategory, $values);
    }
    public function deleteFunctionCategory(FunctionCategory $functionCategory)
    {
        return $this->functionCategoryRepository->deleteFunctionCategory($functionCategory);
    }

    public function getFunctionTitles()
    {
        return $this->functionTitleRepository->getFunctionTitles();
    }

    public function storeFunctionTitle($values)
    {
        return $this->functionTitleRepository->createFunctionTitle($values);
    }

    public function updateFunctionTitle(FunctionTitle $functionTitle, $values)
    {
        return $this->functionTitleRepository->updateFunctionTitle($functionTitle, $values);
    }

    public function getCompanyFunctionTitles($company_id)
    {
        return Company::with([
            'sectors' => function ($query) {
                $query->where('status', true);
                $query->with([
                    'functionCategories' => function ($query) {
                        $query->where('status', true);
                        $query->with('functionTitles');
                    },
                ]);
            },
        ])->find($company_id)
            ->sectors
            ->flatMap(function ($sector) {
                return $sector->functionCategories->flatMap(function ($functionCategory) {
                    return $functionCategory->functionTitles;
                });
            })
            ->toArray();
    }

    public function getCompanyFunctionTitlesOptions($company_id)
    {
        return $functionTitles = self::getCompanyFunctionTitles($company_id);

        // return array_map(function ($title) {
        //     return [
        //         'value' => $title['id'],
        //         'label' => $title['name'],
        //     ];
        // }, $functionTitles);
    }

    public function createFunctionTitle()
    {
        return [
            'function_categories' => $this->getFunctionCategoryOptions()
        ];
    }

    public function getFunctionCategoryOptions()
    {
        return FunctionCategory::where('status', '=', true)
            ->select('id as value', 'name as label')
            ->get();
    }

    public function getFunctionTitleDetails($id)
    {
        return $this->functionTitleRepository->getFunctionTitleById($id, ['functionCategory']);
    }
    public function deleteFunctionTitle($functionTitle)
    {
        return $this->functionTitleRepository->deleteFunctionTitle($functionTitle);
    }
    public function getActiveFunctionCategories()
    {
        return $this->functionCategoryRepository->getActiveFunctionCategories();
    }

    public function getFunctionTitlesLinkedToSectors(array $sector_Ids)
    {
        $function_titles  = FunctionCategory::with(['functionTitles'])->whereIn('sector_id', $sector_Ids)->get()->pluck('functionTitles');
        $function_titles->toArray();
        $return_data = [];
        foreach ($function_titles as $values) {
            foreach ($values as $value) {
                $return_data[$value['id']] = $value;
            }
        }
        return array_values($return_data);
    }
}