<?php

namespace App\Repositories\Employee;

use App\Models\Company\Employee\ImportEmployee;
use Illuminate\Database\Eloquent\Collection;

class ImportEmployeeRepository
{

    public function createImportEmployeeFile($data)
    {
        return ImportEmployee::create($data);
    }

    public function getImportEmployeeFiles()
    {
        return ImportEmployee::where('status', true)->get();
    }
}
