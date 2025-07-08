<div>
<x-slot name="header">
    <h1 class="text-2xl font-semibold text-gray-800 leading-tight">
        Gestión de Unidades de Transporte
    </h1>
</x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session()->has('message'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('message') }}</span>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar unidades..." class="form-input rounded-md shadow-sm mt-1 block w-1/3">
                        <a href="{{ route('admin.units.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Registrar Nueva Unidad
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca/Modelo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Operativo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($units as $unit)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $unit->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $unit->placa_identificador }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $unit->tipo_unidad }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $unit->marca }} {{ $unit->modelo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $unit->anio }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($unit->kilometraje_actual, 2) }} km</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($unit->estado_operativo == 'Operativo') bg-green-100 text-green-800
                                                @elseif($unit->estado_operativo == 'En Mantenimiento') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $unit->estado_operativo }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.units.edit', $unit) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>
                                            <button wire:click="deleteUnit({{ $unit->id }})" wire:confirm="¿Estás seguro de que quieres eliminar esta unidad de transporte?" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No se encontraron unidades de transporte.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $units->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>