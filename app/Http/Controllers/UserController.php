<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $vigiladores = User::where('role', 'vigilador')->get();
        return view('admin.vigiladores', compact('vigiladores'));
    }

    public function toggleSuspension(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        if ($user->role !== 'vigilador') {
            return redirect()->route('admin.vigiladores')->with('error', 'Solo se pueden suspender vigiladores.');
        }
        $user->update(['is_suspended' => !$user->is_suspended]);
        $status = $user->is_suspended ? 'suspendido' : 'reactivado';
        return redirect()->route('admin.vigiladores')->with('status', "Vigilador {$user->name} ha sido {$status}.");
    }
}
