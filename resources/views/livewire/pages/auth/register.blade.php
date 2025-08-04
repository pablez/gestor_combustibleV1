<?php

use App\Models\User;
use App\Models\CodigoRegistro;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\UserApprovalRequest;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state([
    'nombre' => '',
    'apellido'=> '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'codigo_registro' => '',
]);

rules([
    'nombre' => ['required', 'string', 'max:255'],
    'apellido' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
    'codigo_registro' => ['required', 'string'],
]);

$register = function () {
    $validated = $this->validate();

    // Validar el código de registro (vigente y no usado)
    $codigo = CodigoRegistro::where('codigo', $validated['codigo_registro'])
        ->where('vigente_hasta', '>', now())
        ->where('usado', false)
        ->latest('created_at')
        ->first();

    if (!$codigo) {
        $this->addError('codigo_registro', 'El código de registro es inválido, ha expirado o ya fue utilizado.');
        return;
    }


    // Asignar datos del código a los campos del usuario
    $validated['unidad_organizacional_id'] = $codigo->unidad_organizacional_id;
    $validated['supervisor_id'] = $codigo->supervisor_id;
    $validated['estado'] = 'Pendiente';
    $validated['password'] = Hash::make($validated['password']);

    // Crear el usuario
    $user = User::create($validated);

    // Asignar el rol solicitado
    if ($codigo->rol_solicitado) {
        $user->assignRole($codigo->rol_solicitado);
    }

    event(new Registered($user));

    // Marcar el código como usado
    $codigo->usado = true;
    $codigo->save();

    // Crear la solicitud de aprobación
    UserApprovalRequest::create([
        'usuario_id' => $user->id,
        'creado_por' => $codigo->creado_por,
        'supervisor_asignado_id' => $codigo->supervisor_id,
        'tipo_solicitud' => 'nuevo_usuario',
        'estado' => 'pendiente',
        'rol_solicitado' => $codigo->rol_solicitado,
        'unidad_organizacional_id' => $codigo->unidad_organizacional_id,
        'rol_creador' => 'registro',
        'datos_usuario' => $user->toArray(),
    ]);

    session()->flash('status', '¡Registro exitoso! Su cuenta está pendiente de activación por un administrador.');
    $this->redirect(route('login', absolute: false), navigate: true);
};

?>

<div>
    <form wire:submit="register">
        <!-- Nombre -->
        <div>
            <x-input-label for="nombre" :value="__('Nombre')" />
            <x-text-input wire:model="nombre" id="nombre" class="block mt-1 w-full" type="text" name="nombre" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
        </div>

        <!-- Apellido -->
        <div class="mt-4">
            <x-input-label for="apellido" :value="__('Apellido')" />
            <x-text-input wire:model="apellido" id="apellido" class="block mt-1 w-full" type="text" name="apellido" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('apellido')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Código de registro -->
        <div class="mt-4">
            <x-input-label for="codigo_registro" :value="__('Código de registro')" />
            <x-text-input wire:model="codigo_registro" id="codigo_registro" class="block mt-1 w-full"
                type="text" name="codigo_registro" required autocomplete="off" />
            <x-input-error :messages="$errors->get('codigo_registro')" class="mt-2" />
            <small class="text-gray-500">Solicita este código a tu supervisor o administrador.</small>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}" wire:navigate>
                {{ __('¿Ya tienes cuenta?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
</div>
