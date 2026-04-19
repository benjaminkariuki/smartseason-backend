<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    // READ: List fields (filtered by role)
    public function index(Request $request)
{
    // Eager load the history and the user who performed the action
    $query = Field::with(['histories.user', 'agent']);

    if ($request->user()->role === 'admin') {
        // Admin views all [cite: 30-31]
        return $query->get();
    }

    // Agent views only assigned fields [cite: 49]
    return $query->where('agent_id', $request->user()->id)->get();
}

    // CREATE: Already implemented, kept for completeness
  public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'crop_type' => 'required|string|max:255',
            'planting_date' => 'required|date',
            'current_stage' => 'required|in:planted,growing,ready,harvested',
            // Changed from 'required' to 'nullable'
            'agent_id' => 'nullable|exists:users,id', 
        ]);

        return Field::create($validated);
    }

    // UPDATE: Edit field info AND reassign agent
public function update(Request $request, Field $field)
{
    // 1. Gatekeeper: Does the user have permission to access this record at all?
    $this->authorize('update', $field);

    // 2. Role-Based Validation: 
    // Define allowed fields based on role.
    if ($request->user()->role === 'admin') {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'crop_type' => 'sometimes|string|max:255',
            'planting_date' => 'sometimes|date',
            'current_stage' => 'sometimes|in:planted,growing,ready,harvested',
            'notes' => 'sometimes|string|nullable',
            'agent_id' => 'sometimes|nullable|exists:users,id',
        ]);
    } else {
        // Field Agent Restriction: ONLY stage and notes allowed
        $validatedData = $request->validate([
            'current_stage' => 'required|in:planted,growing,ready,harvested',
            'notes' => 'nullable|string',
        ]);
    }

    // 3. Update only the validated fields
    $field->update($validatedData);

    return response()->json([
        'message' => 'Field updated successfully',
        'data' => $field
    ]);
}
    // DELETE: Remove a field
    public function destroy(Field $field)
    {
        $field->delete();
        return response()->json(['message' => 'Field deleted successfully']);
    }
}