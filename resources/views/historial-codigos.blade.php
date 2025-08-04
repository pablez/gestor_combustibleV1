<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Historial de Códigos de Registro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @livewire('historial-codigos-panel')
    </div>
</x-app-layout>
