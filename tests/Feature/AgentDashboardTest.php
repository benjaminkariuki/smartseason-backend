<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_sees_their_own_workload_and_priority_alerts(): void
    {
        $agent = User::factory()->create(['role' => 'field_agent']);
        $otherAgent = User::factory()->create(['role' => 'field_agent']);

        // Assigned to our agent, but At Risk
        Field::factory()->create([
            'agent_id' => $agent->id, 
            'current_stage' => 'growing', 
            'updated_at' => now()->subDays(20)
        ]);

        // Assigned to our agent, Active
        Field::factory()->create([
            'agent_id' => $agent->id, 
            'current_stage' => 'growing', 
            'updated_at' => now()
        ]);

        // Assigned to OTHER agent (Agent should NOT count this)
        Field::factory()->create(['agent_id' => $otherAgent->id]);

        $response = $this->actingAs($agent)->getJson('/api/agent/dashboard');

        $response->assertStatus(200)
                 ->assertJsonPath('work_queue.total_assigned', 2)
                 ->assertJsonCount(1, 'priority_alerts'); // Only the 'At Risk' one
    }
}