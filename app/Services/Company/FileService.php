<?php

namespace App\Services\Company;

use App\Models\Planning\Files;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function createFileData($values)
    {
        return Files::create($values);
    }

    public function saveFile($file, $file_name, $path)
    {    
        $storePath = tenant('id') . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);

        $filePath = Storage::disk('tenant')->putFileAs($storePath, $file, $file_name);
        
        $fileData = $this->createFileData([
            'file_name' => $file_name,
            'file_path' => $filePath, 
        ]);

        return $fileData->id;

    }
}
