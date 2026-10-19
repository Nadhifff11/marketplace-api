<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(
            User::latest()->get(['id', 'name', 'email', 'role', 'created_at'])
        );
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Tidak bisa menghapus admin',
            ], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User dihapus']);
    }
}