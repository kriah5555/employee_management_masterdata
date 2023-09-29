<?php

namespace Database\Seeders;

use App\Models\Employee\CommuteType;
use Illuminate\Database\Seeder;
use App\Models\Contract\ContractType;
use App\Models\Employee\MaritalStatus;
use App\Models\Employee\Gender;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contractTypeList = [
            ['name' => 'Annual contract', 'contract_renewal_type_id' => 7, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Employment contract full time variable', 'contract_renewal_type_id' => 8, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Daily contract', 'contract_renewal_type_id' => 2, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Flex long term contract', 'contract_renewal_type_id' => 5, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Framework Agreement', 'contract_renewal_type_id' => 8, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Voluntary overtime', 'contract_renewal_type_id' => 6, 'created_by' => 0, 'updated_by' => 0],
        ];
        ContractType::insert($contractTypeList);
        $maritalStatuses = [
            ['name' => 'Single', 'sort_order' => 1],
            ['name' => 'Married', 'sort_order' => 2],
            ['name' => 'Civil Partnership', 'sort_order' => 3],
            ['name' => 'Separated', 'sort_order' => 4],
            ['name' => 'Divorced', 'sort_order' => 5],
            ['name' => 'Widowed', 'sort_order' => 6],
            ['name' => 'In a Relationship', 'sort_order' => 7],
            ['name' => 'Other', 'sort_order' => 8]
        ];
        MaritalStatus::insert($maritalStatuses);
        $genders = [
            ['name' => 'Male', 'sort_order' => 1],
            ['name' => 'Female', 'sort_order' => 2],
            ['name' => 'Non-Binary', 'sort_order' => 3],
            ['name' => 'Transgender', 'sort_order' => 4],
            ['name' => 'Genderqueer', 'sort_order' => 5],
            ['name' => 'Genderfluid', 'sort_order' => 6],
            ['name' => 'Agender', 'sort_order' => 7],
            ['name' => 'Bigender', 'sort_order' => 8],
            ['name' => 'Two-Spirit', 'sort_order' => 9],
            ['name' => 'Other', 'sort_order' => 10],
            ['name' => 'Prefer not to say', 'sort_order' => 11]
        ];
        Gender::insert($genders);

        $maritalStatuses = [
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
        CommuteType::insert($maritalStatuses);
    }
}