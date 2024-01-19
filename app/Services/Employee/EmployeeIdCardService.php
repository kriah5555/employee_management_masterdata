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

        $filePath = Storage::disk('tenant')->putFileAs($storePath, $file, $filename);

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

    public function getEmployeeIdCards($employee_profile_id) # will get all contracts and documents of employee
    {
        try {

            $employee_id_cards = $this->employeeIdCardRepository->getEmployeeIdCardByEmployeeProfileId($employee_profile_id);
            return $employee_id_cards->map(function ($id_card) {

                $type = null;

                if ($id_card->type == config('constants.EMPLOYEE_ID_FRONT')) {
                    $type = 'ID card front';
                } elseif ($id_card->type == config('constants.EMPLOYEE_ID_BACK')) {
                    $type = 'ID card back';
                }

                return [
                    'file_id'   => $id_card->files->id,
                    'file_name' => $id_card->files->file_name,
                    'file_url'  => $id_card->file_url,
                    'type'      => $type,
                ];
            });
    
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
