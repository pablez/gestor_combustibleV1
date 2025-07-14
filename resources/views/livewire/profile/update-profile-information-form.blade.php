<?php

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

use function Livewire\Volt\state;
use function Livewire\Volt\uses;

uses([WithFileUploads::class]);

state([
    'nombre' => fn () => auth()->user()->nombre,
    'apellido' => fn () => auth()->user()->apellido,
    'email' => fn () => auth()->user()->email,
    'foto_perfil' => null,
]);

$updateProfileInformation = function () {
    $user = Auth::user();

    $validated = $this->validate([
        'nombre' => ['required', 'string', 'max:255'],
        'apellido' => ['nullable', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        'foto_perfil' => ['nullable', 'image', 'max:2048'], // 2MB máximo
    ]);

    // Procesar la foto de perfil si se subió una nueva
    if ($this->foto_perfil) {
        // Eliminar la foto anterior si existe
        if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
            Storage::disk('public')->delete($user->foto_perfil);
        }
        
        // Guardar la nueva foto
        $fotoPath = $this->foto_perfil->store('fotos-perfil', 'public');
        $validated['foto_perfil'] = $fotoPath;
    }

    $user->fill($validated);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    // Resetear el campo de foto para evitar problemas
    $this->foto_perfil = null;

    $this->dispatch('profile-updated', nombre: $user->nombre);
};

$sendVerification = function () {
    $user = Auth::user();

    if ($user->hasVerifiedEmail()) {
        $this->redirectIntended(default: route('dashboard', absolute: false));
        return;
    }

    $user->sendEmailVerificationNotification();
    Session::flash('status', 'verification-link-sent');
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Información del Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Actualizar su información del perfil, email y foto.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        
        {{-- Sección de foto de perfil --}}
        <div class="flex items-center space-x-6">
            <div class="shrink-0">
                @if ($foto_perfil)
                    <img src="{{ $foto_perfil->temporaryUrl() }}" alt="Vista previa" class="h-20 w-20 object-cover rounded-full border-2 border-gray-300 dark:border-gray-600">
                @else
                    <img src="{{ auth()->user()->foto_perfil_url }}" alt="Foto actual" class="h-20 w-20 object-cover rounded-full border-2 border-gray-300 dark:border-gray-600">
                @endif
            </div>
            <div class="flex-1">
                <x-input-label for="foto_perfil" :value="__('Foto de Perfil')" />
                <input type="file" wire:model="foto_perfil" id="foto_perfil" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-600 dark:file:text-gray-300 dark:hover:file:bg-gray-500">
                <x-input-error class="mt-2" :messages="$errors->get('foto_perfil')" />
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">JPG, PNG o GIF (máximo 2MB)</p>
            </div>
        </div>

        {{-- Información personal --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="nombre" :value="__('Nombre')" />
                <x-text-input wire:model="nombre" id="nombre" name="nombre" type="text" class="mt-1 block w-full" required autofocus autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
            </div>

            <div>
                <x-input-label for="apellido" :value="__('Apellido')" />
                <x-text-input wire:model="apellido" id="apellido" name="apellido" type="text" class="mt-1 block w-full" autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('apellido')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Tu dirección de email no está verificada.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Haz clic aquí para reenviar el email de verificación.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu dirección de email.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Información adicional del usuario --}}
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Información de la Cuenta') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Rol:') }}</span>
                    <span class="text-gray-600 dark:text-gray-400">{{ auth()->user()->getRoleNames()->first() ?? 'Sin rol' }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Estado:') }}</span>
                    <span class="text-gray-600 dark:text-gray-400">{{ auth()->user()->estado }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Registrado:') }}</span>
                    <span class="text-gray-600 dark:text-gray-400">{{ auth()->user()->created_at->format('d/m/Y') }}</span>
                </div>
                @if(auth()->user()->supervisor)
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Supervisor:') }}</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ auth()->user()->supervisor->nombre }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Guardado.') }}
            </x-action-message>
        </div>
    </form>
</section>
