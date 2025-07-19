<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Alertas de sesión --}}
                    @if (session()->has('message'))
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('message') }}</span>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- Indicador de contexto actual --}}
                    @if(auth()->user()->hasRole('Admin'))
                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Vista de Admin</p>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        Mostrando solo Supervisores y Conductores de tu unidad organizacional: 
                                        <span class="font-semibold">{{ auth()->user()->unidadOrganizacional->siglas ?? 'Sin unidad' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif(auth()->user()->hasRole('Supervisor'))
                        <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-green-800 dark:text-green-200">Vista de Supervisor</p>
                                    <p class="text-sm text-green-700 dark:text-green-300">
                                        Mostrando solo Conductores/Operadores bajo tu supervisión en: 
                                        <span class="font-semibold">{{ auth()->user()->unidadOrganizacional->siglas ?? 'Sin unidad' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif(auth()->user()->hasRole('Admin General'))
                        <div class="bg-purple-50 dark:bg-purple-900 border border-purple-200 dark:border-purple-700 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-purple-800 dark:text-purple-200">Vista de Admin General</p>
                                    <p class="text-sm text-purple-700 dark:text-purple-300">
                                        Mostrando todos los usuarios del sistema
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Barra de búsqueda y filtros --}}
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                        {{-- Búsqueda --}}
                        <div class="relative w-full lg:w-1/3">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre, apellido, email..." class="pl-10 pr-4 form-input rounded-md shadow-sm block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Filtros --}}
                        <div class="flex flex-wrap gap-2">
                            {{-- Filtro de Rol --}}
                            @if(!empty($managableRoles))
                                <select wire:model.live="roleFilter" class="form-select rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todos los Roles</option>
                                    @foreach($managableRoles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                    <option value="sin-rol">Sin Rol</option>
                                </select>
                            @endif

                            {{-- Filtro de Estado --}}
                            <select wire:model.live="statusFilter" class="form-select rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los Estados</option>
                                <option value="Activo">Activos</option>
                                <option value="Inactivo">Inactivos</option>
                                <option value="Pendiente">Pendientes</option>
                            </select>

                            {{-- Filtro de Unidad Organizacional --}}
                            @if($unidadesOrganizacionales->count() > 1)
                                <select wire:model.live="unidadFilter" class="form-select rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todas las Unidades</option>
                                    @foreach($unidadesOrganizacionales as $unidad)
                                        <option value="{{ $unidad->id_unidad_organizacional }}">{{ $unidad->siglas }} - {{ $unidad->nombre_unidad }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        {{-- Botón crear usuario --}}
                        @can('crear usuarios')
                            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Crear Usuario
                            </a>
                        @endcan
                    </div>

                    {{-- Estadísticas rápidas --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg border border-blue-200 dark:border-blue-700">
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Visibles</p>
                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $this->totalUsersCount }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg border border-green-200 dark:border-green-700">
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">Activos</p>
                            <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $this->activeUsersCount }}</p>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg border border-yellow-200 dark:border-yellow-700">
                            <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pendientes</p>
                            <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $this->pendingUsersCount }}</p>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg border border-red-200 dark:border-red-700">
                            <p class="text-sm font-medium text-red-600 dark:text-red-400">Inactivos</p>
                            <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ $this->inactiveUsersCount }}</p>
                        </div>
                    </div>

                    {{-- Tabla de usuarios --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unidad Organizacional</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supervisor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600" src="{{ $user->foto_perfil_url }}" alt="Foto de {{ $user->nombre }}">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->nombre }} {{ $user->apellido }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->roles->count() > 0)
                                                @foreach($user->roles as $role)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-1 mb-1
                                                        @if($role->name == 'Admin General') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 @endif
                                                        @if($role->name == 'Admin') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @endif
                                                        @if($role->name == 'Supervisor') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif
                                                        @if($role->name == 'Conductor/Operador') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if($user->supervisor)
                                                <div class="flex items-center">
                                                    <img class="h-6 w-6 rounded-full object-cover mr-2" src="{{ $user->supervisor->foto_perfil_url }}" alt="Supervisor">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->supervisor->nombre }} {{ $user->supervisor->apellido }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->supervisor->roles->first()->name ?? 'Sin rol' }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">Sin supervisor</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($user->estado == 'Activo') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif
                                                @if($user->estado == 'Inactivo') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif
                                                @if($user->estado == 'Pendiente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                                {{ $user->estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($this->canManage($user))
                                                <div class="flex items-center space-x-2">
                                                    {{-- Botón Ver --}}
                                                    @can('ver usuarios')
                                                        <a href="{{ route('admin.users.show', $user) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-150" title="Ver detalles">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </a>
                                                    @endcan
                                                    
                                                    {{-- Botón Editar --}}
                                                    @can('editar usuarios')
                                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-150" title="Editar usuario">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </a>
                                                    @endcan
                                                    
                                                    {{-- Botón Eliminar --}}
                                                    @can('eliminar usuarios')
                                                        <button wire:click="deleteUser({{ $user->id }})" 
                                                                wire:confirm="¿Estás seguro de que quieres eliminar a {{ $user->nombre }} {{ $user->apellido }}?" 
                                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-150"
                                                                title="Eliminar usuario">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    @endcan
                                                </div>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600 text-xs italic">Sin permisos</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="text-gray-500 dark:text-gray-400">
                                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                                </svg>
                                                <p class="mt-2 text-sm font-medium">No se encontraron usuarios</p>
                                                <p class="mt-1 text-xs text-gray-400">
                                                    @if($search || $roleFilter || $statusFilter || $unidadFilter)
                                                        Los filtros aplicados no arrojaron resultados.
                                                    @else
                                                        No tienes usuarios bajo tu gestión.
                                                    @endif
                                                </p>
                                                @if($search || $roleFilter || $statusFilter || $unidadFilter)
                                                    <div class="mt-4 space-x-2">
                                                        <button wire:click="$set('search', '')" class="text-indigo-600 hover:text-indigo-500 text-sm">Limpiar búsqueda</button>
                                                        <button wire:click="$set('roleFilter', '')" class="text-indigo-600 hover:text-indigo-500 text-sm">Limpiar filtros</button>
                                                        <button wire:click="$set('statusFilter', '')" class="text-indigo-600 hover:text-indigo-500 text-sm">Resetear todo</button>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>