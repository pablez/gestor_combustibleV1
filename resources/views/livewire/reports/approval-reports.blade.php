<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reportes de Aprobaciones de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Indicador de contexto del usuario --}}
            <div class="mb-6 p-4 @if(auth()->user()->hasRole('Admin General')) bg-purple-50 dark:bg-purple-900 border-purple-200 dark:border-purple-700 @else bg-blue-50 dark:bg-blue-900 border-blue-200 dark:border-blue-700 @endif border rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 @if(auth()->user()->hasRole('Admin General')) text-purple-600 dark:text-purple-400 @else text-blue-600 dark:text-blue-400 @endif mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium @if(auth()->user()->hasRole('Admin General')) text-purple-800 dark:text-purple-200 @else text-blue-800 dark:text-blue-200 @endif">
                            Reporte de {{ auth()->user()->roles->first()->name }} - {{ $estadisticas['contexto'] }}
                        </p>
                        <p class="text-sm @if(auth()->user()->hasRole('Admin General')) text-purple-700 dark:text-purple-300 @else text-blue-700 dark:text-blue-300 @endif">
                            {{ $estadisticas['alcance'] }}
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Filtros --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Filtros de Reporte</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Inicio</label>
                            <input type="date" wire:model.live="fechaInicio" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Fin</label>
                            <input type="date" wire:model.live="fechaFin" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                            <select wire:model.live="filtroEstado" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600">
                                <option value="todos">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="aprobado">Aprobado</option>
                                <option value="rechazado">Rechazado</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Creador</label>
                            <select wire:model.live="filtroRolCreador" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600">
                                <option value="todos">Todos</option>
                                @if(auth()->user()->hasRole('Admin General'))
                                    <option value="Admin">Admin</option>
                                @endif
                                <option value="Supervisor">Supervisor</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aprobador</label>
                            <select wire:model.live="filtroRolAprobador" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600">
                                <option value="todos">Todos</option>
                                <option value="Admin General">Admin General</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unidad</label>
                            <select wire:model.live="filtroUnidad" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600">
                                <option value="todas">
                                    @if(auth()->user()->hasRole('Admin General'))
                                        Todas
                                    @else
                                        Mi Unidad
                                    @endif
                                </option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id_unidad_organizacional }}">{{ $unidad->siglas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Estadísticas específicas por rol --}}
            @if(auth()->user()->hasRole('Admin General'))
                {{-- Estadísticas para Admin General --}}
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sistema</h4>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $estadisticas['total'] }}</p>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pendientes</h4>
                        <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $estadisticas['pendientes'] }}</p>
                        <div class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                            <div>Supervisores: {{ $estadisticas['supervisores_pendientes'] ?? 0 }}</div>
                            <div>Conductores: {{ $estadisticas['conductores_pendientes'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-green-600 dark:text-green-400">Aprobados</h4>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $estadisticas['aprobados'] }}</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-red-600 dark:text-red-400">Rechazados</h4>
                        <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ $estadisticas['rechazados'] }}</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-purple-600 dark:text-purple-400">Aprobé</h4>
                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $estadisticas['aprobados_por_mi'] }}</p>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-blue-600 dark:text-blue-400">Tiempo Promedio</h4>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ round($estadisticas['tiempo_promedio_procesamiento'] ?? 0, 1) }} días</p>
                    </div>
                </div>
            @else
                {{-- Estadísticas para Admin --}}
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Mi Unidad</h4>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $estadisticas['total'] }}</p>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pendientes</h4>
                        <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $estadisticas['pendientes_unidad'] ?? 0 }}</p>
                        <div class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">De mi unidad</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-green-600 dark:text-green-400">Aprobados</h4>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $estadisticas['total_aprobados_unidad'] ?? 0 }}</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-red-600 dark:text-red-400">Rechazados</h4>
                        <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ $estadisticas['total_rechazados_unidad'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-blue-600 dark:text-blue-400">Aprobé</h4>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $estadisticas['aprobados_por_mi'] }}</p>
                    </div>
                </div>
            @endif

            {{-- Tabla de datos --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Rol Solicitado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Creado por</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Aprobado por</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tiempo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fechas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($datosReporte as $solicitud)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full object-cover mr-3" src="{{ $solicitud->usuario->foto_perfil_url ?? asset('images/foto-perfil.png') }}" alt="Foto">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $solicitud->usuario->nombre ?? 'Usuario eliminado' }} {{ $solicitud->usuario->apellido ?? '' }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $solicitud->usuario->email ?? $solicitud->datos_usuario['email'] ?? 'Sin email' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($solicitud->rol_solicitado == 'Supervisor') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 
                                            @elseif($solicitud->rol_solicitado == 'Conductor/Operador') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                            {{ $solicitud->rol_solicitado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($solicitud->creador)
                                            <div class="flex items-center">
                                                <img class="h-6 w-6 rounded-full object-cover mr-2" src="{{ $solicitud->creador->foto_perfil_url }}" alt="Creador">
                                                <div>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $solicitud->creador->nombre }} {{ $solicitud->creador->apellido }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $solicitud->rol_creador }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">Sin información</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($solicitud->aprobador)
                                            <div class="flex items-center">
                                                <img class="h-6 w-6 rounded-full object-cover mr-2" src="{{ $solicitud->aprobador->foto_perfil_url }}" alt="Aprobador">
                                                <div>
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $solicitud->aprobador->nombre }} {{ $solicitud->aprobador->apellido }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $solicitud->rol_aprobador }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $solicitud->getClaseBadgeEstado() }}">
                                            {{ ucfirst($solicitud->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($solicitud->unidadOrganizacional)
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $solicitud->unidadOrganizacional->siglas }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $solicitud->unidadOrganizacional->tipo_unidad }}</div>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">Sin unidad</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $solicitud->getTiempoProcesamiento() }} días
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div>Creado: {{ $solicitud->created_at->format('d/m/Y H:i') }}</div>
                                        @if($solicitud->fecha_aprobacion)
                                            <div>{{ $solicitud->estado === 'aprobado' ? 'Aprobado' : 'Rechazado' }}: {{ $solicitud->fecha_aprobacion->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="text-gray-500 dark:text-gray-400">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No hay datos para mostrar</p>
                                            <p class="text-sm">
                                                @if(auth()->user()->hasRole('Admin General'))
                                                    No hay solicitudes de aprobación en el rango de fechas seleccionado.
                                                @else
                                                    No hay solicitudes que puedas ver en el rango de fechas seleccionado.
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>