<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Usuarios Pendientes de Aprobación') }}
            </h2>
            {{-- Botón de aprobación múltiple --}}
            @if(!empty($selectedUsers))
                <button wire:click="openBulkApproval" class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Aprobar Seleccionados ({{ count($selectedUsers) }})
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Alertas de sesión --}}
                    @if (session()->has('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-200" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-200" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.866-.833-2.636 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    {{-- Indicador de contexto del usuario --}}
                    <div class="mb-6 p-4 bg-{{ $userContext['color'] }}-50 dark:bg-{{ $userContext['color'] }}-900 border border-{{ $userContext['color'] }}-200 dark:border-{{ $userContext['color'] }}-700 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-{{ $userContext['color'] }}-600 dark:text-{{ $userContext['color'] }}-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-{{ $userContext['color'] }}-800 dark:text-{{ $userContext['color'] }}-200">
                                    Vista de {{ $userContext['tipo'] }}
                                </p>
                                <p class="text-sm text-{{ $userContext['color'] }}-700 dark:text-{{ $userContext['color'] }}-300">
                                    {{ $userContext['descripcion'] }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Estadísticas específicas según el rol --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg border border-blue-200 dark:border-blue-700">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                        @if(auth()->user()->hasRole('Admin General'))
                                            Total del Sistema
                                        @else
                                            Total de tu Unidad
                                        @endif
                                    </p>
                                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $statistics['total_visible'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg border border-green-200 dark:border-green-700">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-green-800 dark:text-green-200">Puedes Aprobar</p>
                                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $statistics['can_approve'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg border border-purple-200 dark:border-purple-700">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-purple-800 dark:text-purple-200">Conductores</p>
                                    <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $statistics['conductores'] }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Mostrar Supervisores solo para Admin General --}}
                        @if(auth()->user()->hasRole('Admin General'))
                            <div class="bg-orange-50 dark:bg-orange-900 p-4 rounded-lg border border-orange-200 dark:border-orange-700">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h2a2 2 0 002-2V7a2 2 0 00-2-2H9m0 0V3a2 2 0 012-2h2a2 2 0 012 2v2M9 5v6"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-orange-800 dark:text-orange-200">Supervisores</p>
                                        <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $statistics['supervisores'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-indigo-50 dark:bg-indigo-900 p-4 rounded-lg border border-indigo-200 dark:border-indigo-700">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h2a2 2 0 002-2V7a2 2 0 00-2-2H9m0 0V3a2 2 0 012-2h2a2 2 0 012 2v2M9 5v6"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-indigo-800 dark:text-indigo-200">Seleccionados</p>
                                        <p class="text-2xl font-bold text-indigo-900 dark:text-indigo-100">{{ $statistics['selected'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($users->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay usuarios pendientes</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if(auth()->user()->hasRole('Admin General'))
                                    No hay usuarios Supervisor o Conductor pendientes de aprobación en el sistema.
                                @else
                                    No hay usuarios Conductor pendientes de aprobación en tu unidad organizacional.
                                @endif
                            </p>
                        </div>
                    @else
                        {{-- Controles de selección múltiple --}}
                        @if($statistics['total_visible'] > 0)
                            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Selección múltiple:
                                        </span>
                                        <button wire:click="selectAllUsers" class="inline-flex items-center px-3 py-1 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Seleccionar Todos
                                        </button>
                                        <button wire:click="deselectAllUsers" class="inline-flex items-center px-3 py-1 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Deseleccionar Todos
                                        </button>
                                    </div>
                                    
                                    @if(!empty($selectedUsers))
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ count($selectedUsers) }} usuario(s) seleccionado(s)
                                            </span>
                                            <button wire:click="openBulkApproval" class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Aprobar Seleccionados
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <input type="checkbox" wire:model="selectAll" wire:click="selectAllUsers" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700">
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol Asignado</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unidad Organizacional</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Supervisor / Creador
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($users as $user)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 {{ in_array($user->id, $selectedUsers) ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" wire:model="selectedUsers" value="{{ $user->id }}" wire:click="toggleUserSelection({{ $user->id }})" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600" src="{{ $user->foto_perfil_url }}" alt="Foto de {{ $user->nombre }}">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $user->nombre }} {{ $user->apellido }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $user->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($user->getRoleNames()->first())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($user->hasRole('Admin')) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @endif
                                                        @if($user->hasRole('Supervisor')) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif
                                                        @if($user->hasRole('Conductor/Operador')) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                                        {{ $user->getRoleNames()->first() }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                                        Sin Rol
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($user->unidadOrganizacional)
                                                    <div class="flex flex-col">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-1
                                                            @if($user->unidadOrganizacional->tipo_unidad == 'Superior') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 @endif
                                                            @if($user->unidadOrganizacional->tipo_unidad == 'Ejecutiva') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 @endif">
                                                            {{ $user->unidadOrganizacional->siglas }}
                                                        </span>
                                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $user->unidadOrganizacional->tipo_unidad }}</span>
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                                        Sin Unidad
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($user->supervisor)
                                                    <div class="flex items-center">
                                                        <img class="h-6 w-6 rounded-full object-cover mr-2" src="{{ $user->supervisor->foto_perfil_url }}" alt="Creador">
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                {{ $user->supervisor->nombre }} {{ $user->supervisor->apellido }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                @if($user->hasRole('Supervisor'))
                                                                    {{-- Supervisor fue creado por Admin --}}
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                        </svg>
                                                                        Creado por Admin
                                                                    </span>
                                                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                                        Admin: {{ $user->supervisor->nombre }} {{ $user->supervisor->apellido }}
                                                                    </div>
                                                                @elseif($user->hasRole('Conductor/Operador'))
                                                                    {{-- Conductor - verificar quién lo creó --}}
                                                                    @if($user->supervisor->hasRole('Supervisor'))
                                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 01-3 0m3 0H9m1.5 0H9m0 0H6"></path>
                                                                            </svg>
                                                                            Creado por Supervisor
                                                                        </span>
                                                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                                            Supervisor: {{ $user->supervisor->nombre }} {{ $user->supervisor->apellido }}
                                                                        </div>
                                                                    @elseif($user->supervisor->hasRole('Admin'))
                                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                            </svg>
                                                                            Creado por Admin
                                                                        </span>
                                                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                                            Admin: {{ $user->supervisor->nombre }} {{ $user->supervisor->apellido }}
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">Sin supervisor asignado</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <div class="flex flex-col">
                                                    <span class="font-medium">{{ $user->created_at->format('d/m/Y') }}</span>
                                                    <span class="text-xs">{{ $user->created_at->format('H:i') }}</span>
                                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button wire:click="viewDetails({{ $user->id }})" class="inline-flex items-center px-3 py-1 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Ver
                                                    </button>
                                                
                                                    <button wire:click="openIndividualApproval({{ $user->id }})" class="inline-flex items-center px-3 py-1 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Aprobar
                                                    </button>
                                                
                                                    <button wire:click="reject({{ $user->id }})" wire:confirm="¿Estás seguro de que quieres rechazar y eliminar a este usuario? Esta acción no se puede deshacer." class="inline-flex items-center px-3 py-1 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Rechazar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para aprobación múltiple --}}
    @if($showBulkApproval)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    Aprobar {{ count($selectedUsers) }} Usuario(s) Seleccionado(s)
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        @if(auth()->user()->hasRole('Admin General'))
                                            Vas a aprobar {{ count($selectedUsers) }} usuario(s) (Supervisores y/o Conductores) del sistema. Por seguridad, confirma tu identidad.
                                        @else
                                            Vas a aprobar {{ count($selectedUsers) }} usuario(s) Conductor(es) de tu unidad organizacional. Por seguridad, confirma tu identidad.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-5 sm:mt-4">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="bulkEmail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input type="email" wire:model="bulkEmail" id="bulkEmail" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" placeholder="tu@email.com">
                                    @error('bulkEmail') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="bulkPassword" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                                    <input type="password" wire:model="bulkPassword" id="bulkPassword" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" placeholder="Tu contraseña">
                                    @error('bulkPassword') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="processBulkApproval" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Confirmar Aprobación
                        </button>
                        <button wire:click="closeBulkApproval" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal para aprobación individual --}}
    @if($showIndividualApproval && $userToApprove)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    Aprobar Usuario {{ $userToApprove->getRoleNames()->first() }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Vas a aprobar a <strong>{{ $userToApprove->nombre }} {{ $userToApprove->apellido }}</strong> como <strong>{{ $userToApprove->getRoleNames()->first() }}</strong>
                                        @if($userToApprove->unidadOrganizacional)
                                            de la unidad <strong>{{ $userToApprove->unidadOrganizacional->siglas }}</strong>
                                        @endif
                                        . Por seguridad, confirma tu identidad.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-5 sm:mt-4">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="individualEmail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input type="email" wire:model="individualEmail" id="individualEmail" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" placeholder="tu@email.com">
                                    @error('individualEmail') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="individualPassword" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                                    <input type="password" wire:model="individualPassword" id="individualPassword" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" placeholder="Tu contraseña">
                                    @error('individualPassword') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="processIndividualApproval" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Confirmar Aprobación
                        </button>
                        <button wire:click="closeIndividualApproval" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal para ver detalles del usuario --}}
    @if($showUserDetails && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeUserDetails"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    Detalles del Usuario {{ $selectedUser->getRoleNames()->first() }}
                                </h3>
                                <div class="mt-4">
                                    <div class="flex items-center space-x-4 mb-6">
                                        <img src="{{ $selectedUser->foto_perfil_url }}" alt="Foto de perfil" class="w-16 h-16 object-cover rounded-full border-2 border-gray-300 dark:border-gray-600">
                                        <div>
                                            <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $selectedUser->nombre }} {{ $selectedUser->apellido }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedUser->email }}</p>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                    {{ $selectedUser->estado }}
                                                </span>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                    ✓ Puedes aprobar
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol Asignado</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedUser->getRoleNames()->first() ?? 'Sin Rol' }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unidad Organizacional</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                @if($selectedUser->unidadOrganizacional)
                                                    {{ $selectedUser->unidadOrganizacional->siglas }} - {{ $selectedUser->unidadOrganizacional->nombre_unidad }}
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $selectedUser->unidadOrganizacional->tipo_unidad }})</span>
                                                @else
                                                    Sin unidad asignada
                                                @endif
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Información de Creación
                                            </label>
                                            <div class="mt-1">
                                                @if($selectedUser->supervisor)
                                                    @if($selectedUser->hasRole('Supervisor'))
                                                        <p class="text-sm text-gray-900 dark:text-gray-100">
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                Creado por Admin
                                                            </span>
                                                        </p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            Admin creador: {{ $selectedUser->supervisor->nombre }} {{ $selectedUser->supervisor->apellido }}
                                                            <span class="text-xs">({{ $selectedUser->supervisor->roles->first()->name ?? 'Sin rol' }})</span>
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            Nota: El Admin también supervisa a este Supervisor
                                                        </p>
                                                    @elseif($selectedUser->hasRole('Conductor/Operador'))
                                                        @if($selectedUser->supervisor->hasRole('Supervisor'))
                                                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                    Creado por Supervisor
                                                                </span>
                                                            </p>
                                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                                Supervisor creador: {{ $selectedUser->supervisor->nombre }} {{ $selectedUser->supervisor->apellido }}
                                                            </p>
                                                        @elseif($selectedUser->supervisor->hasRole('Admin'))
                                                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                                    Creado por Admin
                                                                </span>
                                                            </p>
                                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                                Admin creador: {{ $selectedUser->supervisor->nombre }} {{ $selectedUser->supervisor->apellido }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                Nota: El Admin seleccionó un Supervisor para este Conductor
                                                            </p>
                                                        @endif
                                                    @endif
                                                @else
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Sin información de creador</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Registro</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $selectedUser->created_at->format('d/m/Y H:i') }}
                                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ $selectedUser->created_at->diffForHumans() }})</span>
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Información adicional para Admin General --}}
                                    @if(auth()->user()->hasRole('Admin General'))
                                        <div class="mt-4 p-3 bg-purple-50 dark:bg-purple-900 border border-purple-200 dark:border-purple-700 rounded-lg">
                                            <h5 class="text-sm font-medium text-purple-800 dark:text-purple-200 mb-2">Información del Sistema - Jerarquía de Creación</h5>
                                            <p class="text-xs text-purple-600 dark:text-purple-400">
                                                @if($selectedUser->hasRole('Supervisor'))
                                                    Este Supervisor fue creado por el Admin: {{ $selectedUser->supervisor->nombre ?? 'Sin información' }}. 
                                                    Como Admin General, puedes aprobar usuarios creados por cualquier Admin del sistema.
                                                @elseif($selectedUser->hasRole('Conductor/Operador'))
                                                    @if($selectedUser->supervisor && $selectedUser->supervisor->hasRole('Supervisor'))
                                                        Este Conductor fue creado por el Supervisor: {{ $selectedUser->supervisor->nombre }}. 
                                                        Como Admin General, puedes aprobar usuarios creados por cualquier Supervisor del sistema.
                                                    @elseif($selectedUser->supervisor && $selectedUser->supervisor->hasRole('Admin'))
                                                        Este Conductor fue creado por el Admin: {{ $selectedUser->supervisor->nombre }}. 
                                                        Como Admin General, puedes aprobar usuarios creados por cualquier Admin del sistema.
                                                    @endif
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="openIndividualApproval({{ $selectedUser->id }})" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Aprobar {{ $selectedUser->getRoleNames()->first() }}
                        </button>
                        <button wire:click="reject({{ $selectedUser->id }})" wire:confirm="¿Estás seguro de que quieres rechazar y eliminar a este usuario? Esta acción no se puede deshacer." type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Rechazar
                        </button>
                        <button wire:click="closeUserDetails" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
