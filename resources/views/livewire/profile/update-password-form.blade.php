<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state([
    'current_password' => '',
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'current_password' => ['required', 'string', 'current_password'],
    'password' => ['required', 'string', Password::defaults(), 'confirmed'],
]);

$updatePassword = function () {
    try {
        $validated = $this->validate();
    } catch (ValidationException $e) {
        $this->reset('current_password', 'password', 'password_confirmation');
        throw $e;
    }

    Auth::user()->update([
        'password' => Hash::make($validated['password']),
    ]);

    $this->reset('current_password', 'password', 'password_confirmation');
    $this->dispatch('password-updated');
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Actualizar Contraseña') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Asegúrate de que tu cuenta esté usando una contraseña larga y aleatoria para mantenerte seguro.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <div>
            <x-input-label for="update_password_current_password" :value="__('Contraseña Actual')" />
            <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" placeholder="Ingresa tu contraseña actual" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Nueva Contraseña')" />
            <x-text-input wire:model="password" id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" placeholder="Mínimo 8 caracteres" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirmar Contraseña')" />
            <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" placeholder="Repite la nueva contraseña" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Consejos de seguridad --}}
        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
            <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">{{ __('Consejos para una contraseña segura:') }}</h4>
            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                <li>• {{ __('Usa al menos 8 caracteres') }}</li>
                <li>• {{ __('Incluye mayúsculas y minúsculas') }}</li>
                <li>• {{ __('Añade números y símbolos') }}</li>
                <li>• {{ __('Evita información personal') }}</li>
            </ul>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Actualizar Contraseña') }}</x-primary-button>

            <x-action-message class="me-3" on="password-updated">
                {{ __('Contraseña actualizada.') }}
            </x-action-message>
        </div>
    </form>
</section>
