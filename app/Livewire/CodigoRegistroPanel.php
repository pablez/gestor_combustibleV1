<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CodigoRegistro;
use Illuminate\Support\Str;

class CodigoRegistroPanel extends Component
{
    public $codigo;
    public $vigente_hasta;

    public function mount()
    {
        $this->actualizarCodigo();
    }

    public function actualizarCodigo()
    {
        $registro = CodigoRegistro::where('vigente_hasta', '>', now())
            ->latest('created_at')
            ->first();

        if ($registro) {
            $this->codigo = $registro->codigo;
            $this->vigente_hasta = $registro->vigente_hasta;
        } else {
            $this->codigo = null;
            $this->vigente_hasta = null;
        }
    }

    public function generarCodigo()
    {
        if (!auth()->user()->hasRole('Admin General') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'No autorizado');
        }

        // Expira códigos anteriores
        CodigoRegistro::where('vigente_hasta', '>', now())->update(['vigente_hasta' => now()]);

        CodigoRegistro::create([
            'codigo' => Str::upper(Str::random(8)),
            'vigente_hasta' => now()->addMinutes(30),
        ]);
        $this->actualizarCodigo();
        session()->flash('success', '¡Nuevo código generado!');
    }

    public function render()
    {
        return view('livewire.codigo-registro-panel');
    }
}
