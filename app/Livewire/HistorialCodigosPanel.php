<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CodigoRegistro;
use Livewire\WithPagination;

class HistorialCodigosPanel extends Component
{
    use WithPagination;
    
    public $filtroRol = '';
    public $filtroUnidad = '';
    public $filtroSupervisor = '';
    public $filtroCodigo = '';
    public $filtroEstado = ''; // 'todos', 'vigentes', 'usados', 'vencidos'
    
    public function resetFiltros()
    {
        $this->reset(['filtroRol', 'filtroUnidad', 'filtroSupervisor', 'filtroCodigo', 'filtroEstado']);
    }
    
    public function aplicarFiltros()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $user = auth()->user();
        
        $query = CodigoRegistro::with(['creador', 'unidadOrganizacional', 'supervisor'])
            ->orderBy('created_at', 'desc');
            
        // Filtrar según la jerarquía del usuario
        if ($user->hasRole('Admin General')) {
            // Admin General ve todos los códigos
            // No necesitamos filtros adicionales
        } elseif ($user->hasRole('Admin')) {
            // Admin ve códigos de su unidad y los propios
            $query->where(function($q) use ($user) {
                $q->where('unidad_organizacional_id', $user->unidad_organizacional_id)
                  ->orWhere('creado_por', $user->id);
            });
        } elseif ($user->hasRole('Supervisor')) {
            // Supervisor solo ve sus propios códigos
            $query->where('creado_por', $user->id);
        } else {
            // Otros roles no ven códigos
            $query->where('id', 0); // Consulta que no devuelve resultados
        }
        
        // Aplicar filtros si están definidos
        if ($this->filtroRol) {
            $query->where('rol_solicitado', $this->filtroRol);
        }
        
        if ($this->filtroUnidad) {
            $query->where('unidad_organizacional_id', $this->filtroUnidad);
        }
        
        if ($this->filtroSupervisor) {
            $query->where('supervisor_id', $this->filtroSupervisor);
        }
        
        if ($this->filtroCodigo) {
            $query->where('codigo', 'like', '%' . $this->filtroCodigo . '%');
        }
        
        if ($this->filtroEstado) {
            $now = now();
            
            if ($this->filtroEstado === 'vigentes') {
                $query->where('vigente_hasta', '>', $now)
                      ->where('usado', false);
            } elseif ($this->filtroEstado === 'usados') {
                $query->where('usado', true);
            } elseif ($this->filtroEstado === 'vencidos') {
                $query->where('vigente_hasta', '<=', $now)
                      ->where('usado', false);
            }
        }
        
        $codigos = $query->paginate(10);
        
        $unidades = \App\Models\UnidadOrganizacional::where('activa', true)->get();
        $supervisores = \App\Models\User::role(['Supervisor', 'Admin', 'Admin General'])->get();
        
        return view('livewire.historial-codigos-panel', [
            'codigos' => $codigos,
            'unidades' => $unidades,
            'supervisores' => $supervisores,
            'roles' => [ 'Admin', 'Supervisor', 'Conductor/Operador']
        ]);
    }
}
