<?php

namespace App\Services\Company;

use App\Models\Tenant;

class CompanyTenancyService
{
    public function createTenant($company)
    {
        $database_name = 'tenant_' . strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $company->company_name) . '_' . $company->id);

        return Tenant::create([
            'tenancy_db_name' => $database_name,
            'database_name'   => $database_name,
            'company_id'      => $company->id,
        ]);
    }
    public function getTenantByCompanyId($companyId)
    {
        return Tenant::where('company_id', $companyId)->first();
    }
}
