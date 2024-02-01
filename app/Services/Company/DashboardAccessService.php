<?php

namespace App\Services\Company;

use Exception;
use App\Models\Company\DashboardAccess;
use App\Models\Company\Location;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardAccessService
{
    public function getDashboardAccessKeyForCompany($companyId)
    {
        try {
            return $this->getDashboardAccessKey('company', $companyId)->access_key;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
    public function getDashboardAccessKeyForLocation($companyId, $locationId)
    {
        try {
            return $this->getDashboardAccessKey('location', $companyId, $locationId)->access_key;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getDashboardAccessKey($type, $companyId, $locationId = null)
    {
        $query = DashboardAccess::where('type', $type)->where('company_id', $companyId);
        if ($type == 'location') {
            $query->where('location_id', $locationId);
        }
        $dashboardAccess = $query->first();
        if (!$dashboardAccess) {
            $dashboardAccess = DashboardAccess::create([
                'access_key'  => Str::uuid(),
                'type'        => $type,
                'company_id'  => $companyId,
                'location_id' => $locationId
            ]);
        }
        return $dashboardAccess;
    }
    public function deleteDashboardAccessKey($access_key)
    {
        try {
            return DashboardAccess::where('access_key', $access_key)->delete();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
    public function validateCompanyDashboardAccessKey($access_key)
    {
        try {
            return DashboardAccess::where('type', 'company')->where('access_key', $access_key)->first() ? true : false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
    public function validateLocationDashboardAccessKey($access_key)
    {
        try {
            return DashboardAccess::where('type', 'location')->where('access_key', $access_key)->first() ? true : false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
    public function validateAccessKey($access_key)
    {
        try {
            return DashboardAccess::where('access_key', $access_key)->first() ? true : false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
