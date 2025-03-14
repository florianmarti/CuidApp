<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rutas relacionadas con el Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'EnsureEmailIsConfirmed'])
    ->name('dashboard');

// Rutas de confirmación de email
Route::get('/confirm-email/{token}', [App\Http\Controllers\Auth\EmailConfirmationController::class, 'confirm'])
    ->name('email.confirm');

// Rutas para usuarios autenticados (Breeze + Cuidapp)
Route::middleware(['auth', 'EnsureEmailIsConfirmed', 'not.inactive'])->group(function () {
    // Perfil de Breeze adaptado
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Funcionalidades de Cuidapp
    // Route::post('/profile/picture', [ProfileController::class, 'updatePicture'])->name('profile.picture');
    Route::post('/profile/document', [ProfileController::class, 'uploadDocument'])->name('profile.document');

    // Gestión de zonas (usuarios)
    Route::get('/zonas/create', [ZonaController::class, 'create'])->name('zonas.create');
    Route::post('/zonas', [ZonaController::class, 'store'])->name('zonas.store');
    Route::get('/zonas/edit', [ZonaController::class, 'edit'])->name('zonas.edit');
    Route::patch('/zonas', [ZonaController::class, 'update'])->name('zonas.update');

    // Solicitud de contratos (usuarios)
    Route::get('/zonas/contract', [ZonaController::class, 'contract'])->name('zonas.contract');
    Route::post('/zonas/contract', [ZonaController::class, 'storeContract'])->name('zonas.contract.store');

    // Acciones de vigiladores (aceptar/rechazar ofertas)
    Route::post('/vigilador/accept/{contrato}', [ZonaController::class, 'acceptOffer'])->name('vigilador.accept');
    Route::post('/vigilador/reject/{contrato}', [ZonaController::class, 'rejectOffer'])->name('vigilador.reject');

    // Toggle de trabajo del vigilador
    Route::post('/vigilador/toggle-working/{contrato}', [ZonaController::class, 'toggleWorking'])->name('vigilador.toggle-working');

    // Marcado de notificaciones como leídas
    Route::patch('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    // Eliminar en grupo las notificaciones
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::post('/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('notifications.deleteAll');
    // Ruta de prueba
    Route::get('/test', function () {
        return view('zonas.create');
    });
});

// Rutas para administradores (requiere rol 'admin')
Route::middleware(['auth', 'EnsureEmailIsConfirmed', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [ZonaController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/contratos', [ZonaController::class, 'adminContratos'])->name('admin.contratos');
    Route::get('/admin/contratos/{contrato}/assign', [ZonaController::class, 'assignVigilador'])->name('admin.contratos.assign');
    Route::post('/admin/contratos/{contrato}', [ZonaController::class, 'storeVigilador'])->name('admin.contratos.store');
    Route::get('/admin/offer/{zona}', [ZonaController::class, 'offer'])->name('admin.offer');
    Route::post('/admin/offer/{zona}', [ZonaController::class, 'storeOffer'])->name('admin.offer.store');
    Route::get('/admin/vigiladores', [ZonaController::class, 'vigiladores'])->name('admin.vigiladores');
    Route::post('/admin/vigilador/{user}/toggle', [ZonaController::class, 'toggleVigiladorStatus'])->name('admin.vigilador.toggle');
    Route::post('/admin/vigilador/{user}/inactivate', [ZonaController::class, 'inactivateVigilador'])->name('admin.vigilador.inactivate'); // Añadida
    Route::get('/admin/verify-documents', [ZonaController::class, 'verifyDocuments'])->name('admin.verify-documents');
    Route::delete('/admin/verify-documents/{document}', [ZonaController::class, 'deleteDocument'])->name('admin.delete-document');
    Route::get('/admin/manage-profile-pictures', [ZonaController::class, 'manageProfilePictures'])->name('admin.manage-profile-pictures');
    Route::post('/admin/manage-profile-pictures/{user}', [ZonaController::class, 'updateProfilePicture'])->name('admin.update-profile-picture');
    Route::post('/admin/document/{document}/approve', [App\Http\Controllers\ZonaController::class, 'approveDocument'])->name('admin.document.approve');
    Route::post('/admin/phone/{user}/approve', [ZonaController::class, 'approvePhone'])->name('admin.phone.approve');
    Route::post('/admin/phone/{user}/reject', [ZonaController::class, 'rejectPhone'])->name('admin.phone.reject');
    Route::post('/admin/phone/{user}/add', [ZonaController::class, 'addPhone'])->name('admin.phone.add');
});

// Rutas de autenticación (Breeze)
require __DIR__.'/auth.php';
