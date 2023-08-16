<?php

namespace Tests\Feature;


use App\Models\EmployeeType\EmployeeType;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeTypeControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /**
     * Test the index method.
     */
    public function test_index()
    {
        // Assuming you have some EmployeeType instances in the database
        $response = $this->get('/api/employee-types');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data'])
            ->assertJson(['success' => true]);
    }

    /**
     * Test the store method.
     */
    public function test_store()
    {
        $data = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            // Add other required fields here
        ];

        $response = $this->post('/api/employee-types', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data'])
            ->assertJson(['success' => true]);
    }

    /**
     * Test the show method.
     */
    public function test_show()
    {
        // Assuming you have an EmployeeType instance in the database
        $employeeType = EmployeeType::first();

        $response = $this->get("/api/employee-types/{$employeeType->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data'])
            ->assertJson(['success' => true]);
    }

    /**
     * Test the update method.
     */
    public function test_update()
    {
        // Assuming you have an EmployeeType instance in the database
        $employeeType = EmployeeType::first();
        $updatedData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            // Add other fields to update here
        ];

        $response = $this->put("/api/employee-types/{$employeeType->id}", $updatedData);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data'])
            ->assertJson(['success' => true]);
    }

    /**
     * Test the destroy method.
     */
    public function test_destroy()
    {
        // Assuming you have an EmployeeType instance in the database
        $employeeType = EmployeeType::first();

        $response = $this->delete("/api/employee-types/{$employeeType->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message'])
            ->assertJson(['success' => true]);
    }
}
