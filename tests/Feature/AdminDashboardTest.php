<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard_stats(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create 3 fields: 1 Active, 1 At Risk (Old), 1 Completed
        Field::factory()->create(['current_stage' => 'growing', 'updated_at' => now()]);
        Field::factory()->create(['current_stage' => 'growing', 'updated_at' => now()->subDays(20)]);
        Field::factory()->create(['current_stage' => 'harvested']);

        $response = $this->actingAs($admin)->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
                 ->assertJsonStructure(['kpi' => ['total_fields', 'status_breakdown'], 'risk_table'])
                 ->assertJsonPath('kpi.total_fields', 3)
                 ->assertJsonPath('kpi.status_breakdown.At Risk', 1);
    }

    public function test_agent_cannot_access_admin_dashboard(): void
    {
        $agent = User::factory()->create(['role' => 'field_agent']);

        $this->actingAs($agent)->getJson('/api/admin/dashboard')
             ->assertStatus(403);
    }
}