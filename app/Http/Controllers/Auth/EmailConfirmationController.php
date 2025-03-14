<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class EmailConfirmationController extends Controller
{
    public function confirm($token): RedirectResponse
    {
        Log::info('Token recibido: ' . ($token ?? 'No recibido'));

        if (!$token) {
            Log::warning('No se proporcionó token en la solicitud');
            return redirect('/login')->with('error', 'Falta el token de confirmación.');
        }

        $user = User::where('confirmation_token', $token)->first();
        Log::info('Usuario encontrado: ' . ($user ? $user->id : 'No encontrado'));

        if (!$user) {
            Log::warning('Token inválido o ya utilizado: ' . $token);
            return redirect('/login')->with('error', 'Token inválido o ya utilizado.');
        }

        $updated = $user->update([
            'is_confirmed' => true,
            'confirmation_token' => null,
        ]);
        Log::info('Usuario actualizado: ' . ($updated ? 'Sí' : 'No') . ' - ID: ' . $user->id);

        if ($updated) {
            Log::info('Valores después de actualizar: is_confirmed=' . $user->is_confirmed . ', confirmation_token=' . $user->confirmation_token);
        }

        return redirect('/login')->with('status', 'Correo confirmado. Ahora puedes iniciar sesión.');
    }
}
