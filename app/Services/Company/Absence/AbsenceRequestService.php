<?php

namespace App\Services\Company\Absence;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\Company\FileService;
use Illuminate\Support\Facades\Storage;
use App\Models\Company\Absence\AbsenceRequest;

class AbsenceRequestService
{

    public function __construct(protected FileService $fileService)
    {

    }
   
    public function employeeLeaveRequest($values) 
    {
        try {
            DB::connection('tenant')->beginTransaction();
    
            $file_ids = [$this->saveAbsenceRequestFiles($values['plan_id'], $values['file'])]; # currently it is single file but the module can link multiple files to the request make change here to link multiple files

            $absence_request = AbsenceRequest::create($values);

            if (!empty($values['files'])) {
                $absence_request->sync($file_ids);
            }
    
            DB::connection('tenant')->commit();
            
            return $absence_request;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function saveAbsenceRequestFiles($plan_id, $file)
    {

        if (!empty($file)) {

            $file_name = str_replace(' ', '_', $plan_id . '_leave_request_file' . '_' . time() . '_' . $file->getClientOriginalName());
    
            $storePath = tenant('id') . DIRECTORY_SEPARATOR . trim(config('constants.EMPLOYEE_ID_PATH'), DIRECTORY_SEPARATOR);
    
            $filePath = Storage::disk('tenant')->putFileAs($storePath, $file, $file_name);
            
            $fileData = $this->fileService->createFileData([
                'file_name' => $file_name,
                'file_path' => $filePath, 
            ]);

            return $fileData->id;
    
            $this->employeeIdCardRepository->updateEmployeeIdCardsByEmployeeProfileId($details);
        }
        }
}
