<?php

namespace App\Livewire\UnidadTransporte;

use App\Models\UnidadTransporte;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UnidadTransporteIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $perPage = 10;

    protected $queryString = ['search' => ['except' => ''], 'perPage'];

    // Método para resetear la paginación cuando cambia la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Método para eliminar una unidad de transporte
    public function deleteUnit(UnidadTransporte $unitTransport)
    {
        // Autorizar la acción de eliminar unidades
        $this->authorize('eliminar unidades');

        try {
            $unitTransport->delete();
            session()->flash('message', 'Unidad de transporte eliminada correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejar errores de clave foránea si la unidad está en uso
            if ($e->getCode() == 23000) { // Código SQLSTATE para integridad de datos
                session()->flash('error', 'No se puede eliminar la unidad porque está asociada a otros registros (ej. solicitudes, mantenimientos).');
            } else {
                session()->flash('error', 'Ocurrió un error al intentar eliminar la unidad: ' . $e->getMessage());
            }
        }
    }



    public function render()
    {
        // Autorizar la acción de ver unidades
        $this->authorize('ver unidades');

        $units = UnidadTransporte::query()
            ->when($this->search, function ($query) {
                $query->where('placa_identificador', 'like', '%' . $this->search . '%')
                        ->orWhere('tipo_unidad', 'like', '%' . $this->search . '%')
                        ->orWhere('marca', 'like', '%' . $this->search . '%')
                        ->orWhere('modelo', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.unidad-transporte.unidad-transporte-index', [
            'units' => $units,
        ]);
    }
}
