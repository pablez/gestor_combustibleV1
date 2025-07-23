<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Crear Nuevo Usuario') }}
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
                    @if (session()->has('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-200" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-200" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.866-.833-2.636 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Información del contexto del usuario --}}
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    @if(auth()->user()->hasRole('Admin General'))
                                        Creando como Admin General
                                    @elseif(auth()->user()->hasRole('Admin'))
                                        Creando como Admin de {{ auth()->user()->unidadOrganizacional->siglas ?? 'Sin unidad' }}
                                    @elseif(auth()->user()->hasRole('Supervisor'))
                                        Creando como Supervisor de {{ auth()->user()->unidadOrganizacional->siglas ?? 'Sin unidad' }}
                                    @endif
                                </p>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    @if(auth()->user()->hasRole('Admin General'))
                                        Puedes crear usuarios de cualquier rol y asignarlos a cualquier unidad organizacional. Solo tú puedes crear usuarios directamente activos. Cuando creas un Admin, automáticamente estará bajo tu supervisión.
                                    @elseif(auth()->user()->hasRole('Admin'))
                                        Puedes crear Supervisores y Conductores para tu unidad organizacional. Los usuarios requerirán aprobación del Admin General del sistema.
                                    @elseif(auth()->user()->hasRole('Supervisor'))
                                        Solo puedes crear Conductores para tu unidad organizacional. Los usuarios requerirán aprobación de un administrador.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Alerta específica para Admin sobre la nueva política --}}
                    @if(auth()->user()->hasRole('Admin'))
                        <div class="mb-6 p-4 bg-orange-50 dark:bg-orange-900 border border-orange-200 dark:border-orange-700 rounded-lg">
                            <p class="text-sm font-medium text-orange-800 dark:text-orange-200">Importante - Nueva Política de Aprobación:</p>
                            <p class="text-sm text-orange-700 dark:text-orange-300">
                                A partir de ahora, todos los usuarios que crees (Supervisores y Conductores) necesitarán aprobación del Admin General del sistema antes de ser activados.
                            </p>
                        </div>
                    @endif

                    <form wire:submit.prevent="saveUser">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            
                            {{-- Sidebar con foto de perfil --}}
                            <div class="lg:col-span-1">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-center">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Foto de Perfil</h3>
                                    
                                    {{-- Vista previa de la foto --}}
                                    <div class="mb-4">
                                        @if ($foto_perfil)
                                            <img src="{{ $foto_perfil->temporaryUrl() }}" alt="Vista previa" class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-indigo-300 dark:border-indigo-600 shadow-lg">
                                        @else
                                            <img src="{{ asset('images/foto-perfil.png') }}" alt="Foto por defecto" class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-gray-300 dark:border-gray-600 shadow-lg">
                                        @endif
                                    </div>
                                    
                                    {{-- Input para foto --}}
                                    <div class="mb-4">
                                        <label for="foto_perfil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Seleccionar Foto</label>
                                        <input type="file" id="foto_perfil" wire:model="foto_perfil" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-600 dark:file:text-gray-300 dark:hover:file:bg-gray-500">
                                        @error('foto_perfil') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    {{-- Información de la foto --}}
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        @if ($foto_perfil)
                                            <p class="text-green-600 dark:text-green-400 font-medium">Foto seleccionada</p>
                                        @else
                                            <p>Se usará la foto por defecto</p>
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
                                                <input type="text" id="nombre" wire:model="nombre" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" placeholder="Ingresa el nombre">
                                                @error('nombre') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label for="apellido" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</label>
                                                <input type="text" id="apellido" wire:model="apellido" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" placeholder="Ingresa el apellido">
                                                @error('apellido') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="md:col-span-2">
                                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                                                <input type="email" id="email" wire:model="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" placeholder="usuario@example.com">
                                                @error('email') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Seguridad --}}
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Seguridad</h3>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña *</label>
                                                <input type="password" id="password" wire:model="password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" placeholder="Mínimo 8 caracteres">
                                                @error('password') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar Contraseña *</label>
                                                <input type="password" id="password_confirmation" wire:model="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" placeholder="Repite la contraseña">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Estado (solo para Admin General) --}}
                                    @if(auth()->user()->hasRole('Admin General'))
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Estado del Usuario</h3>
                                            
                                            <div>
                                                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado Inicial</label>
                                                <select id="estado" wire:model="estado" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                    <option value="Activo">Activo</option>
                                                    <option value="Pendiente">Pendiente</option>
                                                    <option value="Inactivo">Inactivo</option>
                                                </select>
                                                @error('estado') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                                <div class="mt-2 space-y-2">
                                                    <div class="flex items-start">
                                                        <div class="flex items-center h-5">
                                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                                        </div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                                            <span class="font-medium">Activo:</span> El usuario puede acceder al sistema inmediatamente
                                                        </div>
                                                    </div>
                                                    <div class="flex items-start">
                                                        <div class="flex items-center h-5">
                                                            <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                                        </div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                                            <span class="font-medium">Pendiente:</span> El usuario aparecerá en la cola de aprobación
                                                        </div>
                                                    </div>
                                                    <div class="flex items-start">
                                                        <div class="flex items-center h-5">
                                                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                                        </div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                                            <span class="font-medium">Inactivo:</span> El usuario no puede acceder al sistema
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <p class="text-xs text-blue-800 dark:text-blue-200">
                                                            <span class="font-medium">Privilegio exclusivo:</span> Solo como Admin General puedes crear usuarios directamente activos o con cualquier estado.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Información de estado para Admin y Supervisor --}}
                                        <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.866-.833-2.636 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Estado Pendiente Automático:</p>
                                                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-2">
                                                        @if(auth()->user()->hasRole('Admin'))
                                                            El usuario será creado con estado "Pendiente" y requerirá aprobación del Admin General del sistema.
                                                        @else
                                                            El usuario será creado con estado "Pendiente" y requerirá aprobación de un administrador de la unidad organizacional.
                                                        @endif
                                                    </p>
                                                    <div class="flex items-center">
                                                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                                        <span class="text-xs text-yellow-600 dark:text-yellow-400">
                                                            El usuario no podrá acceder al sistema hasta ser aprobado
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Sección de Roles y Permisos --}}
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Roles y Permisos</h3>
                                        
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Asignar Rol *</label>
                                            @if($roles && $roles->count() > 0)
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                                    @foreach($roles as $role)
                                                        <label class="inline-flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors duration-200 {{ $selectedRole == $role->id ? 'bg-indigo-50 dark:bg-indigo-900 border-indigo-300 dark:border-indigo-600' : '' }}">
                                                            <input type="radio" wire:model.live="selectedRole" name="selectedRole" value="{{ $role->id }}" class="rounded-full border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700">
                                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ $role->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg">
                                                    <p class="text-red-700 dark:text-red-300">No hay roles disponibles para tu nivel de acceso.</p>
                                                </div>
                                            @endif
                                            @error('selectedRole') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Selector de Unidad Organizacional (solo para Admin General) --}}
                                        @if($showUnidadSelector)
                                            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                                                <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">Unidad Organizacional</h4>
                                                <label for="unidad_organizacional_id" class="block text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Seleccionar Unidad Organizacional *</label>
                                                <select id="unidad_organizacional_id" wire:model.live="unidad_organizacional_id" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                    <option value="">Selecciona una unidad organizacional</option>
                                                    @foreach($unidadesOrganizacionales as $unidad)
                                                        <option value="{{ $unidad->id_unidad_organizacional }}">
                                                            {{ $unidad->siglas }} - {{ $unidad->nombre_unidad }} ({{ $unidad->tipo_unidad }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('unidad_organizacional_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        @endif

                                        {{-- Información automática para Admin General creando Admin --}}
                                        @if(auth()->user()->hasRole('Admin General') && $selectedRole == $adminRoleId && $unidad_organizacional_id)
                                            <div class="mt-4 p-4 bg-purple-50 dark:bg-purple-900 border border-purple-200 dark:border-purple-700 rounded-lg">
                                                <div class="flex items-start">
                                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="text-sm font-medium text-purple-800 dark:text-purple-200">Supervisión Automática</p>
                                                        <p class="text-sm text-purple-700 dark:text-purple-300">
                                                            Como Admin General, automáticamente serás el supervisor directo de este Admin. 
                                                            El usuario será creado directamente activo y bajo tu supervisión.
                                                        </p>
                                                        <p class="text-sm text-purple-700 dark:text-purple-300 mt-2">
                                                            <strong>Unidad asignada:</strong> {{ $unidadesOrganizacionales->where('id_unidad_organizacional', $unidad_organizacional_id)->first()->siglas ?? 'Seleccionada' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Información automática para Admin creando Supervisor --}}
                                        @if(auth()->user()->hasRole('Admin') && $selectedRole == $supervisorRoleId)
                                            <div class="mt-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">✅ Configuración Automática Completada:</p>
                                                        <ul class="text-sm text-green-700 dark:text-green-300 mt-1 space-y-1">
                                                            <li>• <strong>Unidad Organizacional:</strong> {{ auth()->user()->unidadOrganizacional->siglas ?? 'Sin unidad' }}</li>
                                                            <li>• <strong>Creador del usuario:</strong> {{ auth()->user()->nombre }} {{ auth()->user()->apellido }} (Tú)</li>
                                                            <li>• <strong>Supervisor asignado:</strong> {{ auth()->user()->nombre }} {{ auth()->user()->apellido }} (Tú)</li>
                                                            <li>• <strong>Estado:</strong> Pendiente (requiere aprobación del Admin General)</li>
                                                        </ul>
                                                        <p class="text-sm text-green-700 dark:text-green-300 mt-2">
                                                            <strong>Nota:</strong> En la cola de aprobación aparecerás como el creador de este Supervisor.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Selector de Supervisor para Admin creando Conductor --}}
                                        @if(auth()->user()->hasRole('Admin') && $selectedRole == $conductorRoleId)
                                            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                                                <div class="mb-3">
                                                    <div class="flex items-center">
                                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <div>
                                                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Unidad Organizacional:</p>
                                                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                                                Se asignará automáticamente a tu unidad organizacional 
                                                                <span class="font-semibold">({{ auth()->user()->unidadOrganizacional->siglas ?? 'Sin unidad' }})</span>
                                                            </p>
                                                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                                                <strong>Nota:</strong> Requerirá aprobación del Admin General.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                @if($showSupervisorSelector)
                                                    <div>
                                                        <label for="supervisor_id" class="block text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Seleccionar Supervisor *</label>
                                                        <select id="supervisor_id" wire:model="supervisor_id" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                            <option value="">Selecciona un Supervisor</option>
                                                            @if($supervisors && $supervisors->count() > 0)
                                                                @foreach($supervisors as $supervisor)
                                                                    <option value="{{ $supervisor->id }}">
                                                                        {{ $supervisor->nombre }} {{ $supervisor->apellido }}
                                                                        @if($supervisor->unidadOrganizacional)
                                                                            ({{ $supervisor->unidadOrganizacional->siglas }})
                                                                        @endif
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                <option value="" disabled>No hay Supervisores disponibles en tu unidad</option>
                                                            @endif
                                                        </select>
                                                        @error('supervisor_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Selecciona quién supervisará a este conductor en tu unidad organizacional.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Selector de Supervisor para Admin General creando Conductor --}}
                                        @if(auth()->user()->hasRole('Admin General') && $selectedRole == $conductorRoleId && $showSupervisorSelector)
                                            <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                                                <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-3">Asignación de Supervisor</h4>
                                                <label for="supervisor_id" class="block text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">Seleccionar Supervisor de la Unidad *</label>
                                                <select id="supervisor_id" wire:model="supervisor_id" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                    <option value="">Selecciona un Supervisor</option>
                                                    @if($supervisors && $supervisors->count() > 0)
                                                        @foreach($supervisors as $supervisor)
                                                            <option value="{{ $supervisor->id }}">
                                                                {{ $supervisor->nombre }} {{ $supervisor->apellido }}
                                                                @if($supervisor->unidadOrganizacional)
                                                                    ({{ $supervisor->unidadOrganizacional->siglas }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="" disabled>No hay Supervisores disponibles en esta unidad</option>
                                                    @endif
                                                </select>
                                                @error('supervisor_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">El conductor será asignado bajo la supervisión del Supervisor seleccionado.</p>
                                            </div>
                                        @endif

                                        {{-- Información automática para Supervisor creando Conductor --}}
                                        @if(auth()->user()->hasRole('Supervisor') && $selectedRole == $conductorRoleId)
                                            <div class="mt-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">Asignación Automática:</p>
                                                        <p class="text-sm text-green-700 dark:text-green-300">
                                                            El conductor será asignado automáticamente a tu unidad organizacional 
                                                            <span class="font-semibold">({{ auth()->user()->unidadOrganizacional->siglas ?? 'Sin unidad' }})</span>
                                                            y bajo tu supervisión directa.
                                                        </p>
                                                        <p class="text-sm text-green-700 dark:text-green-300 mt-2">
                                                            <strong>Nota:</strong> El usuario estará en estado "Pendiente" y requerirá aprobación de un administrador de la unidad.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Selector de Admin para Admin General creando Supervisor --}}
                                        @if(auth()->user()->hasRole('Admin General') && $selectedRole == $supervisorRoleId && $showAdminSelector)
                                            <div class="mt-4 p-4 bg-purple-50 dark:bg-purple-900 border border-purple-200 dark:border-purple-700 rounded-lg">
                                                <h4 class="text-lg font-semibold text-purple-900 dark:text-purple-100 mb-3">Asignación de Supervisor del Supervisor</h4>
                                                <label for="supervisor_id" class="block text-sm font-medium text-purple-800 dark:text-purple-200 mb-2">Seleccionar Admin de la Unidad *</label>
                                                <select id="supervisor_id" wire:model="supervisor_id" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                                                    <option value="">Selecciona un Admin</option>
                                                    @if($admins && $admins->count() > 0)
                                                        @foreach($admins as $admin)
                                                            <option value="{{ $admin->id }}">
                                                                {{ $admin->nombre }} {{ $admin->apellido }}
                                                                @if($admin->unidadOrganizacional)
                                                                    ({{ $admin->unidadOrganizacional->siglas }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="" disabled>No hay Admins disponibles en esta unidad</option>
                                                    @endif
                                                </select>
                                                @error('supervisor_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                                <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                                                    El nuevo Supervisor será asignado bajo la supervisión del Admin seleccionado de esta unidad organizacional.
                                                </p>
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Crear Usuario
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