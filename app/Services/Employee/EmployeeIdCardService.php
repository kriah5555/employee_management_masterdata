<?php

namespace App\Services\Employee;

use Exception;
use App\Models\Planning\Files;
use Illuminate\Support\Facades\DB;
use App\Services\Company\FileService;
use App\Repositories\Employee\EmployeeIdCardRepository;
use Illuminate\Support\Facades\Storage;

class EmployeeIdCardService
{

    public function __construct(
        protected EmployeeIdCardRepository $employeeIdCardRepository,
        protected FileService $fileService,
    ) {
    }

    public function saveEmployeeIdCard($details)
    {
        try {
            DB::connection('tenant')->beginTransaction();

            if (isset($details['id_card_front'])) {
                $this->saveIdCard($details, $details['id_card_front'], config('constants.EMPLOYEE_ID_FRONT'));
            }

            if (isset($details['id_card_back'])) {
                $this->saveIdCard($details, $details['id_card_back'], config('constants.EMPLOYEE_ID_BACK'));
            }

            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function saveIdCard($details, $file, $type)
    {
        $filename = $this->generateFilename($details['employee_profile_id'], $type, $file->getClientOriginalName());

        $storePath = tenant('id') . DIRECTORY_SEPARATOR . trim(config('constants.EMPLOYEE_ID_PATH'), DIRECTORY_SEPARATOR);

        $filePath = Storage::disk('tenant')->putFile($storePath, $file);

        $fileData = $this->fileService->createFileData([
            'file_name' => $filename,
            'file_path' => $filePath, 
        ]);

        $details['file_id'] = $fileData->id;
        $details['type'] = $type;
        unset($details['id_card_front'], $details['id_card_back']);

        $this->employeeIdCardRepository->updateEmployeeIdCardsByEmployeeProfileId($details);
    }

    private function generateFilename($employeeProfileId, $type, $originalName)
    {
        return str_replace(' ', '_', $employeeProfileId . ($type == 1 ? '_ID_front' : '_ID_back') . '_' . time() . '_' . $originalName);
    }
}
