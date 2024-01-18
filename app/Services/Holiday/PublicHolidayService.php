<?php

namespace App\Services\Holiday;

use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
use App\Models\Holiday\PublicHoliday;
use App\Services\CompanyService;

class PublicHolidayService extends BaseService
{
    protected $public_holiday;

    protected $company_service;

    public function __construct(PublicHoliday $public_holiday)
    {
        parent::__construct($public_holiday);
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

    public function getOptionsToEdit($public_holiday_id)
    {
        try {
            #$options = $this->getOptionsToCreate();
            $options = $this->get($public_holiday_id, ['companiesValue']);
            return $options;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function create($values)
    {
        try {
            $values['date'] = !empty($values['date']) ? date('Y-m-d', strtotime($values['date'])) : $values['date'];
            DB::beginTransaction();
                $public_holiday = $this->model::create($values);
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
            $values['date'] = !empty($values['date']) ? date('Y-m-d', strtotime($values['date'])) : $values['date'];
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
