<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    protected $connection = 'master';

    use HasDatabase;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'company_id',
            'database_name',
        ];
    }
}
