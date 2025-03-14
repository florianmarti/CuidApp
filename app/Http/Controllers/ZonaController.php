<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;
use App\Notifications\VigiladorAsignado;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Contrato;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class ZonaController extends Controller
{
    public function create()
{
    $user = \Auth::user();
    if ($user->role !== 'user') {
        return redirect('/dashboard')->with('error', 'Solo los usuarios pueden crear zonas.');
    }
    if ($user->zonas()->exists()) {
        return redirect('/dashboard')->with('error', 'Ya has creado una zona. Solo puedes tener una zona activa.');
    }
    return view('zonas.create');
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Zona::create([
            'name' => $request->name,
            'user_id' => \Auth::user()->id,
        ]);

        return redirect('/dashboard')->with('status', 'Zona creada exitosamente.');
    }

    public function contract()
    {
        return view('zonas.contract');
    }
    public function manageProfilePictures(Request $request)
    {
        $search = $request->input('search');
        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->get();

        return view('admin.manage-profile-pictures', compact('users'));
    }
    public function edit()
    {
    $zona = \Auth::user()->zonas()->first();
    return view('zonas.edit', ['zona' => $zona]);
    }

    public function storeContract(Request $request)
{
    $user = \Auth::user();
    $zona = $user->zonas()->first();

    $request->validate([
        'type' => ['required', 'in:Diurno,Nocturno'],
    ]);

    // Verificar si la zona ya tiene un contrato pendiente o activo
    if ($zona->contratos()->whereIn('status', ['pending', 'active'])->exists()) {
        return redirect('/dashboard')->with('error', 'Tu zona ya tiene una solicitud pendiente o un vigilador activo asignado.');
    }

    $contrato = \App\Models\Contrato::create([
        'zona_id' => $zona->id,
        'vigilador_id' => null,
        'start_date' => null,
        'end_date' => null,
        'type' => $request->type,
        'status' => 'pending',
    ]);

    Notification::create([
        'user_id' => 10,
        'contrato_id' => $contrato->id,
        'message' => "El usuario {$user->name} ha solicitado un vigilador para la zona: {$zona->name} (Turno: {$request->type}).",
        'read' => false,
    ]);

    return redirect('/dashboard')->with('status', 'Solicitud enviada al administrador.');
}

    public function update(Request $request)
    {
        $zona = \Auth::user()->zonas()->first();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $zona->update([
            'name' => $request->name,
        ]);

        return redirect('/dashboard')->with('status', 'Zona actualizada exitosamente.');
    }

    public function adminContratos(Request $request)
{
    $contratos = Contrato::with('zona', 'vigilador')->get()->groupBy('status');

    return view('admin.contratos', [
        'contratosPendientes' => $contratos['pending'] ?? collect(),
        'contratosOfertados' => $contratos['offered'] ?? collect(),
        'contratosActivos' => $contratos['active'] ?? collect(),
        'contratosFinalizados' => $contratos['finished'] ?? collect(),
        'contratosRechazados' => $contratos['rejected'] ?? collect(),
    ]);
}

    public function assignVigilador($contrato)
    {
        $contrato = \App\Models\Contrato::findOrFail($contrato);
        $vigiladores = \App\Models\User::where('role', 'vigilador')
                                       ->whereDoesntHave('contratos', function ($query) {
                                           $query->where('status', 'active');
                                       })->get();
        return view('admin.assign', ['contrato' => $contrato, 'vigiladores' => $vigiladores]);
    }

    public function storeVigilador(Request $request, $contratoId)
    {
        $request->validate([
            'vigilador_id' => 'required|exists:users,id',
            'type' => 'required|string|in:Diurno,Nocturno',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $contrato = Contrato::findOrFail($contratoId);
        $vigilador = User::findOrFail($request->input('vigilador_id'));
        $contrato->update([
            'vigilador_id' => $vigilador->id,
            'type' => $request->input('type'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => 'offered',
        ]);

        Notification::create([
            'user_id' => $vigilador->id,
            'contrato_id' => $contrato->id,
            'message' => "Te han ofrecido la zona: {$contrato->zona->name} (Turno: {$contrato->type}) desde {$contrato->start_date->toDateString()} hasta {$contrato->end_date->toDateString()}. Acepta o rechaza en tu dashboard.",
            'read' => false,
        ]);

        return redirect('/dashboard')->with('status', 'Oferta enviada al vigilador.');
    }



    public function offer($zona)
    {
        $zona = \App\Models\Zona::findOrFail($zona);
        // Filtrar vigiladores que no tienen contratos activos
        $vigiladores = \App\Models\User::where('role', 'vigilador')
                                    ->whereDoesntHave('contratos', function ($query) {
                                        $query->where('status', 'active');
                                    })->get();
        return view('admin.offer', compact('zona', 'vigiladores'));
    }

    public function storeOffer(Request $request, $zonaId)
{
    \Log::info("Iniciando storeOffer para zona_id: {$zonaId}");
    \Log::info("Datos recibidos: " . json_encode($request->all()));

    $zona = Zona::findOrFail($zonaId);
    $vigilador = User::findOrFail($request->input('vigilador_id'));

    if ($vigilador->contratos()->where('status', 'active')->exists()) {
        \Log::info("Vigilador {$vigilador->id} ya tiene un contrato activo.");
        return redirect('/admin/dashboard')->with('error', 'El vigilador seleccionado ya tiene un contrato activo.');
    }

    // Buscar contrato existente (pending o offered)
    $contrato = $zona->contratos()->whereIn('status', ['pending', 'offered'])->first();
    if ($contrato) {
        // Actualizar el contrato existente
        $contrato->update([
            'vigilador_id' => $vigilador->id,
            'type' => $request->input('type'),
            'status' => 'offered',
        ]);
        \Log::info("Contrato actualizado: id {$contrato->id}, status 'offered', vigilador_id {$vigilador->id}");
    } else {
        // Crear nuevo contrato si no existe
        $contrato = Contrato::create([
            'zona_id' => $zona->id,
            'vigilador_id' => $vigilador->id,
            'type' => $request->input('type'),
            'status' => 'offered',
        ]);
        \Log::info("Contrato creado: id {$contrato->id}, status 'offered', vigilador_id {$vigilador->id}");
    }

    // Eliminar notificaciones previas para este vigilador y zona
    Notification::where('user_id', $vigilador->id)
        ->where('contrato_id', $contrato->id)
        ->delete();

    // Crear nueva notificación
    Notification::create([
        'user_id' => $vigilador->id,
        'contrato_id' => $contrato->id,
        'message' => "Te han ofrecido la zona: {$zona->name} (Turno: {$request->input('type')}). Acepta o rechaza en tu dashboard.",
        'read' => false,
    ]);
    \Log::info("Notificación creada para vigilador {$vigilador->id}: Te han ofrecido la zona: {$zona->name}");

    \Log::info("Estableciendo mensaje de sesión: Oferta enviada al vigilador.");
    return redirect('/admin/dashboard')->with('status', 'Oferta enviada al vigilador.');
}



public function acceptOffer($contratoId)
{
    $contrato = Contrato::findOrFail($contratoId);

    if (!Auth::check()) {
        return redirect('/login')->with('error', 'Debes estar autenticado para aceptar una oferta.');
    }

    // Eliminar notificaciones por contrato_id
    $deletedById = Notification::where('user_id', Auth::id())->where('contrato_id', $contrato->id)->delete();
    \Log::info("Notificaciones eliminadas por contrato_id {$contrato->id}: " . $deletedById);

    // Eliminar notificaciones por mensaje (como respaldo)
    $message = "Te han ofrecido la zona: {$contrato->zona->name} (Turno: {$contrato->type}) desde {$contrato->start_date} hasta {$contrato->end_date}. Acepta o rechaza en tu dashboard.";
    $deletedByMessage = Notification::where('user_id', Auth::id())->where('message', $message)->delete();
    \Log::info("Notificaciones eliminadas por mensaje: " . $deletedByMessage);

    $contrato->update(['status' => 'active']);

    if ($contrato->zona && $contrato->zona->user) {
        Notification::create([
            'user_id' => $contrato->zona->user->id,
            'contrato_id' => $contrato->id,
            'message' => "El vigilador " . Auth::user()->name . " ha aceptado tu solicitud para la zona: {$contrato->zona->name} (Turno: {$contrato->type}) desde " . $contrato->start_date->toDateString() . " hasta " . $contrato->end_date->toDateString() . ".",
            'read' => false,
        ]);
    }

    $admin = User::where('role', 'admin')->first();
    if ($admin) {
        Notification::create([
            'user_id' => $admin->id,
            'contrato_id' => $contrato->id,
            'message' => "El vigilador " . Auth::user()->name . " ha aceptado la oferta para la zona: {$contrato->zona->name} (Turno: {$contrato->type}) desde " . $contrato->start_date->toDateString() . " hasta " . $contrato->end_date->toDateString() . ".",
            'read' => false,
        ]);
    }

    return redirect('/dashboard')->with('status', 'Oferta aceptada exitosamente.');
}


public function rejectOffer($contratoId)
{
    $contrato = Contrato::findOrFail($contratoId);
    $zona = $contrato->zona;

    // Eliminar notificaciones por contrato_id
    $deletedById = Notification::where('user_id', Auth::id())->where('contrato_id', $contrato->id)->delete();
    \Log::info("Notificaciones eliminadas por contrato_id {$contrato->id}: " . $deletedById);

    // Eliminar notificaciones por mensaje (como respaldo)
    $message = "Te han ofrecido la zona: {$zona->name} (Turno: {$contrato->type}) desde {$contrato->start_date} hasta {$contrato->end_date}. Acepta o rechaza en tu dashboard.";
    $deletedByMessage = Notification::where('user_id', Auth::id())->where('message', $message)->delete();
    \Log::info("Notificaciones eliminadas por mensaje: " . $deletedByMessage);

    // Eliminar el contrato rechazado
    $contrato->delete();
    \Log::info("Contrato rechazado y eliminado: id {$contrato->id}");

    // Crear un nuevo contrato pendiente
    $newContrato = Contrato::create([
        'zona_id' => $zona->id,
        'user_id' => $zona->user_id,
        'type' => $contrato->type,
        'status' => 'pending',
    ]);
    \Log::info("Nuevo contrato pendiente creado: id {$newContrato->id}, zona_id {$zona->id}");

    // Notificar al Admin
    Notification::create([
        'user_id' => User::where('role', 'admin')->first()->id,
        'contrato_id' => $newContrato->id,
        'message' => "El vigilador " . Auth::user()->name . " ha rechazado la oferta para la zona: {$zona->name} (Turno: {$contrato->type}).",
        'read' => false,
    ]);

    return redirect('/dashboard')->with('status', 'Oferta rechazada exitosamente.');
}

public function toggleWorking(Request $request, $contratoId)
{
    \Log::info("ToggleWorking called for contrato_id: {$contratoId}");
    $contrato = Contrato::findOrFail($contratoId);

    // Validación de permisos
    if ($contrato->vigilador_id !== Auth::id() || $contrato->status !== 'active') {
        \Log::warning("Permiso denegado para contrato_id: {$contratoId}");
        return redirect('/dashboard')->with('error', 'No tienes permiso para modificar este contrato.');
    }

    \Log::info("Request data: " . json_encode($request->all()));
    $isWorking = $request->has('is_working'); // true si el checkbox está marcado
    $previousState = $contrato->is_working;
    \Log::info("Valor calculado de is_working: " . ($isWorking ? 'true' : 'false'));
    \Log::info("Estado anterior de is_working: " . ($previousState ? 'true' : 'false'));

    if ($isWorking !== $previousState) {
        // Actualizar el estado
        $updateResult = $contrato->update(['is_working' => $isWorking]);
        \Log::info("Resultado de update: " . ($updateResult ? 'éxito' : 'fallo'));

        // Recargar el modelo para confirmar
        $contrato->refresh();
        \Log::info("Estado actualizado a is_working: " . ($contrato->is_working ? 'true' : 'false'));

        // Mensaje y notificaciones
        $status = $isWorking ? 'activo' : 'inactivo';
        $message = $isWorking ? 'Has marcado que estás en la zona.' : 'Has marcado que no estás en la zona.';

        // Notificación al admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id' => $admin->id,
                'contrato_id' => $contrato->id,
                'message' => "El vigilador {$contrato->vigilador->name} está {$status} en la zona {$contrato->zona->name}.",
                'read' => false,
            ]);
            \Log::info("Notificación enviada al admin.");
        }

        // Notificación al usuario de la zona
        if ($contrato->zona && $contrato->zona->user) {
            Notification::create([
                'user_id' => $contrato->zona->user->id,
                'contrato_id' => $contrato->id,
                'message' => "{$contrato->vigilador->name} está {$status} en su zona {$contrato->zona->name}.",
                'read' => false,
            ]);
            \Log::info("Notificación enviada al usuario.");
        }

        return redirect('/dashboard')->with('status', $message);
    }

    \Log::info("Estado no cambió, no se actualizó.");
    return redirect('/dashboard')->with('status', 'El estado no ha cambiado.');
}
public function adminDashboard()
{
    $zonas = Zona::whereDoesntHave('contratos', function ($query) {
        $query->whereIn('status', ['pending', 'offered', 'active']);
    })->with('user')->get();
    $notifications = Notification::where('user_id', Auth::id())->where('read', false)->get();

    // Extraer documentos relacionados de las notificaciones
    $documentIds = $notifications->map(function ($notification) {
        if (preg_match('/Documento ID: (\d+)/', $notification->message, $matches)) {
            return (int)$matches[1];
        }
        return null;
    })->filter()->all();

    $documents = Document::whereIn('id', $documentIds)->get()->keyBy('id');



    return view('admin.dashboard', compact('zonas', 'notifications', 'documents'));
}
public function verifyDocuments(Request $request)
{
    $search = $request->input('search');
    $documents = Document::where('verified', false)
        ->with('user')
        ->when($search, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
        ->get();

    $usersWithPendingPhone = User::where('phone_verified', false)
        ->when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->get();

    return view('admin.verify-documents', compact('documents', 'usersWithPendingPhone'));
}
public function approvePhone($userId)
{
    $user = User::findOrFail($userId);
    $user->update(['phone_verified' => true]);
    Notification::create([
        'user_id' => $user->id,
        'message' => "Tu número de teléfono {$user->phone_number} ha sido verificado.",
        'read' => false,
    ]);
    return redirect()->route('admin.verify-documents')->with('status', 'Número de teléfono aprobado.');
}
public function approveDocument(Request $request, $documentId)
{
    // Buscar el documento
    $document = Document::findOrFail($documentId);

    // Verificar que no esté ya aprobado
    if ($document->verified) {
        return redirect()->route('admin.verify-documents')->with('error', 'El documento ya está aprobado.');
    }

    // Actualizar el estado del documento
    $document->update(['verified' => true]);

    // Notificar al usuario
    Notification::create([
        'user_id' => $document->user_id,
        'message' => "Tu documento ID: {$document->id} ha sido aprobado por el administrador.",
        'read' => false,
    ]);

    return redirect()->route('admin.verify-documents')->with('status', 'Documento aprobado exitosamente.');
}

public function rejectPhone($userId)
{
    $user = User::findOrFail($userId);
    $user->update(['phone_number' => null, 'phone_verified' => false]);
    Notification::create([
        'user_id' => $user->id,
        'message' => "Tu número de teléfono fue rechazado. Por favor, contáctate con el administrador.",
        'read' => false,
    ]);
    return redirect()->route('admin.verify-documents')->with('status', 'Número de teléfono rechazado.');
}
public function addPhone(Request $request, $userId)
{
    $request->validate([
        'phone_number' => ['required', 'string', 'max:255', 'regex:/^\d{10}$/', 'unique:users'],
    ]);

    $user = User::findOrFail($userId);
    $user->update([
        'phone_number' => $request->phone_number,
        'phone_verified' => true, // Se verifica directamente al ingresarlo manualmente
    ]);

    Notification::create([
        'user_id' => $user->id,
        'message' => "Tu nuevo número de teléfono {$user->phone_number} ha sido agregado y verificado por el administrador.",
        'read' => false,
    ]);

    return redirect()->route('admin.verify-documents')->with('status', 'Número de teléfono agregado y verificado.');
}
public function vigiladores()
    {
        $vigiladores = User::where('role', 'vigilador')->get();
        return view('admin.vigiladores', compact('vigiladores'));
    }

    public function inactivateVigilador($userId)
{
    $user = User::findOrFail($userId);

    \Log::info("Inactivando vigilador: {$user->name}, is_inactive actual: {$user->is_inactive}");

    if ($user->is_inactive) {
        return redirect()->route('admin.vigiladores')->with('error', "El vigilador {$user->name} ya está inactivo.");
    }

    $updated = $user->update([
        'is_inactive' => true,
        'is_suspended' => false,
    ]);

    \Log::info("Actualización is_inactive: " . ($updated ? 'Sí' : 'No'));

    if (!$updated) {
        return redirect()->route('admin.vigiladores')->with('error', "Error al inactivar al vigilador {$user->name}.");
    }

    Notification::create([
        'user_id' => $user->id,
        'message' => "Tu cuenta ha sido inactivada permanentemente debido a múltiples rechazos o incumplimiento de reglas. Contacta al administrador para más información.",
        'read' => false,
    ]);

    return redirect()->route('admin.vigiladores')->with('status', "Vigilador {$user->name} ha sido inactivado permanentemente.");
}

    public function toggleVigiladorStatus(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        if ($user->is_inactive) {
            return redirect()->route('admin.vigiladores')->with('error', "No puedes modificar el estado de un vigilador inactivo.");
        }

        $newStatus = !$user->is_suspended;
        $updated = $user->update(['is_suspended' => $newStatus]);

        if (!$updated) {
            return redirect()->route('admin.vigiladores')->with('error', 'Error al actualizar el estado del vigilador.');
        }

        if ($newStatus) {
            Notification::create([
                'user_id' => $user->id,
                'message' => "Tu cuenta ha sido suspendida temporalmente. En breve solucionaremos este inconveniente.",
                'read' => false,
            ]);
            $message = "Vigilador {$user->name} ha sido suspendido.";
        } else {
            Notification::create([
                'user_id' => $user->id,
                'message' => "Tu cuenta ha sido reactivada por el administrador. Ya puedes continuar usando Cuidapp.",
                'read' => false,
            ]);
            $message = "Vigilador {$user->name} ha sido reactivado.";
        }

        return redirect()->route('admin.vigiladores')->with('status', $message);
    }
}

