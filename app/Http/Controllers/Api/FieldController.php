<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function update(Request $request, Field $field)
    {
        // This triggers the FieldPolicy 'update' method.
        // If it returns false, it automatically throws a 403 Forbidden exception.
        $this->authorize('update', $field);

        // Logic for updating the field goes here
        $request->validate([
            'current_stage' => 'required|in:planted,growing,ready,harvested',
        ]);

        $field->update($request->only('current_stage'));

        return response()->json(['message' => 'Field updated successfully', 'data' => $field]);
    }
}