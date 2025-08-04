<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    Volt::route('login', 'pages.auth.login')
        ->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});

Route::get('/admin/aprobaciones', \App\Livewire\User\ApprovalQueue::class)
        ->middleware(['auth', 'role:Admin General|Admin'])
        ->name('admin.approvals');

Route::get('/admin/reportes/aprobaciones', \App\Livewire\Reports\ApprovalReports::class)
    ->middleware(['auth', 'role:Admin General|Admin'])
    ->name('admin.reports.approval');

// Rutas para Admines y Supervisores - Gestión de usuarios
Route::middleware(['auth','role:Admin General|Admin|Supervisor'])->group(function () {
    Route::get('/usuarios', \App\Livewire\User\UserIndex::class)->name('admin.users.index');
    Route::get('/usuarios/crear', \App\Livewire\User\UserCreate::class)->name('admin.users.create');
    Route::get('/usuarios/{user}/editar', \App\Livewire\User\UserEdit::class)->name('admin.users.edit');
    Route::get('/usuarios/{user}/ver', \App\Livewire\User\UserShow::class)->name('admin.users.show');
    Route::get('/usuarios/imprimir', \App\Livewire\User\UserPrint::class)->name('admin.users.print');
});

// Rutas para ver unidades - Accesible para Admin, Supervisor y Conductor/Operador
Route::middleware(['auth','role:Admin General|Admin|Supervisor|Conductor/Operador'])
    ->group(function () {
        Route::get('/unidades', \App\Livewire\UnidadTransporte\UnidadTransporteIndex::class)
            ->name('admin.units.index');
    });

// Rutas para crear y editar unidades - Solo para Admines
Route::middleware(['auth','role:Admin'])->group(function () {
    Route::get('/unidades/crear', \App\Livewire\UnidadTransporte\UnidadTransporteCreate::class)->name('admin.units.create');
    Route::get('/unidades/{unitTransport}/editar', \App\Livewire\UnidadTransporte\UnidadTransporteEdit::class)->name('admin.units.edit');
});


