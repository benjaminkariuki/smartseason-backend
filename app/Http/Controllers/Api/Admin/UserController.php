<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Ensure only admins can access these methods
        $this->middleware(function ($request, $next) {
            if ($request->user()->role !== 'admin') {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        return User::where('role', 'field_agent')->get();
    }

public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'field_agent',
            'is_active' => true
        ]);
    }

    public function updateStatus(Request $request, User $user) {
        $request->validate(['is_active' => 'required|boolean']);
        
        $user->update(['is_active' => $request->is_active]);
        
        return response()->json(['message' => 'Status updated successfully']);
    }

    
}