<?php

namespace App\Services\Holiday;

use Illuminate\Support\Facades\DB;
use App\Models\Holiday\HolidayCode;
use App\Models\Company;
use App\Repositories\Holiday\HolidayCodeRepository;

class HolidayCodeService
{
    protected $holidayCodeRepository;

    protected $companyService;

    public function __construct(HolidayCodeRepository $holidayCodeRepository)
    {
        $this->holidayCodeRepository = $holidayCodeRepository;
    }

    public function getHolidayCodes()
    {
        return $this->holidayCodeRepository->getHolidayCodes();
    }
    public function getHolidayCodeDetails($id)
    {
        $holidayCode = $this->holidayCodeRepository->getHolidayCodeById($id);
        $holidayCode->holiday_type = [
            'value' => $holidayCode->holiday_type,
            'label' => config('constants.HOLIDAY_TYPE_OPTIONS')[$holidayCode->holiday_type]
        ];
        $holidayCode->count_type = [
            'value' => $holidayCode->count_type,
            'label' => config('constants.HOLIDAY_COUNT_TYPE_OPTIONS')[$holidayCode->count_type]
        ];
        $holidayCode->employee_category = array_map(function ($employeeCategory) {
            return [
                'value' => $employeeCategory,
                'label' => config('constants.HOLIDAY_EMPLOYEE_CATEGORY_OPTIONS')[$employeeCategory] ?? null,
            ];
        }, json_decode($holidayCode->employee_category));
        $holidayCode->icon_type = [
            'value' => $holidayCode->icon_type,
            'label' => config('constants.HOLIDAY_ICON_TYPE_OPTIONS')[$holidayCode->icon_type]
        ];
        $holidayCode->contract_type = [
            'value' => $holidayCode->contract_type,
            'label' => config('constants.HOLIDAY_CONTRACT_TYPE_OPTIONS')[$holidayCode->contract_type]
        ];
        $holidayCode->type = [
            'value' => $holidayCode->type,
            'label' => config('constants.HOLIDAY_OPTIONS')[$holidayCode->type]
        ];
        return $holidayCode;
        // return $this->holidayCodeRepository->getHolidayCodeById($id);
    }
    
    public function getHolidayCodeTypeOptions()
    {
        return getValueLabelOptionsFromConfig('constants.HOLIDAY_TYPE_OPTIONS');
    }
    public function getHolidayCodeCountTypeOptions()
    {
        return getValueLabelOptionsFromConfig('constants.HOLIDAY_COUNT_TYPE_OPTIONS');
    }
    public function getHolidayCodeIconTypeOptions()
    {
        return getValueLabelOptionsFromConfig('constants.HOLIDAY_ICON_TYPE_OPTIONS');
    }

    public function getHolidayTypeOptions()
    {
        return getValueLabelOptionsFromConfig('constants.HOLIDAY_OPTIONS');
    }

    public function getCompanyLinkingOptions()
    {
        return getValueLabelOptionsFromConfig('constants.HOLIDAY_INCLUDE_OPTIONS');
    }

    public function createHolidayCode($values)
    {
        return DB::transaction(function () use ($values) {
            $values['employee_category'] = json_encode($values['employee_category']); // Encode the employee_category array as JSON before saving
            $holidayCode = $this->holidayCodeRepository->createHolidayCode($values);
            $employee_types = $values['employee_types'] ?? [];
            $holidayCode->employeeTypes()->sync($employee_types);
            $holidayCode->linkCompanies($values['link_companies'], $values['companies'] ?? []);
            return $holidayCode;
        });
    }

    public function updateHolidayCode($holidayCode, $values)
    {
        return DB::transaction(function () use ($holidayCode, $values) {
            $values['employee_category'] = json_encode($values['employee_category']); // Encode the employee_category array as JSON before saving
            $this->holidayCodeRepository->updateHolidayCode($holidayCode, $values);
            $employee_types = $values['employee_types'] ?? [];
            $holidayCode->employeeTypes()->sync($employee_types);
            return $holidayCode;
        });
    }
    public function getHolidayCodesWithStatusForCompany($company_id)
    {
        try {
            // Get all holiday codes
            $allHolidayCodes = HolidayCode::where('status', true)->get();

            // Get the IDs of holiday codes linked to the company
            $linkedHolidayCodesIds = $this->getAllHolidayCodesLinkedToCompany($company_id);

            // Format the holiday codes with their status
            $formattedHolidayCodes = $allHolidayCodes->map(function ($holidayCode) use ($linkedHolidayCodesIds) {
                return [
                    'value'  => $holidayCode->id,
                    'label'  => $holidayCode->holiday_code_name,
                    'status' => in_array($holidayCode->id, $linkedHolidayCodesIds),
                ];
            });

            return $formattedHolidayCodes->toArray();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAllHolidayCodesLinkedToCompany($company_id)
    {
        $company = Company::findOrFail($company_id);
        return $company->holidayCodes()->pluck('holiday_codes.id')->toArray();
    }

    public function updateHolidayCodesToCompany($company_id, $values)
    {
        try {
            DB::beginTransaction();

            $company = Company::findOrFail($company_id);
            $holiday_code_ids = $values['holiday_code_ids'] ?? [];

            // Sync the holiday codes to the company
            $company->holidayCodes()->sync($holiday_code_ids);

            // Refresh the company model to ensure it reflects the updated holiday codes
            $company->refresh();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}