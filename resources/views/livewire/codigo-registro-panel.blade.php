<div x-data="{ showForm: @entangle('formVisible'), showHistorial: @entangle('historialVisible') }">
    {{-- Notificación global de error encima del modal --}}
    @if(session()->has('error'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-[100] w-full max-w-lg px-4">
            <div class="bg-red-100 border border-red-300 text-red-800 rounded-lg shadow-lg p-4 animate__animated animate__fadeInDown">
                <div class="flex items-center mb-2">
                    <span class="font-bold text-lg mr-2">✖</span>
                    <span class="font-semibold">Error</span>
                </div>
                <div class="text-sm">{{ session('error') }}</div>
            </div>
        </div>
    @endif
    {{-- Mensaje de éxito --}}
    @if(session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-2 shadow animate__animated animate__fadeInDown">
            <span class="font-semibold">✔</span> {{ session('success') }}
        </div>
    @endif

    @php
        $user = auth()->user();
    @endphp

    <div class="flex flex-col md:flex-row gap-4 mb-6">
        {{-- Código vigente --}}
        <div class="w-full md:w-1/2">
            @php
                $codigosVigentes = App\Models\CodigoRegistro::where('vigente_hasta', '>', now())
                    ->where('usado', false)
                    ->where('creado_por', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->get();
            @endphp
            
            @if($codigosVigentes->count() > 0)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 shadow animate__animated animate__fadeIn">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold">🔑 Tus códigos de registro vigentes</h4>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">
                            {{ $codigosVigentes->count() }} {{ $codigosVigentes->count() == 1 ? 'código' : 'códigos' }}
                        </span>
                    </div>
                    
                    @if($codigosVigentes->count() == 1)
                        {{-- Mostrar un solo código grande --}}
                        <p class="font-mono text-lg mb-1 text-2xl tracking-widest bg-white p-2 rounded border">{{ $codigosVigentes->first()->codigo }}</p>
                        <div class="flex justify-between items-center text-xs text-green-600">
                            <span>Válido hasta: {{ $codigosVigentes->first()->vigente_hasta->format('d/m/Y H:i') }}</span>
                            <span>{{ $codigosVigentes->first()->rol_solicitado }}</span>
                        </div>
                    @else
                        {{-- Mostrar lista de códigos múltiples --}}
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach($codigosVigentes as $codigo)
                                <div class="bg-white p-2 rounded border">
                                    <div class="flex justify-between items-center">
                                        <span class="font-mono text-sm">{{ $codigo->codigo }}</span>
                                        <span class="text-xs text-gray-600">{{ $codigo->rol_solicitado }}</span>
                                    </div>
                                    <div class="text-xs text-green-600">
                                        Expira: {{ $codigo->vigente_hasta->format('d/m H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 shadow animate__animated animate__fadeIn">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-yellow-700">No tienes códigos de registro vigentes.</span>
                    </div>
                    <small class="text-yellow-600 text-xs block mt-1">Puedes generar múltiples códigos según necesites.</small>
                </div>
            @endif
        </div>

        {{-- Mini Historial de códigos --}}
        <div class="w-full md:w-1/2">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow">
                <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        Últimos códigos generados
                    </h3>
                    <button 
                        wire:click="toggleHistorial" 
                        class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition"
                    >
                        {{ $historialVisible ? 'Ocultar' : 'Mostrar' }}
                    </button>
                </div>
                <div 
                    x-show="showHistorial"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                >
                    @if(count($ultimosCodigos) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código</th>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol</th>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unidad</th>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    </tr>
                                </thead>
                                {{-- Reemplazar la sección de la tabla en el historial --}}
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($ultimosCodigos as $codigo)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-2 py-2 whitespace-nowrap font-mono">{{ $codigo->codigo }}</td>
                                            <td class="px-2 py-2 whitespace-nowrap">{{ $codigo->rol_solicitado }}</td>
                                            <td class="px-2 py-2 whitespace-nowrap">{{ $codigo->unidadOrganizacional->siglas ?? 'N/A' }}</td>
                                            <td class="px-2 py-2 whitespace-nowrap">
                                                @if($codigo->usado)
                                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full">Usado</span>
                                                @elseif($codigo->vigente_hasta > now())
                                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">
                                                        Vigente ({{ $codigo->vigente_hasta->diffForHumans() }})
                                                    </span>
                                                @else
                                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full">Vencido</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Filtros simples para Admin General y Admin --}}
                        @if($user->hasAnyRole(['Admin General', 'Admin']))
                            <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Filtros rápidos</div>
                                <div class="flex flex-wrap gap-2">
                                    <select wire:model="filtroRol" wire:change="aplicarFiltros" class="text-xs rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                        <option value="">Todos los roles</option>
                                        <option value="Admin General">Admin General</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Supervisor">Supervisor</option>
                                        <option value="Conductor/Operador">Conductor/Operador</option>
                                    </select>
                                    
                                    <button 
                                        wire:click="resetFiltros" 
                                        class="text-xs bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 px-2 py-1 rounded"
                                    >
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        @endif
                        
                        <div class="p-3 text-center text-xs text-gray-500 dark:text-gray-400">
                            <a href="{{ route('historial-codigos') }}" class="text-blue-600 hover:underline">
                                Ver historial completo
                            </a>
                        </div>
                    @else
                        <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No hay códigos generados recientemente.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @php
        $user = auth()->user();
    @endphp

    @if($user && ($user->hasRole('Admin General') || $user->hasRole('Admin') || $user->hasRole('Supervisor')))
        <div>
            @if(!$formVisible)
                <button 
                    wire:click="$set('formVisible', true)" 
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1.5 px-3 rounded shadow-sm transition-all duration-200 transform hover:scale-105"
                >
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Generar nuevo código
                    </span>
                </button>
            @endif

            {{-- Modal con formulario --}}
            <div 
                x-show="showForm" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
                style="display: none;"
            >
                <div 
                    x-show="showForm"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    @click.away="$wire.set('formVisible', false)"
                    class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl p-6 w-full max-w-md border border-blue-100 dark:border-blue-900 mx-4"
                >
                    {{-- Errores de validación dentro del modal --}}
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-300 text-red-800 rounded-lg shadow-lg p-3 mb-2 animate__animated animate__fadeInDown">
                            <div class="flex items-center mb-1">
                                <span class="font-bold text-lg mr-2">✖</span>
                                <span class="font-semibold">Errores de validación</span>
                            </div>
                            <ul class="list-disc pl-5 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form wire:submit.prevent="generarCodigo" class="space-y-4">
                        
                        <h3 class="text-lg font-bold text-blue-700 dark:text-blue-300 mb-4 text-center">
                            Datos para generar código
                        </h3>
                        
                        {{-- 1. Selección de unidad organizacional --}}
                        @if($user->hasRole('Admin General'))
                            <div class="space-y-2">
                                <label for="unidad_organizacional_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Unidad organizacional <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    wire:model.live="unidad_organizacional_id" 
                                    id="unidad_organizacional_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                >
                                    <option value="">Seleccione una unidad</option>
                                    @foreach(App\Models\UnidadOrganizacional::where('activa', true)->get() as $unidad)
                                        {{-- Admin General puede ver todas las unidades EXCEPTO la suya --}}
                                        @if($unidad->id_unidad_organizacional != $user->unidad_organizacional_id)
                                            <option value="{{ $unidad->id_unidad_organizacional }}">
                                                {{ $unidad->nombre_unidad }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('unidad_organizacional_id') 
                                    <span class="text-red-500 text-xs animate-pulse">{{ $message }}</span> 
                                @enderror
                            </div>
                        @elseif($user->hasRole('Admin') || $user->hasRole('Supervisor'))
                            <div class="space-y-2">
                                <label for="unidad_organizacional_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Unidad organizacional
                                </label>
                                <select 
                                    wire:model.live="unidad_organizacional_id" 
                                    id="unidad_organizacional_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-100 cursor-not-allowed"
                                    readonly
                                >
                                    @if($user->unidadOrganizacional)
                                        <option value="{{ $user->unidadOrganizacional->id_unidad_organizacional }}" selected>
                                            {{ $user->unidadOrganizacional->nombre_unidad }}
                                        </option>
                                    @else
                                        <option value="">Sin unidad asignada</option>
                                    @endif
                                </select>
                                @if($user->hasRole('Supervisor'))
                                    <small class="text-gray-500 text-xs">Como Supervisor, utilizarás tu unidad organizacional actual</small>
                                @else
                                    <small class="text-gray-500 text-xs">Solo puedes crear códigos para tu unidad organizacional</small>
                                @endif
                                @error('unidad_organizacional_id') 
                                    <span class="text-red-500 text-xs animate-pulse">{{ $message }}</span> 
                                @enderror
                            </div>
                        @endif

                        {{-- 2. Selección de rol --}}
                        @if($user->hasRole('Admin General'))
                            {{-- Para Admin General: aparece solo cuando selecciona unidad --}}
                            <div 
                                x-show="$wire.unidad_organizacional_id" 
                                x-transition:enter="transition ease-out duration-500 transform"
                                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-300 transform"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                                class="space-y-2"
                                style="display: none;"
                            >
                                <label for="rol_solicitado" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Rol solicitado <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    wire:model.live="rol_solicitado" 
                                    id="rol_solicitado" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                >
                                    <option value="">Seleccione un rol</option>
                                    @if(!empty($rolesPermitidos))
                                        @foreach($rolesPermitidos as $rol)
                                            <option value="{{ $rol }}">{{ $rol }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('rol_solicitado') 
                                    <span class="text-red-500 text-xs animate-pulse">{{ $message }}</span> 
                                @enderror
                            </div>
                        @else
                            {{-- Para Admin y Supervisor: aparece siempre --}}
                            <div class="space-y-2">
                                <label for="rol_solicitado" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Rol solicitado <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    wire:model="rol_solicitado" 
                                    id="rol_solicitado" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @if($user->hasRole('Supervisor')) bg-gray-100 @endif"
                                    @if($user->hasRole('Supervisor')) readonly @endif
                                >
                                    @if($user->hasRole('Supervisor'))
                                        <option value="Conductor/Operador" selected>Conductor/Operador</option>
                                    @else
                                        <option value="">Seleccione un rol</option>
                                        @if(!empty($rolesPermitidos))
                                            @foreach($rolesPermitidos as $rol)
                                                <option value="{{ $rol }}">{{ $rol }}</option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                                @if($user->hasRole('Supervisor'))
                                    <small class="text-gray-500 text-xs">Como Supervisor, estás autorizado para crear usuarios Conductor/Operador</small>
                                @endif
                                @error('rol_solicitado') 
                                    <span class="text-red-500 text-xs animate-pulse">{{ $message }}</span> 
                                @enderror
                            </div>
                        @endif

                        {{-- 3. Selección de supervisor, aparece después de seleccionar rol --}}
                        <div 
                            @if($user->hasRole('Admin General'))
                                x-show="($wire.rol_solicitado === 'Conductor/Operador' || $wire.rol_solicitado === 'Supervisor') && $wire.unidad_organizacional_id"
                            @elseif($user->hasRole('Admin'))
                                x-show="$wire.rol_solicitado === 'Conductor/Operador'"
                            @elseif($user->hasRole('Supervisor'))
                                x-show="false"
                            @else
                                x-show="false"
                            @endif
                            x-transition:enter="transition ease-out duration-500 transform"
                            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-300 transform"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                            class="space-y-2"
                            style="display: none;"
                        >
                            <label for="supervisor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                @if($user->hasRole('Admin General') && $rol_solicitado === 'Supervisor')
                                    Supervisor (Admin de la unidad) <span class="text-red-500">*</span>
                                @else
                                    Supervisor
                                    @if($rol_solicitado === 'Conductor/Operador')
                                        <span class="text-red-500">*</span>
                                    @endif
                                @endif
                            </label>
                            <select 
                                wire:model="supervisor_id" 
                                id="supervisor_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            >
                                @if($user->hasRole('Admin General') && $rol_solicitado === 'Supervisor')
                                    <option value="">Seleccione el Admin supervisor</option>
                                @else
                                    <option value="">Seleccione supervisor</option>
                                @endif
                                @if(!empty($supervisoresUnidad))
                                    @foreach($supervisoresUnidad as $supervisor)
                                        <option value="{{ $supervisor->id }}">
                                            {{ $supervisor->nombre }} {{ $supervisor->apellido }}
                                            @if($user->hasRole('Admin General') && $rol_solicitado === 'Supervisor')
                                                (Admin)
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if($user->hasRole('Admin General') && $rol_solicitado === 'Supervisor')
                                <small class="text-blue-600 text-xs">Selecciona el Admin que supervisará a este Supervisor</small>
                            @endif
                            @error('supervisor_id') 
                                <span class="text-red-500 text-xs animate-pulse">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- 4. Información del supervisor asignado automáticamente --}}
                        <div 
                            @if($user->hasRole('Admin'))
                                x-show="$wire.rol_solicitado === 'Supervisor' || ($wire.rol_solicitado === 'Admin General' || $wire.rol_solicitado === 'Admin')"
                            @elseif($user->hasRole('Admin General'))
                                x-show="$wire.rol_solicitado === 'Admin General' || $wire.rol_solicitado === 'Admin'"
                            @elseif($user->hasRole('Supervisor'))
                                x-show="true"
                            @else
                                x-show="false"
                            @endif
                            x-transition:enter="transition ease-out duration-500 transform"
                            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-300 transform"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                            class="space-y-2 bg-blue-50 p-3 rounded-lg border border-blue-100"
                        >
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Supervisor asignado
                            </label>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Supervisor asignado
                            </label>
                            <div class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 bg-gray-100 px-3 py-2 text-sm text-gray-600 dark:text-gray-300">
                                @if($user->hasRole('Admin') && ($rol_solicitado === 'Supervisor' || $rol_solicitado === 'Admin General' || $rol_solicitado === 'Admin'))
                                    {{ $user->nombre }} {{ $user->apellido }} (Tú)
                                @elseif($user->hasRole('Admin General') && ($rol_solicitado === 'Admin General' || $rol_solicitado === 'Admin'))
                                    {{ $user->nombre }} {{ $user->apellido }} (Admin General)
                                @elseif($user->hasRole('Supervisor') && $rol_solicitado === 'Conductor/Operador')
                                    {{ $user->nombre }} {{ $user->apellido }} (Tú - Supervisor)
                                @elseif($user->hasRole('Supervisor'))
                                    {{ $user->nombre }} {{ $user->apellido }} (Tú - Supervisor)
                                @endif
                            <small class="text-gray-500 text-xs">
                                @if($user->hasRole('Admin'))
                                    Como Admin, serás automáticamente el supervisor de este usuario.
                                @elseif($user->hasRole('Admin General'))
                                    Como Admin General, serás automáticamente el supervisor de este usuario.
                                @elseif($user->hasRole('Supervisor'))
                                    Como Supervisor, serás automáticamente el supervisor de este Conductor/Operador.
                                @endif
                            </small>
                        </div>

                        {{-- Botones del formulario --}}
                        <div class="flex gap-3 justify-center pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button 
                                type="submit" 
                                wire:loading.attr="disabled"
                                x-data="{ pressed: false }"
                                @mousedown="pressed = true"
                                @mouseup="setTimeout(() => pressed = false, 300)"
                                :class="pressed ? 'animate-bounce bg-blue-600 scale-95' : 'bg-blue-500 hover:bg-blue-600 scale-100'"
                                class="disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-2 px-4 rounded-md shadow-sm transition-all duration-300 transform focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                <span wire:loading.remove>
                                    @if($user->hasRole('Supervisor'))
                                        Generar código para Conductor/Operador
                                    @else
                                        Guardar y generar
                                    @endif
                                </span>
                                <span wire:loading class="inline-flex items-center gap-2">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Generando...
                                </span>
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('formVisible', false)" 
                                x-data="{ pressed: false }"
                                @mousedown="pressed = true"
                                @mouseup="setTimeout(() => pressed = false, 300)"
                                :class="pressed ? 'animate-bounce bg-gray-300 scale-95' : 'bg-gray-200 hover:bg-gray-300 scale-100 dark:bg-gray-700 dark:hover:bg-gray-600'"
                                class="text-gray-800 dark:text-gray-200 font-semibold py-2 px-4 rounded-md shadow-sm transition-all duration-300 transform focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>