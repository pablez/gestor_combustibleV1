<div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-4 sm:p-6">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Historial de Códigos de Registro</h1>
                
                {{-- Filtros --}}
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 mb-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 sm:mb-0">Filtros</h3>
                        <button 
                            wire:click="resetFiltros" 
                            class="text-xs bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 px-2 py-1 rounded"
                        >
                            Limpiar filtros
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        {{-- Filtro por rol --}}
                        <div>
                            <label for="filtroRol" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Rol</label>
                            <select 
                                wire:model="filtroRol" 
                                id="filtroRol" 
                                class="block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800"
                            >
                                <option value="">Todos los roles</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol }}">{{ $rol }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Filtro por unidad --}}
                        <div>
                            <label for="filtroUnidad" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Unidad</label>
                            <select 
                                wire:model="filtroUnidad" 
                                id="filtroUnidad" 
                                class="block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800"
                            >
                                <option value="">Todas las unidades</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id_unidad_organizacional }}">{{ $unidad->siglas }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Filtro por supervisor --}}
                        <div>
                            <label for="filtroSupervisor" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Supervisor</label>
                            <select 
                                wire:model="filtroSupervisor" 
                                id="filtroSupervisor" 
                                class="block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800"
                            >
                                <option value="">Todos los supervisores</option>
                                @foreach($supervisores as $supervisor)
                                    <option value="{{ $supervisor->id }}">{{ $supervisor->nombre }} {{ $supervisor->apellido }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Filtro por código --}}
                        <div>
                            <label for="filtroCodigo" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Código</label>
                            <input 
                                wire:model="filtroCodigo" 
                                id="filtroCodigo" 
                                type="text" 
                                placeholder="Buscar por código" 
                                class="block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800"
                            >
                        </div>
                        
                        {{-- Filtro por estado --}}
                        <div>
                            <label for="filtroEstado" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                            <select 
                                wire:model="filtroEstado" 
                                id="filtroEstado" 
                                class="block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800"
                            >
                                <option value="">Todos los estados</option>
                                <option value="vigentes">Vigentes</option>
                                <option value="usados">Usados</option>
                                <option value="vencidos">Vencidos</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <button 
                            wire:click="aplicarFiltros" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Aplicar filtros
                        </button>
                    </div>
                </div>
                
                {{-- Tabla de códigos --}}
                <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supervisor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Creado por</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($codigos as $codigo)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 whitespace-nowrap font-mono">{{ $codigo->codigo }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $codigo->rol_solicitado }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $codigo->unidadOrganizacional->siglas ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        @if($codigo->supervisor)
                                            {{ $codigo->supervisor->nombre }} {{ $codigo->supervisor->apellido }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        @if($codigo->creador)
                                            {{ $codigo->creador->nombre }} {{ $codigo->creador->apellido }}
                                        @else
                                            Usuario eliminado
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div>{{ $codigo->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $codigo->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        @if($codigo->usado)
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full">Usado</span>
                                        @elseif($codigo->vigente_hasta > now())
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">Vigente</span>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Hasta: {{ $codigo->vigente_hasta->format('d/m/Y H:i') }}
                                            </div>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full">Vencido</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No se encontraron códigos de registro.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $codigos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
