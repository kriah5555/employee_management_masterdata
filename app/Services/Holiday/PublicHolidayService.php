<?php

namespace App\Services\Holiday;

use Illuminate\Support\Facades\DB;
use App\Models\Holiday\PublicHoliday;
use App\Services\CompanyService;

class PublicHolidayService
{
    protected $public_holiday;

    protected $company_service;

    public function __construct()
    {
        $this->company_service = app(CompanyService::class);
    }

    public function getOptionsToCreate()
    {
        try {
            return [
                'companies' => $this->company_service->getCompanies()
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function create($values)
    {
        try {
            DB::beginTransaction();
            $public_holiday = PublicHoliday::create($values);
            $public_holiday->companies()->sync($values['companies'] ?? []);
            DB::commit();
            return $public_holiday;
        } catch (\Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($public_holiday_model, $values)
    {
        try {
            DB::beginTransaction();
            $public_holiday_model->update($values);
            $public_holiday_model->companies()->sync($values['companies'] ?? []);
            DB::commit();
            return $public_holiday_model;
        } catch (\Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
