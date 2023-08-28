<?php

namespace Tests\Feature;


use App\Models\EmployeeType\EmployeeType;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Database\Factories\EmployeeTypeCategoryFactory;
use Database\Factories\ContractTypesFactory;
use Database\Factories\EmployeeTypeFactory;
use Database\Factories\DimonaTypeFactory;


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
    public function test_employee_type_store()
    {
        $data = [
            'name'                      => "test employee data",
            'description'               => $this->faker->sentence,
            'employee_type_category_id' => EmployeeTypeCategoryFactory::new()->create()->id,
            'contract_types'            => [ContractTypesFactory::new()->create()->id, ContractTypesFactory::new()->create()->id],
            'dimona_type_id'            => DimonaTypeFactory::new()->create()->id,
            'status'                    => 1
        ];

        $response = $this->post('/api/employee-types', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data'])
            ->assertJson(['success' => true]);

        // Validate the response data
        $responseData = $response->json('data');
        $this->assertEquals($data['name'], $responseData['name']);
        $this->assertEquals($data['description'], $responseData['description']);
        // Add more assertions for other fields
    }

    public function test_employee_type_show()
    {
        EmployeeTypeFactory::new()->create();
        $employeeType = EmployeeType::orderBy('updated_at', 'desc')->first(); // Get last updated

        $response = $this->get("/api/employee-types/{$employeeType->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data'])
            ->assertJson(['success' => true]);

        // Validate the response data
        $responseData = $response->json('data');
        $this->assertEquals($employeeType->name, $responseData['name']);
        $this->assertEquals($employeeType->description, $responseData['description']);
    }

    /**
     * Test the update method.
     */
    public function test_employee_type_update()
    {
        EmployeeTypeFactory::new()->create();
        $employeeType = EmployeeType::latest()->first(); // Get last updated

        $updatedData = [
            'name'                      => "test employee data",
            'description'               => $this->faker->sentence,
            'employee_type_category_id' => EmployeeTypeCategoryFactory::new()->create()->id,
            'contract_types'            => [ContractTypesFactory::new()->create()->id, ContractTypesFactory::new()->create()->id],
            'dimona_type_id'            => DimonaTypeFactory::new()->create()->id,
            'status'                    => 1
        ];

        $response = $this->put("/api/employee-types/{$employeeType->id}", $updatedData);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data'])
            ->assertJson(['success' => true]);

        // Validate the response data
        $responseData = $response->json('data');
        $this->assertEquals($updatedData['name'], $responseData['name']);
        $this->assertEquals($updatedData['description'], $responseData['description']);
    }

    /**
     * Test the destroy method.
     */
    public function test_employee_type_destroy()
    {
        $employeeType = EmployeeType::latest()->first();

        $response = $this->delete("/api/employee-types/{$employeeType->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message'])
            ->assertJson(['success' => true]);

        // Validate that the record has been deleted
        $this->assertNull(EmployeeType::find($employeeType->id));
    }

    public function test_employee_type_required_fields_store()
    {
        $invalidData = [];

        $response = $this->post('/api/employee-types', $invalidData);

        $response->assertStatus(422) // Unprocessable Entity
            ->assertJsonStructure(['success', 'message'])
            ->assertJson(['success' => false]);

        // Validate the response message
        $responseData = $response->json('message');
        $this->assertIsArray($responseData);
        $this->assertContains('Employee type name is required.', $responseData);
        $this->assertContains('The status field is required.', $responseData);
        $this->assertContains('The employee type category id field is required.', $responseData);
        $this->assertContains('The dimona type id field is required.', $responseData);
    }

    public function test_employee_type_with_invalid_data_store()
    {
        $invalidData = [
            'name'                      => 'normal employee',
            'description'               => $this->faker->sentence,
            'employee_type_category_id' => 't',
            'contract_types'            => 't',
            'dimona_type_id'            => 't',
            'status'                    => "t"
        ];

        $response = $this->post('/api/employee-types', $invalidData);

        $response->assertStatus(422) // Unprocessable Entity
            ->assertJsonStructure(['success', 'message'])
            ->assertJson(['success' => false]);

        // Validate the response message
        $responseData = $response->json('message');
        $this->assertIsArray($responseData);
        $this->assertContains('Status must be a boolean value.', $responseData);
        $this->assertContains('The employee type category id field must be an integer.', $responseData);
        $this->assertContains('The contract types field must be an array.', $responseData);
        $this->assertContains('The dimona type id field must be an integer.', $responseData);

        // Test contract_types validations
        $invalidData['contract_types'] = ["t", "t"];

        $response = $this->post('/api/employee-types', $invalidData);

        $response->assertStatus(422) // Unprocessable Entity
            ->assertJsonStructure(['success', 'message'])
            ->assertJson(['success' => false]);

        // Validate the response message
        $responseData = $response->json('message');
        $this->assertIsArray($responseData);
        $this->assertContains('The contract_types.0 field must be an integer.', $responseData);
        $this->assertContains('The contract_types.1 field must be an integer.', $responseData);
    }
}
