<?php

namespace App\Services\SocialSecretary;

use App\Models\Holiday\HolidayCodesOfSocialSecretary;
use App\Models\SocialSecretary\SocialSecretary;
use App\Repositories\SocialSecretary\SocialSecretaryRepository;
use App\Services\Holiday\HolidayCodeService;
use Illuminate\Support\Facades\DB;

class SocialSecretaryService
{
    protected $socialSecretaryRepository;
    protected $holidayCodeService;

    public function __construct(SocialSecretaryRepository $socialSecretaryRepository, HolidayCodeService $holidayCodeService)
    {
        $this->socialSecretaryRepository = $socialSecretaryRepository;
        $this->holidayCodeService = $holidayCodeService;
    }

    public function getSocialSecretaries()
    {
        return $this->socialSecretaryRepository->getSocialSecretaries();
    }
    public function createSocialSecretary($values)
    {
        return $this->socialSecretaryRepository->createSocialSecretary($values);
    }
    public function getSocialSecretaryDetails($id)
    {
        return $this->socialSecretaryRepository->getSocialSecretaryById($id);
    }
    public function updateSocialSecretary($socialSecretary, $values)
    {
        return $this->socialSecretaryRepository->updateSocialSecretary($socialSecretary, $values);
    }
    public function deleteSocialSecretary($socialSecretary)
    {
        return $this->socialSecretaryRepository->deleteSocialSecretary($socialSecretary);
    }

    public function getSocialSecretaryOptions()
    {
        try {
            return SocialSecretary::where('status', true)->get();
            // return SocialSecretary::where('status', true)->select(['id as label', 'name as value'])->get();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getSocialSecretaryHolidayConfiguration($socialSecretaryId)
    {
        // Retrieve all holiday codes using the instance
        $holidayCodes = $this->holidayCodeService->getHolidayCodes();

        $socialSecretary = $this->socialSecretaryRepository->getSocialSecretaryById($socialSecretaryId);

        // Transform the data into the desired format
        $result = $holidayCodes->map(function ($holidayCode) use ($socialSecretaryId) {
            $social_secretary_code = HolidayCodesOfSocialSecretary::where('holiday_code_id', $holidayCode->id)
                ->where('social_secretary_id', $socialSecretaryId)
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
            'social_secretary_id'   => $socialSecretaryId,
            'social_secretary_name' => $socialSecretary->name,
            'details'               => $result
        ];
    }
    public function updateSocialSecretaryHolidayConfiguration($values)
    {
        DB::transaction(function () use ($values) {

            $social_secretary_codes = $values['social_secretary_codes'];
            $social_secretary_id = $values['social_secretary_id'];

            foreach ($social_secretary_codes as $social_secretary_code) {
                $holidayCodeId = $social_secretary_code['holiday_code_id'];
                $socialSecretaryCode = $social_secretary_code['social_secretary_code'];

                // Check if 'social_secretary_code' is NULL, and if so, set a default value
                if ($socialSecretaryCode === null) {
                    $socialSecretaryCode = ''; // Or provide any default value you prefer
                }
                // Use updateOrCreate to update existing or create new records
                $holidayCodesOfSocialSecretary = HolidayCodesOfSocialSecretary::updateOrCreate(
                    [
                        'holiday_code_id'     => $holidayCodeId,
                        'social_secretary_id' => $social_secretary_id,
                    ],
                    [
                        'social_secretary_code' => $socialSecretaryCode
                    ]
                );
                $holidayCodesOfSocialSecretary->save();
            }
        });
    }
}