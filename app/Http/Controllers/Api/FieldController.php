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
    $query = Field::with(['histories.user', 'agent']);

    // 1. Role-Based Access Control (RBAC)
    if ($request->user()->role !== 'admin') {
        $query->where('agent_id', $request->user()->id);
    } else {
        if ($request->has('agent_id') && $request->agent_id !== 'All Assignees') {
            $query->where('agent_id', $request->agent_id);
        }
    }

    // 2. Filter by Stage
    if ($request->has('stage') && $request->stage !== 'All Stages') {
        $query->where('current_stage', strtolower($request->stage));
    }

    // 3. Filter by Status (Translating Accessor Logic to SQL)
    if ($request->has('status') && $request->status !== 'All Statuses') {
        $status = strtolower($request->status);
        $threshold = now()->subDays(14);

        if ($status === 'completed') {
            // Completed: Stage must be harvested
            $query->where('current_stage', 'harvested');
            
        } elseif ($status === 'at risk') {
            // At Risk: Not harvested AND untouched for 14+ days
            $query->where('current_stage', '!=', 'harvested')
                  ->where('updated_at', '<', $threshold);
                  
        } elseif ($status === 'active') {
            // Active: Not harvested AND updated within the last 14 days
            $query->where('current_stage', '!=', 'harvested')
                  ->where('updated_at', '>=', $threshold);
        }
    }

    // 4. Paginate and Return
    $fields = $query->latest()->paginate(10);

    return response()->json($fields);
}

    // CREATE: Already implemented, kept for completeness
  public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'crop_type' => 'required|string|max:255',
            'planting_date' => 'required|date',
            'current_stage' => 'required|in:planted,growing,ready,harvested,preparation',
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
            'current_stage' => 'sometimes|in:planted,growing,ready,harvested,preparation',
            'notes' => 'sometimes|string|nullable',
            'agent_id' => 'sometimes|nullable|exists:users,id',
        ]);
    } else {
        // Field Agent Restriction: ONLY stage and notes allowed
        $validatedData = $request->validate([
            'current_stage' => 'required|in:planted,growing,ready,harvested,preparation',
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