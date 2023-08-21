<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Dimona\DimonaType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DimonaTypeFactory extends Factory
{
    protected $model = DimonaType::class;

    public function definition(): array
    {
        return $this->faker->randomElement([
            ['name' => 'Student', 'dimona_type_key' => 'student', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Flex', 'dimona_type_key' => 'flexi', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'OTH', 'dimona_type_key' => 'oth', 'created_by' => 0, 'updated_by' => 0]
        ]);
    }
}
