<?php

namespace App\Livewire\UnidadTransporte;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class UnidadTransporteEdit extends Component
{
    use AuthorizesRequests;
    public function render()
    {
        return view('livewire.unidad-transporte.unidad-transporte-edit');
    }
}
