<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Usuario') }}: {{ $user->nombre }} {{ $user->apellido }}
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

                    {{-- Mensajes de estado --}}
                    @if (session()->has('message'))
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded relative mb-4" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="block sm:inline">{{ session('message') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded relative mb-4" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.866-.833-2.636 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Información del usuario a editar --}}
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $user->foto_perfil_url }}" alt="Foto actual" class="w-12 h-12 object-cover rounded-full border-2 border-blue-300 dark:border-blue-600">
                            <div>
                                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">
                                    Editando: {{ $user->nombre }} {{ $user->apellido }}
                                </h3>
                                <p class="text-sm text-blue-600 dark:text-blue-400">
                                    {{ $user->email }} • {{ $user->getRoleNames()->first() ?? 'Sin rol' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="updateUser">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            
                            {{-- Sidebar con foto de perfil --}}
                            <div class="lg:col-span-1">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-center">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Foto de Perfil</h3>
                                    
                                    {{-- Foto actual --}}
                                    <div class="mb-4">
                                        @if ($foto_perfil)
                                            <img src="{{ $foto_perfil->temporaryUrl() }}" alt="Vista previa" class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-indigo-300 dark:border-indigo-600 shadow-lg">
                                        @else
                                            <img src="{{ $user->foto_perfil_url }}" alt="Foto actual" class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-gray-300 dark:border-gray-600 shadow-lg">
                                        @endif
                                    </div>
                                    
                                    {{-- Input para nueva foto --}}
                                    <div class="mb-4">
                                        <label for="foto_perfil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cambiar Foto</label>
                                        <input type="file" id="foto_perfil" wire:model="foto_perfil" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-600 dark:file:text-gray-300 dark:hover:file:bg-gray-500">
                                        @error('foto_perfil') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    {{-- Información de la foto --}}
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        @if ($foto_perfil)
                                            <p class="text-green-600 dark:text-green-400 font-medium">Nueva foto seleccionada</p>
                                        @elseif($user->hasCustomProfilePhoto())
                                            <p>Foto personalizada actual</p>
                                        @else
                                            <p>Usando foto por defecto</p>
                                        @endif
                                        <p class="text-xs mt-1">Máximo 2MB, formatos: JPG, PNG, GIF</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Formulario principal --}}
                            <div class="lg:col-span-2">
                                <div class="space-y-6">
                                    
                                    {{-- Información Personal --}}
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Información Personal</h3>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre *</label>
                                                <input type="text" id="nombre" wire:model="nombre" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                @error('nombre') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label for="apellido" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</label>
                                                <input type="text" id="apellido" wire:model="apellido" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                @error('apellido') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="md:col-span-2">
                                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                                                <input type="email" id="email" wire:model="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                @error('email') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Seguridad --}}
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Seguridad</h3>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva Contraseña</label>
                                                <input type="password" id="password" wire:model="password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dejar en blanco para no cambiar</p>
                                                @error('password') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar Contraseña</label>
                                                <input type="password" id="password_confirmation" wire:model="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Estado --}}
                                    @if(auth()->user()->hasRole('Administrador|Supervisor'))
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Estado del Usuario</h3>
                                            
                                            @if($user->estado !== 'Pendiente')
                                                <div>
                                                    <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                                    <select id="estado" wire:model="estado" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                        <option value="Activo">Activo</option>
                                                        <option value="Inactivo">Inactivo</option>
                                                    </select>
                                                    @error('estado') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                            @else
                                                <div class="p-4 bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-200 rounded">
                                                    <div class="flex items-center">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.866-.833-2.636 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                        </svg>
                                                        <div>
                                                            <p class="font-bold">Estado: Pendiente</p>
                                                            <p class="text-sm">Este usuario debe ser aprobado por un administrador antes de que su estado pueda ser modificado.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Roles y Permisos --}}
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Roles y Permisos</h3>
                                        
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Asignar Rol</label>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                                @forelse($roles as $role)
                                                    <label class="inline-flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors duration-200 {{ $selectedRole == $role->id ? 'bg-indigo-50 dark:bg-indigo-900 border-indigo-300 dark:border-indigo-600' : '' }}">
                                                        <input type="radio" wire:model.live="selectedRole" name="selectedRole" value="{{ $role->id }}" class="rounded-full border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700">
                                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ $role->name }}</span>
                                                    </label>
                                                @empty
                                                    <p class="text-gray-500 dark:text-gray-400">No hay roles disponibles.</p>
                                                @endforelse
                                            </div>
                                            @error('selectedRole') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Campo para asignar supervisor --}}
                                        @if(auth()->user()->hasRole('Administrador') && $selectedRole == $conductorRoleId)
                                            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                                                <label for="supervisor_id" class="block text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Asignar Supervisor</label>
                                                <select id="supervisor_id" wire:model="supervisor_id" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                    <option value="">Sin supervisor</option>
                                                    @foreach($supervisors as $supervisor)
                                                        <option value="{{ $supervisor->id }}">{{ $supervisor->nombre }} {{ $supervisor->apellido }}</option>
                                                    @endforeach
                                                </select>
                                                @error('supervisor_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Botones de acción --}}
                                    <div class="flex items-center justify-end space-x-4 pt-6">
                                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancelar
                                        </a>
                                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 active:bg-indigo-800 dark:active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Actualizar Usuario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>