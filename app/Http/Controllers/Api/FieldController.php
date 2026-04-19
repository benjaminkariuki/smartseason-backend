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
        if ($request->user()->role === 'admin') {
            return Field::all();
        }
        return Field::where('agent_id', $request->user()->id)->get();
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
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'crop_type' => 'sometimes|string|max:255',
            'planting_date' => 'sometimes|date',
            'current_stage' => 'sometimes|in:planted,growing,ready,harvested',
            // Changed from 'exists' to 'nullable|exists'
            'agent_id' => 'sometimes|nullable|exists:users,id', 
        ]);

        $field->update($validated);
        return response()->json($field);
    }
    // DELETE: Remove a field
    public function destroy(Field $field)
    {
        $field->delete();
        return response()->json(['message' => 'Field deleted successfully']);
    }
}