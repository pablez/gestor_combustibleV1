<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalles del Usuario') }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a la Lista
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        {{-- Información Principal --}}
                        <div class="lg:col-span-2">
                            {{-- Información Personal --}}
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Información Personal
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $user->nombre }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $user->apellido ?? 'No especificado' }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $user->email }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                        <div class="mt-1">
                                            @if($user->estado == 'Activo')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $user->estado }}
                                                </span>
                                            @elseif($user->estado == 'Inactivo')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $user->estado }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $user->estado }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Información de Unidad Organizacional --}}
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Unidad Organizacional
                                </h3>
                                
                                @if($user->unidadOrganizacional)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de la Unidad</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $user->unidadOrganizacional->nombre_unidad }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Siglas</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $user->unidadOrganizacional->siglas }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de Unidad</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $user->unidadOrganizacional->tipo_unidad }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                            <div class="mt-1">
                                                @if($user->unidadOrganizacional->estado == 'Activo')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                        {{ $user->unidadOrganizacional->estado }}
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                        {{ $user->unidadOrganizacional->estado }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                                        <p class="text-yellow-700 dark:text-yellow-300 text-sm">
                                            <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            No tiene unidad organizacional asignada
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- Información de Roles --}}
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    Roles y Permisos
                                </h3>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles Asignados</label>
                                    <div class="mt-1 flex flex-wrap gap-2">
                                        @forelse($user->roles as $role)
                                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                Sin Rol
                                            </span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- Información de Jerarquía --}}
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Jerarquía de Supervisión
                                </h3>
                                
                                <div class="space-y-4">
                                    {{-- Supervisor Asignado --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supervisor Directo</label>
                                        @if($user->supervisor)
                                            <div class="mt-1 flex items-center p-3 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                                                <img src="{{ $user->supervisor->foto_perfil_url }}" alt="Foto supervisor" class="w-8 h-8 rounded-full mr-3">
                                                <div>
                                                    <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                                        {{ $user->supervisor->nombre }} {{ $user->supervisor->apellido }}
                                                    </p>
                                                    <p class="text-xs text-blue-700 dark:text-blue-300">
                                                        {{ $user->supervisor->roles->first()->name ?? 'Sin rol' }}
                                                        @if($user->supervisor->unidadOrganizacional)
                                                            - {{ $user->supervisor->unidadOrganizacional->siglas }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 italic">No tiene supervisor asignado</p>
                                        @endif
                                    </div>

                                    {{-- Cadena de Supervisión --}}
                                    @if(count($supervisionChain) > 1)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cadena de Supervisión</label>
                                            <div class="mt-1 space-y-2">
                                                @foreach($supervisionChain as $index => $supervisor)
                                                    <div class="flex items-center pl-{{ ($index + 1) * 4 }}">
                                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $supervisor->nombre }} {{ $supervisor->apellido }}
                                                            ({{ $supervisor->roles->first()->name ?? 'Sin rol' }})
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Usuarios que supervisa --}}
                                    @if($user->supervisados->count() > 0)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Usuarios que Supervisa</label>
                                            <div class="mt-1 space-y-2">
                                                @foreach($user->supervisados as $supervisee)
                                                    <div class="flex items-center p-2 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                                                        <img src="{{ $supervisee->foto_perfil_url }}" alt="Foto usuario" class="w-6 h-6 rounded-full mr-2">
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-green-900 dark:text-green-100">
                                                                {{ $supervisee->nombre }} {{ $supervisee->apellido }}
                                                            </p>
                                                            <p class="text-xs text-green-700 dark:text-green-300">
                                                                {{ $supervisee->roles->first()->name ?? 'Sin rol' }}
                                                                @if($supervisee->unidadOrganizacional)
                                                                    - {{ $supervisee->unidadOrganizacional->siglas }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <span class="px-2 py-1 text-xs rounded-full {{ $supervisee->estado == 'Activo' ? 'bg-green-100 text-green-800' : ($supervisee->estado == 'Inactivo' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                            {{ $supervisee->estado }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Fechas importantes --}}
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Fechas Importantes
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Registro</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Última Actualización</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    
                                    @if($user->email_verified_at)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Verificado</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $user->email_verified_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    @else
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Verificado</label>
                                            <p class="mt-1 text-sm text-yellow-600 dark:text-yellow-400 font-medium">Pendiente de verificación</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Sidebar con foto de perfil y acciones --}}
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <div class="text-center">
                                    {{-- Foto de perfil --}}
                                    <div class="mb-4">
                                        <img src="{{ $user->foto_perfil_url }}" alt="Foto de perfil" class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-gray-300 dark:border-gray-600 shadow-lg">
                                        @if(!$user->hasCustomProfilePhoto())
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">Foto por defecto</p>
                                        @endif
                                    </div>
                                    
                                    {{-- Nombre completo --}}
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $user->nombre }} {{ $user->apellido }}
                                    </h2>
                                    
                                    {{-- Rol principal --}}
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $user->roles->first()->name ?? 'Sin rol asignado' }}
                                    </p>
                                    
                                    {{-- Unidad organizacional --}}
                                    @if($user->unidadOrganizacional)
                                        <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                                            {{ $user->unidadOrganizacional->siglas }}
                                        </p>
                                    @endif
                                </div>
                                
                                {{-- Acciones --}}
                                @can('editar usuarios')
                                    <div class="mt-6 space-y-3">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Editar Usuario
                                        </a>
                                        
                                        @if(auth()->user()->hasRole('Admin General|Admin') && $user->id !== auth()->id())
                                            <button wire:click="$dispatch('confirm-delete', { userId: {{ $user->id }} })" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar Usuario
                                            </button>
                                        @endif
                                    </div>
                                @endcan
                            </div>

                            {{-- Estadísticas --}}
                            @if($userStats)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Estadísticas
                                    </h3>
                                    
                                    <div class="space-y-3">
                                        @foreach($userStats as $key => $value)
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ ucfirst(str_replace('_', ' ', $key)) }}
                                                </span>
                                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $value }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
