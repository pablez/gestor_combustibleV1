<?php

namespace App\Livewire\UnidadTransporte;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\UnidadTransporte;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UnidadTransporteCreate extends Component
{
    use AuthorizesRequests;

    public $tipo_unidad = '';
    public $placa_identificador = '';
    public $marca = '';
    public $modelo = '';
    public $anio = '';
    public $tipo_combustible = ''; // Se inicializará con el primer valor del enum si se desea
    public $capacidad_tanque_litros = '';
    public $estado_operativo = 'Operativo'; // Valor por defecto
    public $kilometraje_actual = 0;

    protected function rules()
    {
        return [
            'tipo_unidad' => ['required', 'string', 'max:100'],
            'placa_identificador' => ['required', 'string', 'max:50', 'unique:unidad_transportes,placa_identificador'],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
            'anio' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)], // Año actual + 1
            'tipo_combustible' => ['required', Rule::in(['Gasolina', 'Diesel', 'GNV', 'Electrico', 'Otros'])],
            'capacidad_tanque_litros' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'estado_operativo' => ['required', Rule::in(['Operativo', 'En Mantenimiento', 'Fuera de Servicio'])],
            'kilometraje_actual' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function mount()
    {
        // Autorizar la acción de crear unidades al cargar el componente
        $this->authorize('crear unidades');
        // Puedes inicializar 'tipo_combustible' si quieres un valor por defecto en el select
        // $this->tipo_combustible = 'Gasolina';
    }

    public function saveUnitTransport()
    {
        // Autorizar la acción de crear unidades antes de guardar
        $this->authorize('crear unidades');

        $this->validate(); // Valida los datos del formulario

        // Crear la nueva unidad de transporte
        UnidadTransporte::create([
            'tipo_unidad' => $this->tipo_unidad,
            'placa_identificador' => $this->placa_identificador,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'anio' => $this->anio,
            'tipo_combustible' => $this->tipo_combustible,
            'capacidad_tanque_litros' => $this->capacidad_tanque_litros,
            'estado_operativo' => $this->estado_operativo,
            'kilometraje_actual' => $this->kilometraje_actual,
        ]);

        // Redirigir de vuelta a la lista de unidades con un mensaje de éxito
        session()->flash('message', 'Unidad de transporte registrada correctamente.');
        return redirect()->route('admin.units.index');
    }

    public function render()
    {
        return view('livewire.unidad-transporte.unidad-transporte-create');
    }
}
