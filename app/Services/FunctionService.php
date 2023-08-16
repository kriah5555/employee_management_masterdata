<?php

namespace App\Services;

use App\Models\Sector\Sector;
use Illuminate\Support\Facades\DB;
use App\Services\SectorService;
use App\Models\Function\FunctionCategory;
use App\Models\Function\FunctionTitle;

class FunctionService
{
    protected $sectorService;

    public function __construct(SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }

    public function getCreateFunctionCategoryOptions()
    {
        $options['sectors'] = $this->sectorService->getSectorOptions();
        $options['categories'] = $this->sectorService->getCategoriesForSector();
        return $options;
    }

    public function getFunctionCategoryDetails($id)
    {
        return FunctionCategory::with([
            'sector',
        ])->findOrFail($id);
    }

    public function getCreateFunctionTitleOptions()
    {
        $options['function_categories'] = $this->getFunctionCategoryOptions();
        return $options;
    }

    public function getFunctionCategoryOptions()
    {
        $options = [];
        $functionCategories = FunctionCategory::where('status', '=', true)->get();
        foreach ($functionCategories as $value) {
            $options[] = [
                'value' => $value['id'],
                'label' => $value['name'],
            ];
        }
        return $options;
    }

    public function getFunctionTitleDetails($id)
    {
        return FunctionTitle::with([
            'functionCategory',
        ])->findOrFail($id);
    }
}
