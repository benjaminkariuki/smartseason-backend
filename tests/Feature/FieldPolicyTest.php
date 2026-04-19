<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_cannot_update_field_assigned_to_someone_else(): void
    {
        $agent1 = User::factory()->create(['role' => 'field_agent']);
        $agent2 = User::factory()->create(['role' => 'field_agent']);
        
        $field = Field::factory()->create(['agent_id' => $agent1->id]);

        // Attempt to update as agent2
        $this->actingAs($agent2);
        
        $response = $this->patchJson("/api/fields/{$field->id}", [
            'current_stage' => 'harvested'
        ]);

        $response->assertStatus(403); // Forbidden
    }
}