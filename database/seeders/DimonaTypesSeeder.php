<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dimona\DimonaType;

class DimonaTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dimonaType = [
            ['name' => 'Student', 'dimona_type_key' => 'student', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Flex', 'dimona_type_key' => 'flexi', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'OTH', 'dimona_type_key' => 'oth', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'No dimona', 'dimona_type_key' => 'no_dimona', 'created_by' => 0, 'updated_by' => 0]
        ];
        DimonaType::insert($dimonaType);
    }
}