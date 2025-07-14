<div>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800 leading-tight">
            Registrar Nueva Unidad de Transporte
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form wire:submit.prevent="saveUnitTransport">
                        <div class="mb-4">
                            <label for="tipo_unidad" class="block text-sm font-medium text-gray-700">Tipo de Unidad</label>
                            <input type="text" id="tipo_unidad" wire:model="tipo_unidad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('tipo_unidad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="placa_identificador" class="block text-sm font-medium text-gray-700">Placa / Identificador</label>
                            <input type="text" id="placa_identificador" wire:model="placa_identificador" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('placa_identificador') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="marca" class="block text-sm font-medium text-gray-700">Marca</label>
                            <input type="text" id="marca" wire:model="marca" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('marca') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="modelo" class="block text-sm font-medium text-gray-700">Modelo</label>
                            <input type="text" id="modelo" wire:model="modelo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('modelo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="anio" class="block text-sm font-medium text-gray-700">Año</label>
                            <input type="number" id="anio" wire:model="anio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="1900" max="{{ date('Y') + 1 }}">
                            @error('anio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tipo_combustible" class="block text-sm font-medium text-gray-700">Tipo de Combustible</label>
                            <select id="tipo_combustible" wire:model="tipo_combustible" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Seleccione...</option>
                                <option value="Gasolina">Gasolina</option>
                                <option value="Diesel">Diesel</option>
                                <option value="GNV">GNV</option>
                                <option value="Electrico">Eléctrico</option>
                                <option value="Otros">Otros</option>
                            </select>
                            @error('tipo_combustible') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="capacidad_tanque_litros" class="block text-sm font-medium text-gray-700">Capacidad Tanque (Litros)</label>
                            <input type="number" step="0.01" id="capacidad_tanque_litros" wire:model="capacidad_tanque_litros" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="0.01">
                            @error('capacidad_tanque_litros') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="estado_operativo" class="block text-sm font-medium text-gray-700">Estado Operativo</label>
                            <select id="estado_operativo" wire:model="estado_operativo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="Operativo">Operativo</option>
                                <option value="En Mantenimiento">En Mantenimiento</option>
                                <option value="Fuera de Servicio">Fuera de Servicio</option>
                            </select>
                            @error('estado_operativo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="kilometraje_actual" class="block text-sm font-medium text-gray-700">Kilometraje Actual (km)</label>
                            <input type="number" step="0.01" id="kilometraje_actual" wire:model="kilometraje_actual" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="0">
                            @error('kilometraje_actual') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.units.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Registrar Unidad
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>