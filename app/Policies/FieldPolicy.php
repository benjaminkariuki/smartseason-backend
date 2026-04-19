<?php

namespace App\Policies;

use App\Models\Field;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FieldPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Both Admins and Agents can list fields
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Field $field): bool
    {
        // Admin can view anything; Agent can only view their assigned fields
        return $user->role === 'admin' || $user->id === $field->agent_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Field $field): bool
    {
        // Admin can update anything; Agent can only update their assigned fields
        return $user->role === 'admin' || $user->id === $field->agent_id;
    }
}