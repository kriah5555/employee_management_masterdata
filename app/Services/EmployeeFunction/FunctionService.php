<?php

namespace App\Services\EmployeeFunction;

use Illuminate\Support\Facades\DB;
use App\Services\Sector\SectorService;
use App\Models\EmployeeFunction\FunctionCategory;
use App\Models\EmployeeFunction\FunctionTitle;
use Exception;

class FunctionService
{
    protected $sectorService;

    public function __construct(SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }

    public function createFunctionCategory()
    {
        return [
            'sectors'    => $this->sectorService->getSectorOptions(),
            'categories' => $this->sectorService->getCategoriesForSector(),
        ];
    }

    public function showFunctionCategory($id)
    {
        return FunctionCategory::with([
            'sector',
        ])->findOrFail($id);
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

    public function showFunctionTitle($id)
    {
        return FunctionTitle::with([
            'functionCategory',
        ])->findOrFail($id);
    }

    public function indexFunctionCategories()
    {
        return FunctionCategory::all();
    }

    public function storeFunctionCategories($values)
    {
        try {
            return DB::transaction(function () use ($values) {
                return FunctionCategory::create($values);
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function editFunctionCategory($id)
    {
        $options = $this->createFunctionCategory();
        $functionCategory = FunctionCategory::findOrFail($id);
        $functionCategory->sectorValue;
        $options['details'] = $functionCategory;
        return $options;
    }


    public function updateFunctionCategories(FunctionCategory $functionCategory, $values)
    {
        try {
            DB::beginTransaction();
            $functionCategory->update($values);
            DB::commit();
            return $functionCategory;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function indexFunctionTitles()
    {
        return FunctionTitle::all();
    }

    public function storeFunctionTitle($values)
    {
        try {
            return DB::transaction(function () use ($values) {
                return FunctionTitle::create($values);
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function editFunctionTitle($id)
    {
        $options = $this->createFunctionTitle();
        $functionCategory = FunctionTitle::findOrFail($id);
        $functionCategory->functionCategoryValue;
        $options['details'] = $functionCategory;
        return $options;
    }

    public function updateFunctionTitle(FunctionTitle $functionTitle, $values)
    {
        try {
            DB::beginTransaction();
            $functionTitle->update($values);
            DB::commit();
            return $functionTitle;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}