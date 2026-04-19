<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_field_and_assign_to_agent(): void
    {
        // 1. Arrange: Create an Admin and an Agent
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create(['role' => 'field_agent']);

        // 2. Act: Send request to create a field
        $this->actingAs($admin)->postJson('/api/fields', [
            'name' => 'North Ridge',
            'crop_type' => 'Maize',
            'planting_date' => '2026-05-01',
            'current_stage' => 'planted',
            'agent_id' => $agent->id,
        ]);

        // 3. Assert: Check if it exists in the database
        $this->assertDatabaseHas('fields', [
            'name' => 'North Ridge',
            'agent_id' => $agent->id,
        ]);
    }


    public function test_admin_can_list_all_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        \App\Models\Field::factory()->count(3)->create();

        $response = $this->actingAs($admin)->getJson('/api/fields');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_agent_can_only_see_assigned_fields(): void
    {
        $agent1 = User::factory()->create(['role' => 'field_agent']);
        $agent2 = User::factory()->create(['role' => 'field_agent']);
        
        \App\Models\Field::factory()->create(['agent_id' => $agent1->id]);
        \App\Models\Field::factory()->create(['agent_id' => $agent2->id]);

        $response = $this->actingAs($agent1)->getJson('/api/fields');

        $response->assertStatus(200)
                 ->assertJsonCount(1); // Should only see their own field
    }

    public function test_admin_can_delete_a_field(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $field = \App\Models\Field::factory()->create();

        $this->actingAs($admin)->deleteJson("/api/fields/{$field->id}")
             ->assertStatus(200);

        $this->assertDatabaseMissing('fields', ['id' => $field->id]);
    }

    public function test_admin_can_create_field_without_assigning_agent(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->postJson('/api/fields', [
            'name' => 'Unassigned Field',
            'crop_type' => 'Maize',
            'planting_date' => '2026-06-01',
            'current_stage' => 'planted',
            // No agent_id sent
        ])->assertStatus(201);

        $this->assertDatabaseHas('fields', [
            'name' => 'Unassigned Field',
            'agent_id' => null, // Verify it saved as null
        ]);
    }

    public function test_agent_cannot_edit_restricted_fields(): void
{
    // Arrange: Create agent and a field
    $agent = User::factory()->create(['role' => 'field_agent']);
    $field = \App\Models\Field::factory()->create([
        'agent_id' => $agent->id,
        'name' => 'Original Name',
        'crop_type' => 'Original Crop'
    ]);

    // Act: Agent attempts to change name and crop_type
    $this->actingAs($agent)->patchJson("/api/fields/{$field->id}", [
        'name' => 'Hacked Name',
        'crop_type' => 'Hacked Crop',
        'current_stage' => 'growing', // Allowed
        'notes' => 'Updated notes'     // Allowed
    ]);

    // Assert: Name and Crop Type should NOT have changed
    $this->assertDatabaseHas('fields', [
        'id' => $field->id,
        'name' => 'Original Name',
        'crop_type' => 'Original Crop',
        'current_stage' => 'growing',
        'notes' => 'Updated notes'
    ]);
}

}