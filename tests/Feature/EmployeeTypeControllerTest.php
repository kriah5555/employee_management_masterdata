<?php

namespace Tests\Feature;


use App\Models\EmployeeType\EmployeeType;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Database\Factories\EmployeeTypeCategoryFactory;
use Database\Factories\ContractRenewalFactory;
use Database\Factories\ContractTypesFactory;
use Database\Factories\EmployeetypeFactory;
use Database\Factories\EmployeeTypeContractFactory;
use App\Models\EmployeeType\EmployeeTypeCategory;
use App\Models\Contracts\ContractTypes;
use App\Models\Contracts\ContractRenewal;


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
            'name' => "test employee data",
            'description' => $this->faker->sentence,
            'employee_type_categories_id' => EmployeeTypeCategoryFactory::new()->create()->id,
            'contract_type_id' => ContractTypesFactory::new()->create()->id,
            'contract_renewal_id' => ContractRenewalFactory::new()->create()->id,
            'status' => 1
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

    public function test_show()
    {
        EmployeeTypeContractFactory::new()->create();
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
    public function test_update()
    {
        EmployeeTypeContractFactory::new()->create();
        $employeeType = EmployeeType::orderBy('updated_at', 'desc')->first(); // Get last updated

        $updatedData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'employee_type_categories_id' => EmployeeTypeCategoryFactory::new()->create()->id,
            'contract_type_id' => ContractTypesFactory::new()->create()->id,
            'contract_renewal_id' => ContractRenewalFactory::new()->create()->id,
            'status' => 1
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
        EmployeeTypeContractFactory::new()->create();
        $employeeType = EmployeeType::latest()->first();

        $response = $this->delete("/api/employee-types/{$employeeType->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message'])
            ->assertJson(['success' => true]);

        // Validate that the record has been deleted
        $this->assertNull(EmployeeType::find($employeeType->id));
    }

    public function test_store_required_fields()
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
        $this->assertContains('The employee type categories id field is required.', $responseData);
        $this->assertContains('The contract type id field is required.', $responseData);
        $this->assertContains('The contract renewal id field is required.', $responseData);
    }

    public function test_store_with_invalid_data()
    {
        $invalidData = [
            'name'                        => 'normal employee',
            'description'                 => $this->faker->sentence,
            'employee_type_categories_id' => 't',
            'contract_type_id'            => 't',
            'contract_renewal_id'         => 't',
            'status'                      => "t"
        ];

        $response = $this->post('/api/employee-types', $invalidData);

        $response->assertStatus(422) // Unprocessable Entity
            ->assertJsonStructure(['success', 'message'])
            ->assertJson(['success' => false]);

        // Validate the response message
        $responseData = $response->json('message');
        $this->assertIsArray($responseData);
        $this->assertContains('Status must be a boolean value.', $responseData);
        $this->assertContains('The employee type categories id field must be an integer.', $responseData);
        $this->assertContains('The contract type id field must be an integer.', $responseData);
        $this->assertContains('The contract renewal id field must be an integer.', $responseData);



        $invalidData = [
            'name'                        => 'normal employee',
            'description'                 => $this->faker->sentence,
            'employee_type_categories_id' => EmployeeTypeCategory::latest()->first()->id + 10,
            'contract_type_id'            => ContractTypes::latest()->first()->id + 10,
            'contract_renewal_id'         => ContractRenewal::latest()->first()->id + 10,
            'status'                      => 99
        ];

        $response = $this->post('/api/employee-types', $invalidData);

        $response->assertStatus(422) // Unprocessable Entity
            ->assertJsonStructure(['success', 'message'])
            ->assertJson(['success' => false]);

        // Validate the response message
        $responseData = $response->json('message');
        $this->assertIsArray($responseData);
        $this->assertContains('The selected employee type categories id is invalid.', $responseData);
        $this->assertContains('The selected contract type id is invalid.', $responseData);
        $this->assertContains('The selected contract renewal id is invalid.', $responseData);
        $this->assertContains('Status must be a boolean value.', $responseData);

    }
}   