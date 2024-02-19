<?php

namespace App\Services\Holiday;

use Illuminate\Support\Facades\DB;
use App\Models\Holiday\HolidayCode;
use App\Models\Company\Company;
use App\Repositories\Holiday\HolidayCodeRepository;

class HolidayCodeService
{
    public function __construct(
        protected HolidayCodeRepository $holidayCodeRepository
    ) {
    }

    public function getHolidayCodes()
    {
        return $this->holidayCodeRepository->get();
    }

    public function find($id, $with = [])
    {
        return $this->holidayCodeRepository->find($id, $with);
    }

    public function getCompanyHolidayCodes($company_id) # will return all holiday codes
    {
        return HolidayCode::whereHas('companies', function ($query) use ($company_id) {
            $query->where('company_id', $company_id);
        })
            ->where('status', true)
            ->where('type', config('absence.HOLIDAY'))
            ->get();
    }

    public function getCompanyLeaveCodes($company_id) # will return all holiday codes
    {
        return HolidayCode::whereHas('companies', function ($query) use ($company_id) {
            $query->where('company_id', $company_id);
        })
            ->where('status', true)
            ->where('type', config('absence.LEAVE'))
            ->get();
    }
    public function getCompanyLeaveCodesTest($company_id) # will return all holiday codes
    {
        $conditions = [
            'status' => true,
            'type'   => config('absence.LEAVE')
        ];
        $has = [
            'companies' => function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            }
        ];
        // return $this->holidayCodeRepository->getHolidayCodes();
        return $this->holidayCodeRepository->getByConditions($conditions, [], $has);
    }

    public function getHolidayCodeDetails($id)
    {
        $holidayCode = $this->holidayCodeRepository->getHolidayCodeById($id);
        $holidayCode->holiday_type = [
            'value' => $holidayCode->holiday_type,
            'label' => config('absence.HOLIDAY_TYPE_OPTIONS')[$holidayCode->holiday_type]
        ];
        $holidayCode->count_type = [
            'value' => $holidayCode->count_type,
            'label' => config('absence.HOLIDAY_COUNT_TYPE_OPTIONS')[$holidayCode->count_type]
        ];
        $holidayCode->employee_category = array_map(function ($employeeCategory) {
            return [
                'value' => $employeeCategory,
                'label' => config('absence.HOLIDAY_EMPLOYEE_CATEGORY_OPTIONS')[$employeeCategory] ?? null,
            ];
        }, json_decode($holidayCode->employee_category));
        $holidayCode->icon_type = [
            'value' => $holidayCode->icon_type,
            'label' => config('absence.HOLIDAY_ICON_TYPE_OPTIONS')[$holidayCode->icon_type]
        ];
        $holidayCode->contract_type = [
            'value' => $holidayCode->contract_type,
            'label' => config('absence.HOLIDAY_CONTRACT_TYPE_OPTIONS')[$holidayCode->contract_type]
        ];
        $holidayCode->type = [
            'value' => $holidayCode->type,
            'label' => config('absence.HOLIDAY_OPTIONS')[$holidayCode->type]
        ];
        return $holidayCode;
        // return $this->holidayCodeRepository->getHolidayCodeById($id);
    }

    public function getHolidayCodeTypeOptions()
    {
        return getValueLabelOptionsFromConfig('absence.HOLIDAY_TYPE_OPTIONS');
    }

    public function getHolidayCodeCountTypeOptions()
    {
        return getValueLabelOptionsFromConfig('absence.HOLIDAY_COUNT_TYPE_OPTIONS');
    }

    public function getHolidayCodeIconTypeOptions()
    {
        return getValueLabelOptionsFromConfig('absence.HOLIDAY_ICON_TYPE_OPTIONS');
    }

    public function getHolidayTypeOptions()
    {
        return getValueLabelOptionsFromConfig('absence.HOLIDAY_OPTIONS');
    }

    public function getCompanyLinkingOptions()
    {
        return getValueLabelOptionsFromConfig('absence.HOLIDAY_INCLUDE_OPTIONS');
    }

    public function createHolidayCode($values)
    {
        return $this->holidayCodeRepository->create($values);
    }

    public function updateHolidayCode($holidayCode, $values)
    {
        return DB::transaction(function () use ($holidayCode, $values) {
            $values['employee_category'] = json_encode($values['employee_category']); // Encode the employee_category array as JSON before saving
            $this->holidayCodeRepository->update($holidayCode, $values);
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
                    'holiday_code_id'   => $holidayCode->id,
                    'holiday_code_name' => $holidayCode->holiday_code_name,
                    'status'            => in_array($holidayCode->id, $linkedHolidayCodesIds),
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
