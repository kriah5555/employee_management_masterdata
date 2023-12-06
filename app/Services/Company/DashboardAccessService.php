<?php

namespace App\Services\Company;

use Exception;
use App\Models\Company\DashboardAccess;
use App\Models\Company\Location;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class DashboardAccessService
{
    protected $locations=[];
    public function getDashboardAccess($request)
    {
        try{
            $unique_key = $request->input('unique_key');
            $dashboardAccessData = DashboardAccess::where('unique_key', $unique_key)->get();

            if($dashboardAccessData->pluck('type')->first() == config('constants.COMPANY')) {
                $this->locations= Location::get();
            } else if($dashboardAccessData->pluck('type')->first() == config('constants.LOCATIONS')) {
                $this->locations= Location::where('id',$dashboardAccessData->pluck('location_id'))->get();
            }
            if ($this->locations->count() > 0) {
                return $this->generateQrCodeWithLocations();
            }
        }catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function generateQrCodeWithLocations(){

        $today = Carbon::now()->toDateString();

        foreach($this->locations as $location){

            $locationName = $location['location_name'];

            $qrCodeData = "Location: $locationName, Date: $today";

            $qrCodeImage = QrCode::size(300)->generate($qrCodeData);

            $base64Image = base64_encode($qrCodeImage);

            $location['qrCode'] = $base64Image;
        }

        return $this->locations;
    }

    public function createDashboardAccess($dashboardAccess)
    {
        try {
            return DashboardAccess::create($dashboardAccess);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteDashboardAccess($unique_key)
    {
        try {
            return DashboardAccess::where('unique_key', $unique_key)->first()->delete();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
