<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Log;

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

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'field_agent',
            'is_active' => true
        ]);

        // Send a simple HTML email directly from the controller
        try {
            $emailContent = "
                <h3>Welcome to the Team, {$user->name}!</h3>
                <p>An administrator has successfully provisioned your account.</p>
                <p><strong>Your Login Credentials:</strong></p>
                <ul>
                    <li><strong>Email:</strong> {$user->email}</li>
                    <li><strong>Password:</strong> {$data['password']}</li>
                </ul>
                <p><em>Please log in and change this password immediately.</em></p>
            ";

            Mail::html($emailContent, function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Welcome to Digital Agronomist - Your Account Credentials');
            });

        } catch (\Exception $e) {
            // Log the error but don't break the user creation if Zoho is slow
            Log::error('Failed to send raw password email to ' . $user->email . ': ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'User created successfully and email sent', 
            'user' => $user
        ], 201);
    }

    public function getActiveAgents()
{
    $agents = User::where('role', 'field_agent')
                              ->where('is_active', true)
                              ->select('id', 'name', 'email') // Only grab what the UI needs
                              ->get();
                              
    return response()->json($agents);
}

   public function updateStatus(Request $request, User $user) {
        // Just flip whatever the current value is in the database!
        $user->update([
            'is_active' => !$user->is_active 
        ]);
        
        return response()->json([
            'message' => 'Status updated successfully',
            'is_active' => $user->is_active // Send back the new status
        ]);
    }

    public function getAllUsers()
    {
        return User::orderBy('created_at', 'desc')->get();
    }

    
}