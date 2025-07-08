<x-app-layout>
    <x-slot name="header">
        {{-- ... existing header ... --}}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- ... existing welcome message ... --}}
                    <p class="mb-6 text-gray-600 dark:text-gray-400">
                        Has iniciado sesión correctamente. Tu rol actual es: 
                        <span class="font-semibold text-blue-600 dark:text-blue-400">
                            {{ auth()->user()->getRoleNames()->first() ?? 'Sin rol asignado' }}
                        </span>
                    </p>

                    <!-- Panel de Gestión para Administradores y Supervisores -->
                    @if(auth()->user()->hasAnyRole(['Administrador','Supervisor']))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Tarjeta para Gestión de Usuarios --}}
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                    <span class="text-xl">👥</span> Panel de Gestión de Usuarios
                                </h4>
                                <p class="text-sm text-blue-600 dark:text-blue-300 mb-3">
                                    Crear, editar y asignar roles a los usuarios del sistema.
                                </p>
                                <a href="{{ route('admin.users.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105">
                                    Gestionar Usuarios
                                </a>
                            </div>

                            {{-- Tarjeta para Gestión de Transporte --}}
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                    <span class="text-xl">🚚</span> Panel de Gestión de Transporte
                                </h4>
                                <p class="text-sm text-blue-600 dark:text-blue-300 mb-3">
                                    Administrar las unidades de transporte, ver estados y mantenimientos.
                                </p>
                                <a href="{{ route('admin.units.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105">
                                    Gestionar Unidades
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Información general para todos los usuarios -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- ... existing general info cards ... --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>