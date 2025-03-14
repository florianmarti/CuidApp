<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        // Verificar que el usuario esté autenticado y que la notificación le pertenezca
        if (!Auth::check() || $notification->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para marcar esta notificación.');
        }
        $notification->update(['read' => true]);
        return redirect()->back()->with('status', 'Notificación marcada como leída.');
    }
}
