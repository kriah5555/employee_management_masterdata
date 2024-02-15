<?php

namespace App\Services\Employee;

use Exception;
use App\Models\User\Gender;
use GuzzleHttp\Psr7\Request;
use App\Models\User\MaritalStatus;
use Illuminate\Support\Facades\DB;
use App\Models\Company\CostCenter;
use App\Events\ImportEmployeeEvent;
use App\Services\Company\FileService;
use Illuminate\Support\Facades\Validator;
use App\Models\EmployeeType\EmployeeType;
use App\Services\Employee\EmployeeService;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Repositories\Employee\ImportEmployeeRepository;

# xl file operation
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

# validation
use App\Rules\MealVoucherRule;
use App\Rules\User\GenderRule;
use Illuminate\Validation\Rule;
use App\Rules\BelgiumCurrencyFormatRule;
use App\Rules\EmployeeCommuteDetailsRule;
use App\Rules\EmployeeFunctionDetailsRule;
use App\Rules\EmployeeContractDetailsRule;
use App\Rules\ResponsiblePersonExistsRule;
use App\Rules\DuplicateSocialSecurityNumberRule;
use App\Rules\ValidateLengthIgnoringSymbolsRule;

class ImportEmployeeService
{

    public function __construct(
        protected FileService $fileService,
        protected EmployeeService $employeeService,
        protected ImportEmployeeRepository $importEmployeeRepository,
    ) {
    }

    public function createImportEmployeeFile($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();

            $file_name      = str_replace(' ', '_', 'Import_planning_' . time() . '_' . $values['file']->getClientOriginalName());
            $file_id        = $this->fileService->saveFile($values['file'], $file_name, config('constants.ABSENCE_FILES_PATH'));
            $importEmployee = $this->importEmployeeRepository->createImportEmployeeFile(['file_id' => $file_id, 'import_status' => config('import_employee.IMPORT_STATUS_PENDING'), 'feedback' => ['']]);
            // $this->importEmployee($importEmployee);
            event(new ImportEmployeeEvent($importEmployee));
            DB::connection('tenant')->commit();

            return $importEmployee;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function importEmployee($importEmployee) # $importEmployee => model object 
    {
        try {
            DB::connection('tenant')->beginTransaction();

            $data = $this->validateAndImportCreateEmployee($importEmployee);

            DB::connection('tenant')->commit();
            return $data; 
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function validateAndImportCreateEmployee($importEmployee)
    {
        $file_path = 'storage/tenants/' . $importEmployee->file->file_path; 

        $feedback_file_path = str_replace('.', '_with_feedback.', $file_path);
        copy($file_path, $feedback_file_path);
        $feedback_file = $this->fileService->createFileData(['file_name' => str_replace('.', '_with_feedback.', $importEmployee->file->file_name),'file_path' => $feedback_file_path]);

        $errors = [];
        $reader = IOFactory::createReaderForFile($feedback_file_path);
        $reader->setReadDataOnly(false);
        $spreadsheet = $reader->load($feedback_file_path);
        $sheet       = $spreadsheet->getActiveSheet();
        $employee_details_formatted = convertXlsintoArray($feedback_file_path);
        unset($employee_details_formatted['0']);

        foreach ($employee_details_formatted as $index => $data) {
            try {
                if (isset($data[config('import_employee.SSN')])) {
                    $errors = $this->validateFields($data);
                    if (!empty($errors)) {
                        $sheet->setCellValue('Z' . ($index), $errors);
                    } else {
                        $gender_id         = $data[config('import_employee.GENDER')] ? ($this->getGenderByText($data[config('import_employee.GENDER')]) ? $this->getGenderByText($data[config('import_employee.GENDER')])->id : null ) : null;
        
                        $marital_status_id = $data[config('import_employee.MARITAL_STATUS')] ? ($this->getMaritalStatusText($data[config('import_employee.MARITAL_STATUS')]) ? $this->getMaritalStatusText($data[config('import_employee.MARITAL_STATUS')])->id : null ) : null;
        
                        $employee_type_id  = $data[config('import_employee.EMPLOYEE_TYPE')] ? ($this->getEmployeeTypeByText($data[config('import_employee.EMPLOYEE_TYPE')]) ? $this->getEmployeeTypeByText($data[config('import_employee.EMPLOYEE_TYPE')])->id : null ) : null;
        
                        $function_id       = $data[config('import_employee.FUNCTION')] ? ($this->getFunctionTitleText($data[config('import_employee.FUNCTION')]) ? $this->getFunctionTitleText($data[config('import_employee.FUNCTION')])->id : null ) : null;
                        $employee_details = [
                            'social_security_number' => trim($data[config('import_employee.SSN')]),
                            'first_name'            => trim($data[config('import_employee.FIRST_NAME')]),
                            'last_name'             => trim($data[config('import_employee.LAST_NAME')]),
                            'gender_id'             => $gender_id,
                            'date_of_birth'         => $data[config('import_employee.DOB')],
                            'street_house_no'       => $data[config('import_employee.STREET_HOUSE_NO')],
                            'postal_code'           => $data[config('import_employee.POSTAL_CODE')],
                            'place_of_birth'        => $data[config('import_employee.PLACE_OF_BIRTH')],
                            'city'                  => $data[config('import_employee.CITY')],
                            'country'               => $data[config('import_employee.COUNTRY')],
                            'nationality'           => $data[config('import_employee.NATIONALITY')],
                            'phone_number'          => $data[config('import_employee.PHONE_NUMBER')],
                            'email'                 => $data[config('import_employee.EMAIL')],
                            'license_expiry_date'   => $data[config('import_employee.LICENSE_EXPIRE_DATE')],
                            'account_number'        => $data[config('import_employee.BANK_ACCOUNT_NUMBER')],
                            'language'              => isset($data[config('import_employee.LANGUAGE')]) ? $data[config('import_employee.LANGUAGE')] : 'nl',
                            'marital_status_id'     => $marital_status_id,
                            'dependent_spouse'      => $data[config('import_employee.DEPENDANT_SPOUSE')],
                            'children'              => $data[config('import_employee.CHILDREN')],
                            'employee_contract_details' => [
                                'employee_type_id'      => $employee_type_id,
                                'sub_type'              => str_replace(' ', '_', trim(strtolower($data[config('import_employee.SUB_TYPE')]))),
                                'schedule_type'         => str_replace(' ', '_', trim(strtolower($data[config('import_employee.SCHEDULE_TYPE')]))),
                                'employment_type'       => str_replace(' ', '_', trim(strtolower($data[config('import_employee.EMPLOYMENT_TYPE')]))),
                                'weekly_contract_hours' => $data[config('import_employee.WEEKLY_CONTRACT_HOURS')],
                                'start_date'            => $data[config('import_employee.CONTRACT_START_DATE')],
                                'end_date'              => $data[config('import_employee.CONTRACT_END_DATE')],
                                'work_days_per_week'    => $data[config('import_employee.WORK_DAYS_PER_WEEK')],
                            ],
                            'employee_function_details' => [[
                                'function_id' => $function_id,
                                'salary'      => $data[config('import_employee.SALARY')],
                                'experience'  => $data[config('import_employee.EXPERIENCE')],
                            ]],
                            'employee_commute_details' => []
                        ];
                        $validator = Validator::make($employee_details, $this->employeeValidations(), []);
        
                        if ($validator->fails()) {
                            $errors[] = implode('; ', $validator->errors()->all());
                        } else {
                            $employee_profile = $this->employeeService->createNewEmployee($employee_details, getCompanyId());
                            $cost_center = $this->getCostCenterByCostCenterNumber($data[config('import_employee.COST_CENTER_NUMBER')]);
                            if ($cost_center) {
                                $cost_center->employees()->attach($employee_profile->id);
                            }
                            $sheet->setCellValue(config('import_employee.IMPORT_EMPLOYEE_FEEDBACK_COLUMN') . ($index + 1), t('Employee created successfully....'));
                        }
                        $sheet->setCellValue(config('import_employee.IMPORT_EMPLOYEE_FEEDBACK_COLUMN') . ($index + 1), implode('; ', $errors));
                    }
                }
            } catch (Exception $e) {
                $sheet->setCellValue(config('import_employee.IMPORT_EMPLOYEE_FEEDBACK_COLUMN') . ($index + 1), $e->getMessage());
                error_log($e->getMessage());
                continue;  # will continue the loop even after teh exception handled
            }
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($feedback_file_path);

        $importEmployee->update(['import_status' => config('import_employee.IMPORT_STATUS_COMPLETED'),'feedback_file_id' => $feedback_file->id, 'imported_date' => date('Y-m-d')]);

        return $errors;
    }

    public function employeeValidations()
    {
            return [
                'first_name'            => 'required|string|max:255',
                'last_name'             => 'required|string|max:255',
                'gender_id'             => [
                    'bail',
                    'nullable',
                    'integer',
                    new GenderRule(),
                ],
                'date_of_birth'         => 'required|date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
                'street_house_no'       => 'nullable|string|max:255',
                'postal_code'           => 'nullable|string|max:50',
                'place_of_birth'        => 'nullable|string|max:50',
                'city'                  => 'nullable|string|max:50',
                'country'               => 'nullable|string|max:50',
                'nationality'           => 'required|string|max:50',
                'latitude'              => 'nullable|numeric',
                'longitude'             => 'nullable|numeric',
                'phone_number'          => 'required|string|max:20',
                'email'                 => 'required|email',
                'license_expiry_date'   => 'nullable|date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
                'account_number'        => 'nullable|string|max:255',
                'language'              => ['nullable', 'string', 'in:' . implode(',', array_keys(config('constants.LANGUAGE_OPTIONS')))],
                'marital_status_id'     => [
                    'bail',
                    'nullable',
                    'integer',
                ],
                'dependent_spouse'      => 'nullable|string|max:255',
                'children'              => 'nullable|integer',
                'fuel_card' => 'nullable|boolean',
                'company_car' => 'nullable|boolean',
                'social_secretary_number' => 'nullable|string|max:255',
                'contract_number' => 'nullable|string|max:255',
                'social_security_number' => ['required', 'string', new ValidateLengthIgnoringSymbolsRule(11, 11, [',', '.', '-']), new DuplicateSocialSecurityNumberRule()],
                'employee_contract_details' => ['bail', 'required', 'array', new EmployeeContractDetailsRule()],
                'employee_function_details' => ['bail', 'required', 'array', new EmployeeFunctionDetailsRule()],
            ];
    }
    
    public function validateFields($employee_data)
    {
        $requiredFields = [
            'first_name',
            'last_name',
            'date_of_birth',
            'street_house_no',
            'postal_code',
            'place_of_birth',
            'city',
            'country',
            'nationality',
            'phone_number',
            'email',
            'license_expiry_date',
            'account_number',
            'language',
            'dependent_spouse',
            'children',
            'employee_type_id',
            'sub_type',
            'schedule_type',
            'employment_type',
            'weekly_contract_hours',
            'start_date',
            'end_date',
            'work_days_per_week',
            'function_id',
            'salary',
            'experience',
        ];

        $errors = [];
        foreach ($requiredFields as $field) {
            if (isset($data[config('import_employee.FIRST_NAME')]) && !empty($data[config('import_employee.FIRST_NAME')])) {
                if (!isset($data[$field])) {
                    $errors[] = 'Please fill all the required fields';
                    break;
                }
            }
        }

        return $errors;
    }

    public function validateData($employee_data)
    {
        
    }

    public function getImportEmployeeFiles()
    {
        try {
            return $this->importEmployeeRepository->getImportEmployeeFiles()->transform(function ($data) {
                $status = null;
                if ($data['import_statusPending'] == config('import_employee.IMPORT_STATUS_PENDING')) {
                    $status = '';
                } elseif ($data['import_status'] == config('import_employee.IMPORT_STATUS_COMPLETED')) {
                    $status = 'Completed';
                }

                $data['import_status'] = $status;
                return $data;
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
        
    }

    public function convertXlsintoArray($full_path)
    { 
        $inputFileType = IOFactory::identify($full_path); # will get the extension of the file
        $objReader = IOFactory::createReader($inputFileType); # will create the reader
        $objReader->setReadDataOnly(true); # will ifnore all styling data and read only the cell data
        $objPHPExcel = $objReader->load($full_path); # load the data of xl sheet
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0); # The following line of code sets the active sheet index to the first sheet

        $maxCell = $objWorksheet->getHighestRowAndColumn();

        return $objWorksheet->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);
    }

    public function getGenderByText($text)
    {
        return Gender::whereRaw('LOWER(name) = LOWER(?)', [trim($text)])->first();
    }

    public function getEmployeeTypeByText($text)
    {
        return EmployeeType::whereRaw('LOWER(name) = LOWER(?)', [trim($text)])->first();
    }

    public function getFunctionTitleText($text)
    {
        return FunctionTitle::whereRaw('LOWER(name) = LOWER(?)', [trim($text)])->first();
    }

    public function getMaritalStatusText($text)
    {
        return MaritalStatus::whereRaw('LOWER(name) = LOWER(?)', [trim($text)])->first();
    }

    public function getCostCenterByCostCenterNumber($cost_center_number)
    {
        return CostCenter::where('cost_center_number', $cost_center_number)->first();
    }

    public function downloadSampleXlFile($type = 'employee')
    {
        try {
            if ($type == 'employee') {
    
                $spreadsheet = new Spreadsheet();
                $spreadsheet->getActiveSheet()->getStyle('A'. 1 .":".config('import_employee.IMPORT_EMPLOYEE_FEEDBACK_COLUMN'). 1)->applyFromArray(
                    ['fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['argb' => 'D9D9D9'],
                        ],
                    ]        
                  ); # to fill the color to first row
    
                  
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->fromArray([array_keys(config('import_employee.IMPORT_EMPLOYEE_HEADERS'))], null, 'A1'); # add header data to sheet
    
                $filename = "Import-$type-sample-xlsx_file.xlsx";
                  
                # commented lines are foe download file
                // $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
                // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                // header('Content-Disposition: attachment; filename='.$filename);
                // header('Cache-Control: must-revalidate');
                // while (ob_get_level() > 0) {
                //     ob_end_clean();
                // }                
                // $writer->save("php://output");

                
                // $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
    
                // $path = storage_path('app/' . $filename);
                // $writer->save($path);
                    
                
                $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
                $path = storage_path('app/public/' . $filename);
                $writer->save($path);

                // Return the URL to the file
                return secure_asset('storage/'.$filename);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
