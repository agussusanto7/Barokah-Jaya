<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'created_at')
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        return response()->json([
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->makeHidden(['password', 'remember_token']),
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = User::with(['transactions' => function ($query) {
            $query->latest()->limit(5);
        }])->findOrFail($id);

        return response()->json([
            'user' => $user->makeHidden(['password', 'remember_token']),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = $request->only('name', 'email');

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->makeHidden(['password', 'remember_token']),
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Check if user has transactions
        if ($user->transactions()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete user with existing transactions',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}