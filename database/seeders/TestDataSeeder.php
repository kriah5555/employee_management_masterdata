<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Company\Company;
use App\Models\CommuteType;
use App\Models\EmployeeFunction\FunctionCategory;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Models\Company\Location;
use App\Models\MinimumSalary;
use App\Models\Sector\SectorAgeSalary;
use Illuminate\Database\Seeder;
use App\Models\Contract\ContractType;
use App\Models\MealVoucher;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeType\EmployeeTypeConfig;
use App\Models\EmployeeType\EmployeeTypeDimonaConfig;
use App\Models\Sector\Sector;
use App\Models\Sector\SectorSalaryConfig;
use App\Models\Sector\SectorSalarySteps;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contractTypeList = [
            ['name' => 'Annual contract', 'contract_renewal_type_id' => 1, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Employment contract full time variable', 'contract_renewal_type_id' => 1, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Daily contract', 'contract_renewal_type_id' => 2, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Flex long term contract', 'contract_renewal_type_id' => 3, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Framework Agreement', 'contract_renewal_type_id' => 4, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Voluntary overtime', 'contract_renewal_type_id' => 4, 'created_by' => 0, 'updated_by' => 0],
        ];
        ContractType::insert($contractTypeList);

        $commuteTypes = [
            ['name' => 'Personal Vehicle', 'sort_order' => 1],
            ['name' => 'Public Transit', 'sort_order' => 2],
            ['name' => 'Bicycle', 'sort_order' => 3],
            ['name' => 'Walking', 'sort_order' => 4],
            ['name' => 'Carpool', 'sort_order' => 5],
            ['name' => 'Company Shuttle', 'sort_order' => 6],
            ['name' => 'Telecommute/Remote', 'sort_order' => 7],
            ['name' => 'Other', 'sort_order' => 8],
            ['name' => 'Not Applicable', 'sort_order' => 9]
        ];
        CommuteType::insert($commuteTypes);

        $mealVouchers = [
            ['name' => 'Sodexo', 'sort_order' => 1]
        ];
        MealVoucher::insert($mealVouchers);


        $employeeTypes = [
            [
                'name'                                => 'Normal employee',
                'employee_type_category_id'           => 1,
                'contract_types'                      => [1],
                'employee_type_config_details'        => [
                    'consecutive_days_limit' => 7,
                    'icon_color'             => "#91aa88",
                    'start_in_past'          => true,
                    'counters'               => true,
                    'contract_hours_split'   => true,
                    'leave_access'           => true,
                    'holiday_access'         => true
                ],
                'employee_type_dimona_config_details' => [
                    'dimona_type_id' => 3
                ]
            ],
            [
                'name'                                => 'Student',
                'employee_type_category_id'           => 2,
                'contract_types'                      => [3],
                'employee_type_config_details'        => [
                    'consecutive_days_limit' => 5,
                    'icon_color'             => "#91aa88",
                    'start_in_past'          => false,
                    'counters'               => true,
                    'contract_hours_split'   => true,
                    'leave_access'           => true,
                    'holiday_access'         => false
                ],
                'employee_type_dimona_config_details' => [
                    'dimona_type_id' => 1
                ]
            ],
            [
                'name'                                => 'Long term student',
                'employee_type_category_id'           => 1,
                'contract_types'                      => [1],
                'employee_type_config_details'        => [
                    'consecutive_days_limit' => 5,
                    'icon_color'             => "#91aa88",
                    'start_in_past'          => false,
                    'counters'               => true,
                    'contract_hours_split'   => true,
                    'leave_access'           => true,
                    'holiday_access'         => true
                ],
                'employee_type_dimona_config_details' => [
                    'dimona_type_id' => 1
                ]
            ],
            [
                'name'                                => 'Long term flex',
                'employee_type_category_id'           => 1,
                'contract_types'                      => [4],
                'employee_type_config_details'        => [
                    'consecutive_days_limit' => 5,
                    'icon_color'             => "#91aa88",
                    'start_in_past'          => false,
                    'counters'               => true,
                    'contract_hours_split'   => true,
                    'leave_access'           => true,
                    'holiday_access'         => true
                ],
                'employee_type_dimona_config_details' => [
                    'dimona_type_id' => 2
                ]
            ],
        ];

        foreach ($employeeTypes as $employeeType) {
            $contractTypes = $employeeType['contract_types'];
            unset($employeeType['contract_types']);
            $employee_type_config_details = $employeeType['employee_type_config_details'];
            unset($employeeType['employee_type_config_details']);
            $employee_type_dimona_config_details = $employeeType['employee_type_dimona_config_details'];
            unset($employeeType['employee_type_dimona_config_details']);
            $employeeTypeObj = EmployeeType::create($employeeType);
            $employeeTypeObj->contractTypes()->sync($contractTypes ?? []);
            $employee_type_config_details['employee_type_id'] = $employee_type_dimona_config_details['employee_type_id'] = $employeeTypeObj->id;
            EmployeeTypeConfig::create($employee_type_config_details);
            EmployeeTypeDimonaConfig::create($employee_type_dimona_config_details);
        }
        $sectorDetails = [
            [
                'name'                         => 'Horeca',
                'paritair_committee'           => 302,
                'category'                     => 9,
                'employee_types'               => [1, 2, 3, 4],
                'sector_salary_config_details' => [
                    'category' => 9,
                    'steps'    => 10,
                ],
                'sector_salary_steps_details'  => [
                    [
                        'level' => 1,
                        'from'  => 0,
                        'to'    => 12
                    ],
                    [
                        'level' => 2,
                        'from'  => 13,
                        'to'    => 24
                    ],
                    [
                        'level' => 3,
                        'from'  => 25,
                        'to'    => 36
                    ],
                    [
                        'level' => 4,
                        'from'  => 37,
                        'to'    => 48
                    ],
                    [
                        'level' => 5,
                        'from'  => 49,
                        'to'    => 60
                    ],
                    [
                        'level' => 6,
                        'from'  => 61,
                        'to'    => 72
                    ],
                    [
                        'level' => 7,
                        'from'  => 73,
                        'to'    => 84
                    ],
                    [
                        'level' => 8,
                        'from'  => 85,
                        'to'    => 96
                    ],
                    [
                        'level' => 9,
                        'from'  => 97,
                        'to'    => 108
                    ],
                    [
                        'level' => 10,
                        'from'  => 109,
                        'to'    => 120
                    ],
                ],
                'sector_age_salary'            => [
                    [
                        'age'              => 18,
                        'percentage'       => 90,
                        'max_time_to_work' => '22:00'
                    ],
                    [
                        'age'              => 17,
                        'percentage'       => 80,
                        'max_time_to_work' => '21:00'
                    ],
                    [
                        'age'              => 16,
                        'percentage'       => 70,
                        'max_time_to_work' => '20:00'
                    ],
                    [
                        'age'              => 15,
                        'percentage'       => 60,
                        'max_time_to_work' => '19:00'
                    ],
                ],
            ]
        ];

        foreach ($sectorDetails as $sectorDetail) {
            $employee_types = $sectorDetail['employee_types'];
            unset($sectorDetail['employee_types']);
            $sector_salary_config_details = $sectorDetail['sector_salary_config_details'];
            unset($sectorDetail['sector_salary_config_details']);
            $sector_salary_steps_details = $sectorDetail['sector_salary_steps_details'];
            unset($sectorDetail['sector_salary_steps_details']);
            $sector_age_salary = $sectorDetail['sector_age_salary'];
            unset($sectorDetail['sector_age_salary']);
            $sectorObj = Sector::create($sectorDetail);
            $sectorObj->employeeTypes()->sync($employee_types ?? []);
            $sector_salary_config_details['sector_id'] = $sectorObj->id;
            $sectorSalaryConfigObj = SectorSalaryConfig::create($sector_salary_config_details);
            $minimumSalaryData = $sectorAgeSalaryDetails = [];
            foreach ($sector_salary_steps_details as $data) {
                $data['sector_salary_config_id'] = $sectorSalaryConfigObj->id;
                $sectorSalaryStepObj = SectorSalarySteps::create($data);
                foreach (range(1, 9) as $category_number) {
                    $minimumSalaryData[] = [
                        'sector_salary_steps_id' => $sectorSalaryStepObj->id,
                        'category_number'        => $category_number,
                        'hourly_minimum_salary'  => 0,
                        'monthly_minimum_salary' => 0
                    ];
                }
            }
            MinimumSalary::insert($minimumSalaryData);
            foreach ($sector_age_salary as $sector_age_salary) {
                $sector_age_salary['sector_id'] = $sectorObj->id;
                $sectorAgeSalaryDetails[] = $sector_age_salary;
            }
            SectorAgeSalary::insert($sectorAgeSalaryDetails);

            $groupFunctions = [
                'name'      => "Group 1",
                'category'  => "4",
                'sector_id' => 1
            ];
            FunctionCategory::insert($groupFunctions);



            $functionTitles = [
                [
                    'name'                 => "Cooking",
                    'function_code'        => "123",
                    'function_category_id' => 1
                ],
                [
                    'name'                 => "Cleaning",
                    'function_code'        => "222",
                    'function_category_id' => 1
                ],
                [
                    'name'                 => "Assistant",
                    'function_code'        => "100",
                    'function_category_id' => 1
                ],
            ];
            FunctionTitle::insert($functionTitles);
        }

        // $companyDetails = [
        //     'company_name'            => 'Test company',
        //     'employer_id'             => 12345,
        //     'sender_number'           => 12345,
        //     'rsz_number'              => 12345,
        //     'social_secretary_number' => 12345,
        //     'username'                => "username",
        //     'email'                   => "companyemail@gmail.com",
        //     'phone'                   => "+919000000000",
        //     'address_details'         => [
        //         'street_house_no' => 'street',
        //         'postal_code'     => 1234,
        //         'city'            => 'city',
        //         'country'         => 'country'
        //     ],
        //     'location_details'        => [
        //         "location_name" => "Location 1"
        //     ],
        //     'sectors'                 => [1]
        // ];
        // $companyAddressObj = Address::create($companyDetails['address_details']);
        // $locationAddressObj = Address::create($companyDetails['address_details']);
        // $locationDetails = $companyDetails['location_details'];
        // unset($companyDetails['location_details']);
        // $sectors = $companyDetails['sectors'];
        // unset($companyDetails['sectors']);
        // unset($companyDetails['address_details']);
        // $companyDetails['address'] = $companyAddressObj->id;
        // $companyObj = Company::create($companyDetails);
        // $companyObj->sectors()->sync($sectors);
        // $locationDetails['company'] = $companyObj->id;
        // $locationDetails['address'] = $locationAddressObj->id;
        // Location::create($locationDetails);

    }
}
