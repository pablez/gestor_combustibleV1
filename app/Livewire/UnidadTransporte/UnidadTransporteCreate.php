<?php

namespace App\Livewire\UnidadTransporte;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class UnidadTransporteCreate extends Component
{
    use AuthorizesRequests;
    public function render()
    {
        return view('livewire.unidad-transporte.unidad-transporte-create');
    }
}
