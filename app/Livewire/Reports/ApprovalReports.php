<?php

namespace App\Livewire\Reports;

use App\Models\UserApprovalRequest;
use App\Models\UnidadOrganizacional;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApprovalReports extends Component
{
    public $fechaInicio;
    public $fechaFin;
    public $filtroEstado = 'todos';
    public $filtroRolCreador = 'todos';
    public $filtroRolAprobador = 'todos';
    public $filtroUnidad = 'todas';

    public function mount()
    {
        // Verificar permisos de acceso a reportes
        if (!auth()->user()->hasRole('Admin General') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'No tienes permisos para ver reportes de aprobaciones.');
        }
        
        $this->fechaInicio = now()->subMonth()->format('Y-m-d');
        $this->fechaFin = now()->format('Y-m-d');
    }

    public function obtenerDatosReporte()
    {
        $usuarioActual = Auth::user();
        
        $consulta = UserApprovalRequest::with([
            'usuario', 'creador', 'aprobador', 'unidadOrganizacional'
        ])->enRangoFechas($this->fechaInicio, $this->fechaFin);

        // Aplicar filtros según el rol del usuario
        if ($usuarioActual->hasRole('Admin General')) {
            // Admin General puede ver reportes de todos los usuarios aprobados de cualquier unidad organizacional
            // No aplicar filtros adicionales de permisos - puede ver todo el sistema
            
        } elseif ($usuarioActual->hasRole('Admin')) {
            // Admin puede ver reportes de todos los usuarios aprobados de su unidad organizacional
            $consulta->where('unidad_organizacional_id', $usuarioActual->unidad_organizacional_id);
        } else {
            // Otros roles no tienen acceso
            return collect();
        }

        // Aplicar filtros de la interfaz
        if ($this->filtroEstado !== 'todos') {
            $consulta->where('estado', $this->filtroEstado);
        }

        if ($this->filtroRolCreador !== 'todos') {
            $consulta->where('rol_creador', $this->filtroRolCreador);
        }

        if ($this->filtroRolAprobador !== 'todos') {
            $consulta->where('rol_aprobador', $this->filtroRolAprobador);
        }

        if ($this->filtroUnidad !== 'todas') {
            $consulta->where('unidad_organizacional_id', $this->filtroUnidad);
        }

        return $consulta->orderBy('created_at', 'desc')->get();
    }

    public function obtenerEstadisticas()
    {
        $datos = $this->obtenerDatosReporte();
        $usuarioActual = Auth::user();
        
        $estadisticas = [
            'total' => $datos->count(),
            'pendientes' => $datos->where('estado', 'pendiente')->count(),
            'aprobados' => $datos->where('estado', 'aprobado')->count(),
            'rechazados' => $datos->where('estado', 'rechazado')->count(),
            'tiempo_promedio_procesamiento' => $datos->where('estado', '!=', 'pendiente')->avg(function($item) {
                return $item->getTiempoProcesamiento();
            }),
            'por_rol_creador' => $datos->groupBy('rol_creador')->map->count(),
            'por_rol_aprobador' => $datos->where('estado', '!=', 'pendiente')->groupBy('rol_aprobador')->map->count(),
        ];

        // Estadísticas específicas según el rol
        if ($usuarioActual->hasRole('Admin General')) {
            $estadisticas['aprobados_por_mi'] = $datos->where('aprobado_por', $usuarioActual->id)->count();
            $estadisticas['contexto'] = 'Sistema Completo - Todas las Unidades';
            $estadisticas['alcance'] = 'Ve reportes de todos los usuarios aprobados de cualquier unidad organizacional';
            
            // Estadísticas adicionales para Admin General
            $estadisticas['por_unidad'] = $datos->groupBy('unidadOrganizacional.siglas')->map->count();
            $estadisticas['supervisores_pendientes'] = $datos->where('estado', 'pendiente')
                                                            ->where('rol_solicitado', 'Supervisor')
                                                            ->count();
            $estadisticas['conductores_pendientes'] = $datos->where('estado', 'pendiente')
                                                            ->where('rol_solicitado', 'Conductor/Operador')
                                                            ->count();
            
        } elseif ($usuarioActual->hasRole('Admin')) {
            $estadisticas['aprobados_por_mi'] = $datos->where('aprobado_por', $usuarioActual->id)->count();
            $estadisticas['de_mi_unidad'] = $datos->count(); // Ya filtrado por unidad en la consulta
            $estadisticas['contexto'] = 'Mi Unidad: ' . ($usuarioActual->unidadOrganizacional->siglas ?? 'Sin unidad');
            $estadisticas['alcance'] = 'Ve reportes de todos los usuarios aprobados de tu unidad organizacional';
            
            // Estadísticas específicas para Admin
            $estadisticas['total_aprobados_unidad'] = $datos->where('estado', 'aprobado')->count();
            $estadisticas['total_rechazados_unidad'] = $datos->where('estado', 'rechazado')->count();
            $estadisticas['pendientes_unidad'] = $datos->where('estado', 'pendiente')->count();
        }
        
        return $estadisticas;
    }

    /**
     * Obtiene las unidades para el filtro según el rol del usuario
     */
    private function obtenerUnidadesParaFiltro()
    {
        $usuarioActual = Auth::user();
        
        if ($usuarioActual->hasRole('Admin General')) {
            // Admin General puede filtrar por cualquier unidad
            return UnidadOrganizacional::where('activa', true)->get();
        } elseif ($usuarioActual->hasRole('Admin')) {
            // Admin solo puede ver datos de su propia unidad
            return UnidadOrganizacional::where('activa', true)
                ->where('id_unidad_organizacional', $usuarioActual->unidad_organizacional_id)
                ->get();
        }
        
        return collect();
    }

    public function render()
    {
        return view('livewire.reports.approval-reports', [
            'datosReporte' => $this->obtenerDatosReporte(),
            'estadisticas' => $this->obtenerEstadisticas(),
            'unidades' => $this->obtenerUnidadesParaFiltro()
        ]);
    }
}
