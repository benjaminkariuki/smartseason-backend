<?php

namespace Tests\Feature;

use App\Models\User; // Make sure to import the User model
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase; // This ensures the database is reset after each test

    public function test_can_create_admin_user(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertEquals('admin', $user->role);
    }

    public function test_can_create_field_agent_user(): void
    {
        $user = User::factory()->create(['role' => 'field_agent']);

        $this->assertEquals('field_agent', $user->role);
    }
}