<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Field;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_lifecycle_generates_correct_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // 1. TEST CREATION LOG
        $response = $this->postJson('/api/fields', [
            'name' => 'Alpha Field',
            'crop_type' => 'Maize',
            'planting_date' => '2026-04-20',
            'current_stage' => 'planted',
            'agent_id' => $admin->id
        ]);
        $field = Field::first();

        $this->assertDatabaseHas('field_histories', [
            'field_id' => $field->id,
            'field_changed' => 'created',
            'user_id' => $admin->id // Ensure log captured the admin
        ]);

        // 2. TEST UPDATE LOG (The "From -> To" check)
        $this->patchJson("/api/fields/{$field->id}", [
            'current_stage' => 'growing',
            'notes' => 'New observations'
        ]);

        $this->assertDatabaseHas('field_histories', [
            'field_id' => $field->id,
            'field_changed' => 'current_stage',
            'old_value' => 'planted',
            'new_value' => 'growing',
            'user_id' => $admin->id
        ]);

        // 3. TEST DELETION LOG
        $this->deleteJson("/api/fields/{$field->id}");

        $this->assertDatabaseHas('field_histories', [
            'field_id' => $field->id,
            'field_changed' => 'deleted',
            'user_id' => $admin->id
        ]);
    }
}