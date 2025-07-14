<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalles del Usuario') }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
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
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Información Personal</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->nombre }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->apellido ?? 'No especificado' }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                        <div class="mt-1">
                                            @if($user->estado == 'Activo')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                    {{ $user->estado }}
                                                </span>
                                            @elseif($user->estado == 'Inactivo')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                    {{ $user->estado }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                    {{ $user->estado }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Información de Roles --}}
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Roles y Permisos</h3>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles Asignados</label>
                                    <div class="mt-1 flex flex-wrap gap-2">
                                        @forelse($user->roles as $role)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                Sin Rol
                                            </span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- Información de Supervisor --}}
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Jerarquía</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supervisor Asignado</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $user->supervisor ? $user->supervisor->nombre . ' ' . $user->supervisor->apellido : 'No asignado' }}
                                        </p>
                                    </div>
                                    
                                    @if($user->hasRole('Supervisor'))
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conductores Supervisados</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $user->conductores->count() }} conductor(es)
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Fechas importantes --}}
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Fechas Importantes</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Registro</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Última Actualización</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    
                                    @if($user->email_verified_at)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Verificado</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->email_verified_at->format('d/m/Y H:i') }}</p>
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
                                        <img src="{{ $user->foto_perfil_url }}" alt="Foto de perfil" class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-gray-300 dark:border-gray-600">
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
                                </div>
                                
                                {{-- Acciones --}}
                                <div class="mt-6 space-y-3">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                        Editar Usuario
                                    </a>
                                    
                                    @if(auth()->user()->hasRole('Administrador') && $user->id !== auth()->id())
                                        <button wire:click="$dispatch('confirm-delete', { userId: {{ $user->id }} })" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                            Eliminar Usuario
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Estadísticas si es supervisor --}}
                            @if($user->hasRole('Supervisor'))
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Estadísticas</h3>
                                    
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Conductores Activos</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $user->conductores->where('estado', 'Activo')->count() }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Conductores Inactivos</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $user->conductores->where('estado', 'Inactivo')->count() }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Pendientes de Aprobación</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $user->conductores->where('estado', 'Pendiente')->count() }}
                                            </span>
                                        </div>
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
