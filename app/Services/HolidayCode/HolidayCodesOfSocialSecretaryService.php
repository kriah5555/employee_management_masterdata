<?php

namespace App\Services\HolidayCode;

use Illuminate\Support\Facades\DB;
use App\Services\HolidayCode\HolidayCodeService;
use App\Models\HolidayCode\HolidayCodesOfSocialSecretary;
use App\Services\BaseService;
use App\Services\SocialSecretary\SocialSecretaryService;

class HolidayCodesOfSocialSecretaryService extends BaseService
{
    protected $holidayCodeService;

    protected $socialSecretaryService;

    public function __construct(HolidayCodesOfSocialSecretary $holidayCodesOfSocialSecretary)
    {
        parent::__construct($holidayCodesOfSocialSecretary);

        $this->holidayCodeService = app(HolidayCodeService::class);

        $this->socialSecretaryService = app(SocialSecretaryService::class);
    }

    public function getOptionsToEdit($social_secretary_id)
    {
        try{
            // Retrieve all holiday codes using the instance
            $holidayCodes = $this->holidayCodeService->getAll();

            $socialSecretary = $this->socialSecretaryService->get($social_secretary_id);

            // Transform the data into the desired format
            $result = $holidayCodes->map(function ($holidayCode) use ($social_secretary_id) {
                $social_secretary_code = $this->model::where('holiday_code_id', $holidayCode->id)
                    ->where('social_secretary_id', $social_secretary_id)
                    ->get()
                    ->first()
                    ->social_secretary_code ?? null;
                return [
                    'holiday_code_id'       => $holidayCode->id,
                    'holiday_code_name'     => $holidayCode->holiday_code_name,
                    'holiday_code'          => $holidayCode->internal_code,
                    'social_secretary_code' => $social_secretary_code,
                ];
            });

            return [
                'social_secretary_id'   => $social_secretary_id,
                'social_secretary_name' => $socialSecretary->name,
                'details'               => $result
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

                $social_secretary_id    = $values['social_secretary_id'];
                $social_secretary_codes = $values['social_secretary_codes'];
            
                foreach ($social_secretary_codes as $social_secretary_code) {
                    $holidayCodeId       = $social_secretary_code['holiday_code_id'];
                    $socialSecretaryCode = $social_secretary_code['social_secretary_code'];

                    // Check if 'social_secretary_code' is NULL, and if so, set a default value
                    if ($socialSecretaryCode === null) {
                        $socialSecretaryCode = ''; // Or provide any default value you prefer
                    }
                    // Use updateOrCreate to update existing or create new records
                    $this->model::updateOrCreate(
                        [
                            'holiday_code_id'     => $holidayCodeId,
                            'social_secretary_id' => $social_secretary_id,
                        ],
                        ['social_secretary_code' => $socialSecretaryCode]
                    );
                }
            DB::commit();
            return true; // Successfully saved holiday codes
        } catch (\Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}