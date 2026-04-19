<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_list_agents(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $this->actingAs($admin)->postJson('/api/admin/agents', [
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'password' => 'password123'
        ])->assertStatus(201);

        $this->actingAs($admin)->getJson('/api/admin/agents')
             ->assertJsonFragment(['email' => 'john@test.com']);
    }

    public function test_deactivated_agent_cannot_login(): void
    {
        $agent = User::factory()->create([
            'role' => 'field_agent',
            'is_active' => false,
            'password' => bcrypt('password123')
        ]);

        $this->postJson('/api/login', [
            'email' => $agent->email,
            'password' => 'password123'
        ])->assertStatus(403); // Forbidden
    }

    public function test_admin_can_toggle_agent_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create(['role' => 'field_agent', 'is_active' => true]);

        $this->actingAs($admin)->patchJson("/api/admin/agents/{$agent->id}/status", [
            'is_active' => false
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $agent->id,
            'is_active' => false
        ]);
    }
}