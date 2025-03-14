<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;
use App\Models\User;
use App\Models\Notification;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // public function updatePicture(Request $request)
    // {
    //     $request->validate([
    //         'profile_picture' => 'required|image|max:2048',
    //     ]);

    //     $user = Auth::user();
    //     if ($request->hasFile('profile_picture')) {
    //         if ($user->profile_picture) {
    //             Storage::delete('public/profiles/' . $user->profile_picture);
    //         }
    //         $path = $request->file('profile_picture')->store('profiles', 'public');
    //         $user->update(['profile_picture' => basename($path)]);
    //     }

    //     return redirect('/profile')->with('status', 'Foto de perfil actualizada.');
    // }

    public function uploadDocument(Request $request)
{
    $request->validate([
        'document' => 'required|file|mimes:pdf,jpg,png|max:2048',
        'type' => 'required|in:DNI,Comprobante de dirección',
    ]);

    $user = Auth::user();
    $path = $request->file('document')->store('documents', 'public');
    $document = Document::create([
        'user_id' => $user->id,
        'file_path' => basename($path),
        'type' => $request->type,
    ]);

    // Notificar a todos los administradores
    $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        Notification::create([
            'user_id' => $admin->id,
            'message' => "El usuario {$user->name} ha subido un documento ({$document->type}) para verificación. Documento ID: {$document->id}",
            'read' => false,
        ]);
    }

    return redirect('/profile')->with('status', 'Documento subido. Espera la verificación del administrador.');
}
}
