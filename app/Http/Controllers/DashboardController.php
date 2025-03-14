<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $zona = $user->role === 'user' ? $user->zonas()->first() : null;
        $notifications = \App\Models\Notification::where('user_id', $user->id)->where('read', false)->get();
        $offers = $user->role === 'vigilador' ? Contrato::where('vigilador_id', $user->id)->where('status', 'offered')->get() : collect();
        $activeContracts = $user->role === 'vigilador' ? Contrato::where('vigilador_id', $user->id)->where('status', 'active')->get() : collect();
        $pendingContractsCount = $user->role === 'admin' ? Contrato::where('status', 'pending')->count() : 0;

        // Añadir contratos para usuarios (mostrar estado de sus zonas)
        $userContracts = $user->role === 'user' ? Contrato::whereHas('zona', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('zona', 'vigilador')->get() : collect();

        return view('dashboard', [
            'user' => $user,
            'zona' => $zona,
            'notifications' => $notifications->fresh(),
            'offers' => $offers,
            'activeContracts' => $activeContracts,
            'userContracts' => $userContracts, // Nueva variable para usuarios
            'role' => $user->role,
            'pendingContractsCount' => $pendingContractsCount,
        ])->with('status', session('status'));
    }
}
