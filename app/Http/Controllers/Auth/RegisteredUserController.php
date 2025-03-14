<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Notifications\ConfirmEmail;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
{
    try {
        // Validar los datos
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:users'],
            'role' => ['required', 'in:user,vigilador'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $token = Str::random(60);

        // Formatear el teléfono si es necesario (opcional)
        $phone_number = preg_replace('/[^0-9]/', '', $request->phone_number); // Solo números
        $phone_number = "(".substr($phone_number, 0, 3).") ".substr($phone_number, 3, 3)." ".substr($phone_number, 6, 4);

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone_number' => $phone_number,
            'phone_verified' => false,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'confirmation_token' => $token,
            'is_confirmed' => false,
        ]);

        // Notificación al admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $user->notifications()->create([
                'user_id' => $admin->id,
                'message' => "El usuario {$user->name} se ha registrado con el número de teléfono: {$user->phone_number}. Verifícalo.",
                'read' => false,
                'notifiable_id' => $user->id,
                'notifiable_type' => User::class,
            ]);
        }

        // Enviar correo de confirmación
        $user->notify(new ConfirmEmail($token));

        return redirect('/login')->with('status', 'Revisa tu correo para confirmar tu cuenta.');
    } catch (\Exception $e) {
        \Log::error('Error en registro de usuario: ' . $e->getMessage());
        return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
    }
}
}
