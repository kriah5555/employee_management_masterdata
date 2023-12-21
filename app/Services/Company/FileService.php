<?php

namespace App\Services\Company;

use App\Models\Planning\Files;

class FileService
{
    public function createFileData($values)
    {
        return Files::create($values);
    }
}
