<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CodigoRegistro;
use Illuminate\Support\Str;


class CodigoRegistroPanel extends Component
{
    public $codigo;
    public $vigente_hasta;
    public $rol_solicitado = '';
    public $unidad_organizacional_id = '';
    public $supervisor_id = '';
    public $formVisible = false;
    public $rolesPermitidos = [];
    public $supervisoresUnidad = [];
    
    // Nuevas propiedades para el historial de códigos
    public $ultimosCodigos = [];
    public $filtroRol = '';
    public $filtroUnidad = '';
    public $filtroSupervisor = '';
    public $historialVisible = false;

    public function mount()
    {
        $user = auth()->user();
        
        // Establecer roles permitidos y unidad organizacional según el rol del usuario
        if ($user->hasRole('Admin General')) {
            $this->rolesPermitidos = ['Admin', 'Supervisor', 'Conductor/Operador'];
            // Admin General NO puede crear códigos para su propia unidad
        } elseif ($user->hasRole('Admin')) {
            $this->rolesPermitidos = ['Supervisor', 'Conductor/Operador'];
            $this->unidad_organizacional_id = $user->unidad_organizacional_id;
        } elseif ($user->hasRole('Supervisor')) {
            $this->rolesPermitidos = ['Conductor/Operador'];
            // Asegurarse de que estos valores siempre están establecidos para Supervisor
            $this->unidad_organizacional_id = $user->unidad_organizacional_id ?? '';
            $this->rol_solicitado = 'Conductor/Operador';
            $this->supervisor_id = $user->id;
            
            // Registrar para depuración
            \Illuminate\Support\Facades\Log::info('Supervisor montó componente:', [
                'supervisor_id' => $this->supervisor_id,
                'unidad_id' => $this->unidad_organizacional_id,
                'rol_solicitado' => $this->rol_solicitado
            ]);
        } else {
            $this->rolesPermitidos = [];
        }
        
        $this->actualizarCodigo();
        $this->cargarUltimosCodigos();
    }

    public function actualizarCodigo()
    {
        $user = auth()->user();
        
        // Buscar el código más reciente que esté vigente (no usado y no expirado)
        $registro = CodigoRegistro::where('vigente_hasta', '>', now())
            ->where('usado', false)
            ->where('creado_por', $user->id)
            ->latest('created_at')
            ->first();

        if ($registro) {
            $this->codigo = $registro->codigo;
            $this->vigente_hasta = $registro->vigente_hasta;
            $this->rol_solicitado = $registro->rol_solicitado;
            $this->unidad_organizacional_id = $registro->unidad_organizacional_id;
            $this->supervisor_id = $registro->supervisor_id;
        } else {
            $this->codigo = null;
            $this->vigente_hasta = null;
            
            // Para supervisores, mantener los valores predeterminados
            if ($user->hasRole('Supervisor')) {
                // No resetear los valores, mantener los establecidos en mount()
                // $this->rol_solicitado ya está establecido como 'Conductor/Operador'
                // $this->unidad_organizacional_id ya está establecido
                // $this->supervisor_id ya está establecido
            } else {
                // Para otros roles, sí resetear
                $this->rol_solicitado = '';
                $this->unidad_organizacional_id = '';
                $this->supervisor_id = '';
            }
        }
    }

    public function generarCodigo()
    {
        $user = request()->user();

        // Validación de rol
        if (!$user->hasAnyRole(['Admin General', 'Admin', 'Supervisor'])) {
            abort(403, 'No autorizado');
        }

        // Para Admin y Supervisor, asegurar que siempre tengan su unidad organizacional
        if ($user->hasRole('Admin') || $user->hasRole('Supervisor')) {
            $this->unidad_organizacional_id = $user->unidad_organizacional_id;
        }

        // Validación de seguridad: Admin General NO puede crear códigos para su propia unidad
        if ($user->hasRole('Admin General') && $this->unidad_organizacional_id == $user->unidad_organizacional_id) {
            session()->flash('error', 'No puedes crear códigos de registro para tu propia unidad organizacional.');
            return;
        }

        // Verificar si la unidad seleccionada es Despacho de Gobernacion
        $unidadSeleccionada = \App\Models\UnidadOrganizacional::find($this->unidad_organizacional_id);
        $esDespachoGobernacion = $unidadSeleccionada && $unidadSeleccionada->nombre_unidad === 'Despacho de Gobernacion';

        // Actualiza rolesPermitidos por si el usuario cambia
        if ($user->hasRole('Admin General')) {
            if ($esDespachoGobernacion) {
                $this->rolesPermitidos = ['Admin General', 'Admin', 'Supervisor', 'Conductor/Operador'];
            } else {
                $this->rolesPermitidos = ['Admin', 'Supervisor', 'Conductor/Operador'];
            }
        } elseif ($user->hasRole('Admin')) {
            if ($esDespachoGobernacion) {
                $this->rolesPermitidos = ['Admin General', 'Admin', 'Supervisor', 'Conductor/Operador'];
            } else {
                $this->rolesPermitidos = ['Supervisor', 'Conductor/Operador'];
            }
        } elseif ($user->hasRole('Supervisor')) {
            $this->rolesPermitidos = ['Conductor/Operador'];
        } else {
            $this->rolesPermitidos = [];
        }

        // Validar rol solicitado
        \Illuminate\Support\Facades\Log::info('Validando rol solicitado:', [
            'usuario' => $user->getRoleNames()->first(),
            'rol_solicitado' => $this->rol_solicitado,
            'roles_permitidos' => $this->rolesPermitidos,
            'esta_en_array' => in_array($this->rol_solicitado, $this->rolesPermitidos)
        ]);
        
        if (!in_array($this->rol_solicitado, $this->rolesPermitidos)) {
            session()->flash('error', 'No tienes permiso para crear este tipo de usuario: ' . $this->rol_solicitado);
            return;
        }

        // Verificar valores específicos para supervisores
        if ($user->hasRole('Supervisor')) {
            // Forzar valores para supervisores y registrar para depuración
            $this->unidad_organizacional_id = $user->unidad_organizacional_id;
            $this->rol_solicitado = 'Conductor/Operador';
            $this->supervisor_id = $user->id;
            
            \Illuminate\Support\Facades\Log::info('Supervisor generando código:', [
                'supervisor_id' => $this->supervisor_id,
                'unidad_id' => $this->unidad_organizacional_id,
                'rol_solicitado' => $this->rol_solicitado
            ]);
        }

        // Validar unidad organizacional y supervisor según el rol del usuario
        if ($user->hasRole('Admin General')) {
            // El Admin General puede crear usuarios para cualquier unidad
            if ($this->rol_solicitado === 'Admin') {
                $this->supervisor_id = $user->id; // El Admin General es el supervisor de los Admin General y Admin
            } elseif ($this->rol_solicitado === 'Supervisor') {
                // Validar que el supervisor seleccionado sea Admin de la unidad
                $supervisor = \App\Models\User::where('id', $this->supervisor_id)
                    ->role('Admin')
                    ->where('unidad_organizacional_id', $this->unidad_organizacional_id)
                    ->first();
                if (!$supervisor) {
                    session()->flash('error', 'El supervisor debe ser un Admin de la unidad organizacional seleccionada.');
                    return;
                }
            } elseif ($this->rol_solicitado === 'Conductor/Operador') {
                // Validar que el supervisor sea de la unidad seleccionada
                $supervisor = \App\Models\User::where('id', $this->supervisor_id)
                    ->role('Supervisor')
                    ->where('unidad_organizacional_id', $this->unidad_organizacional_id)
                    ->first();
                if (!$supervisor) {
                    session()->flash('error', 'El supervisor debe ser un Supervisor de la unidad organizacional seleccionada.');
                    return;
                }
            }
        } elseif ($user->hasRole('Admin')) {
            // El Admin solo puede crear usuarios para su unidad
            // Verificar que la unidad seleccionada sea la del usuario (convertir a entero para comparación)
            if ((int)$this->unidad_organizacional_id !== (int)$user->unidad_organizacional_id) {
                session()->flash('error', 'Solo puedes crear códigos para tu propia unidad organizacional.');
                return;
            }
            
            if ($this->rol_solicitado === 'Admin General' || $this->rol_solicitado === 'Admin') {
                // Solo permitido en Despacho de Gobernacion
                if (!$esDespachoGobernacion) {
                    session()->flash('error', 'Solo puedes crear usuarios Admin General o Admin en la unidad Despacho de Gobernacion.');
                    return;
                }
                $this->supervisor_id = $user->id; // El Admin es el supervisor
            } elseif ($this->rol_solicitado === 'Supervisor') {
                $this->supervisor_id = $user->id; // El Admin es el supervisor de los Supervisores
            } elseif ($this->rol_solicitado === 'Conductor/Operador') {
                // Validar que el supervisor sea de la misma unidad
                $supervisor = \App\Models\User::where('id', $this->supervisor_id)
                    ->role('Supervisor')
                    ->where('unidad_organizacional_id', $user->unidad_organizacional_id)
                    ->first();
                if (!$supervisor) {
                    session()->flash('error', 'El supervisor debe ser un Supervisor de tu unidad organizacional.');
                    return;
                }
            }
        } elseif ($user->hasRole('Supervisor')) {
            // El Supervisor solo puede crear Conductores/Operadores para su unidad
            $this->unidad_organizacional_id = $user->unidad_organizacional_id;
            $this->rol_solicitado = 'Conductor/Operador';
            $this->supervisor_id = $user->id;
        }

        // Validaciones según rol
        if ($user->hasRole('Admin General')) {
            $this->validate([
                'rol_solicitado' => 'required|string',
                'unidad_organizacional_id' => 'required|integer',
            ]);

            // Verificar si es Despacho de Gobernacion para permitir rol Admin General
            $unidadSeleccionada = \App\Models\UnidadOrganizacional::find($this->unidad_organizacional_id);
            if ($this->rol_solicitado === 'Admin General' && 
                (!$unidadSeleccionada || $unidadSeleccionada->nombre_unidad !== 'Despacho de Gobernacion')) {
                session()->flash('error', 'El rol de Admin General solo puede ser asignado en la unidad Despacho de Gobernacion.');
                return;
            }
            // Si el rol solicitado requiere supervisor, validar que exista y pertenezca a la unidad
            if ($this->rol_solicitado === 'Conductor/Operador' || $this->rol_solicitado === 'Supervisor') {
                if ($this->supervisor_id) {
                    if ($this->rol_solicitado === 'Supervisor') {
                        $supervisor = \App\Models\User::where('id', $this->supervisor_id)
                            ->role('Admin')
                            ->where('unidad_organizacional_id', $this->unidad_organizacional_id)
                            ->first();
                        if (!$supervisor) {
                            session()->flash('error', 'El supervisor seleccionado debe ser un Admin de la unidad organizacional.');
                            return;
                        }
                    } else {
                        $supervisor = \App\Models\User::where('id', $this->supervisor_id)
                            ->role('Supervisor')
                            ->where('unidad_organizacional_id', $this->unidad_organizacional_id)
                            ->first();
                        if (!$supervisor) {
                            session()->flash('error', 'El supervisor seleccionado no pertenece a la unidad organizacional.');
                            return;
                        }
                    }
                    $supervisorId = $supervisor->id;
                } else {
                    $supervisorId = null;
                }
            } elseif ($this->rol_solicitado === 'Admin General' || $this->rol_solicitado === 'Admin') {
                $supervisorId = $user->id; // El Admin General es el supervisor
            } else {
                $supervisorId = null;
            }
        } elseif ($user->hasRole('Admin')) {
            $this->validate([
                'rol_solicitado' => 'required|string',
            ]);
            
            if ($this->rol_solicitado === 'Admin General' || $this->rol_solicitado === 'Admin') {
                $supervisorId = $user->id;
            } elseif ($this->rol_solicitado === 'Supervisor') {
                $supervisorId = $user->id;
            } elseif ($this->rol_solicitado === 'Conductor/Operador') {
                $supervisor = \App\Models\User::where('id', $this->supervisor_id)
                    ->role('Supervisor')
                    ->where('unidad_organizacional_id', $user->unidad_organizacional_id)
                    ->first();
                if (!$supervisor) {
                    session()->flash('error', 'El supervisor seleccionado no pertenece a tu unidad.');
                    return;
                }
                $supervisorId = $supervisor->id;
            } else {
                session()->flash('error', 'Rol no válido para tu nivel de permisos.');
                return;
            }
        } elseif ($user->hasRole('Supervisor')) {
            // El Supervisor solo puede crear Conductores/Operadores para su unidad
            // Forzar valores para evitar problemas
            $this->unidad_organizacional_id = $user->unidad_organizacional_id;
            $this->rol_solicitado = 'Conductor/Operador';
            $this->supervisor_id = $user->id;
            $supervisorId = $user->id; // Asignar explícitamente
            
            // Validar que el usuario Supervisor tenga unidad asignada
            if (empty($this->unidad_organizacional_id)) {
                session()->flash('error', 'No tienes una unidad organizacional asignada.');
                return;
            }
            
            // Debug para ver los valores
            \Illuminate\Support\Facades\Log::info('Supervisor creando código', [
                'supervisor_id' => $supervisorId,
                'unidad' => $this->unidad_organizacional_id,
                'rol' => $this->rol_solicitado
            ]);
            
            // No se requieren más validaciones para supervisores
            // Ya que todos los valores están predefinidos
        }

        try {
            // *** CAMBIO IMPORTANTE: NO invalidar códigos anteriores ***
            // Los códigos solo se invalidan cuando son usados o expiran por tiempo
            // Comentamos estas líneas:
            // CodigoRegistro::where('vigente_hasta', '>', now())
            //     ->where('creado_por', $user->id)
            //     ->update(['vigente_hasta' => now()]);
            
            // Asegurarse de que supervisorId esté definido para supervisores
            if ($user->hasRole('Supervisor')) {
                $supervisorId = $user->id;
                // Garantizar que valores críticos estén establecidos
                $this->unidad_organizacional_id = $user->unidad_organizacional_id;
                $this->rol_solicitado = 'Conductor/Operador';
            }
            
            // Log para depuración
            \Illuminate\Support\Facades\Log::info('Antes de crear', [
                'supervisor_id' => $supervisorId ?? null,
                'user_id' => $user->id,
                'unidad' => $this->unidad_organizacional_id,
                'rol' => $this->rol_solicitado
            ]);
                
            CodigoRegistro::create([
                'codigo' => Str::upper(Str::random(8)),
                'vigente_hasta' => now()->addMinutes(30),
                'rol_solicitado' => $this->rol_solicitado,
                'unidad_organizacional_id' => $this->unidad_organizacional_id,
                'supervisor_id' => $supervisorId ?? null,
                'creado_por' => $user->id,
            ]);
            
            // Restablecer los valores pero considerando el rol de usuario
            if ($user->hasRole('Supervisor')) {
                // Para supervisores, mantener valores predefinidos
                $this->formVisible = false;
                $this->rol_solicitado = 'Conductor/Operador';
                $this->supervisor_id = $user->id;
            } else {
                // Para otros roles, reset completo
                $this->reset(['rol_solicitado', 'unidad_organizacional_id', 'supervisor_id', 'formVisible']);
            }
            
            $this->actualizarCodigo();
            $this->cargarUltimosCodigos(); // Actualizar la lista de últimos códigos
            session()->flash('success', '¡Nuevo código generado! Los códigos anteriores seguirán vigentes hasta ser usados o expirar.');
        } catch (\Exception $e) {
            // Log del error para depuración
            \Illuminate\Support\Facades\Log::error('Error al generar código', [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile(),
                'usuario' => $user->id,
                'rol_usuario' => $user->getRoleNames()->first(),
                'rol_solicitado' => $this->rol_solicitado,
                'unidad' => $this->unidad_organizacional_id,
                'supervisor_id' => $this->supervisor_id ?? null
            ]);
            
            session()->flash('error', 'Error al generar el código: ' . $e->getMessage());
        }
    }

    public function updatedUnidadOrganizacionalId()
    {
        $user = auth()->user();
        
        // Para Admin y Supervisor, siempre forzar su unidad organizacional
        if ($user->hasRole('Admin') || $user->hasRole('Supervisor')) {
            $this->unidad_organizacional_id = $user->unidad_organizacional_id;
        }
        
        // Validación de seguridad: Admin General NO puede seleccionar su propia unidad
        if ($user->hasRole('Admin General') && $this->unidad_organizacional_id == $user->unidad_organizacional_id) {
            session()->flash('error', 'No puedes crear códigos de registro para tu propia unidad organizacional.');
            $this->unidad_organizacional_id = '';
            return;
        }
        
        // Verificar si la unidad seleccionada es Despacho de Gobernacion
        $unidadSeleccionada = \App\Models\UnidadOrganizacional::find($this->unidad_organizacional_id);
        $esDespachoGobernacion = $unidadSeleccionada && $unidadSeleccionada->nombre_unidad === 'Despacho de Gobernacion';
        
        if ($user->hasRole('Admin General')) {
            if ($esDespachoGobernacion) {
                $this->rolesPermitidos = ['Admin General', 'Admin', 'Supervisor', 'Conductor/Operador'];
            } else {
                $this->rolesPermitidos = ['Admin', 'Supervisor', 'Conductor/Operador'];
            }
        } elseif ($user->hasRole('Admin')) {
            if ($esDespachoGobernacion) {
                $this->rolesPermitidos = ['Admin General', 'Admin', 'Supervisor', 'Conductor/Operador'];
            } else {
                $this->rolesPermitidos = ['Supervisor', 'Conductor/Operador'];
            }
        } elseif ($user->hasRole('Supervisor')) {
            $this->rolesPermitidos = ['Conductor/Operador'];
        } else {
            $this->rolesPermitidos = [];
        }

        // Limpiar selección de rol y supervisor al cambiar la unidad
        // Pero NO para supervisores, que tienen valores fijos
        if (!$user->hasRole('Supervisor')) {
            $this->rol_solicitado = '';
            $this->supervisor_id = '';
        }

        // Actualiza supervisores de la unidad seleccionada
        if ($this->unidad_organizacional_id) {
            $this->supervisoresUnidad = \App\Models\User::role('Supervisor')
                ->where('unidad_organizacional_id', $this->unidad_organizacional_id)
                ->get();
        } else {
            $this->supervisoresUnidad = [];
        }
    }

    public function updatedRolSolicitado()
    {
        $user = auth()->user();
        
        if ($user->hasRole('Admin General')) {
            // Lógica existente para Admin General
        } elseif ($user->hasRole('Admin')) {
            // Lógica existente para Admin
        } elseif ($user->hasRole('Supervisor')) {
            // Forzar el rol para supervisores
            $this->rol_solicitado = 'Conductor/Operador';
            $this->supervisor_id = $user->id;
            // Registrar para depuración
            \Illuminate\Support\Facades\Log::info('Supervisor actualizó rol: Forzando valores', [
                'supervisor_id' => $this->supervisor_id,
                'rol_solicitado' => $this->rol_solicitado
            ]);
        }
    }

    public function cargarUltimosCodigos()
    {
        $user = auth()->user();
        $query = CodigoRegistro::with(['creador', 'unidadOrganizacional', 'supervisor'])
            ->orderBy('created_at', 'desc');
            
        // Filtrar según la jerarquía del usuario
        if ($user->hasRole('Admin General')) {
            // Admin General ve todos los códigos (vigentes, usados y vencidos)
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
        
        // Limitar a los últimos 10 códigos (aumentamos el límite para ver más códigos vigentes)
        $this->ultimosCodigos = $query->limit(10)->get();
    }
    
    public function aplicarFiltros()
    {
        $this->cargarUltimosCodigos();
    }
    
    public function resetFiltros()
    {
        $this->reset(['filtroRol', 'filtroUnidad', 'filtroSupervisor']);
        $this->cargarUltimosCodigos();
    }
    
    public function toggleHistorial()
    {
        $this->historialVisible = !$this->historialVisible;
        if ($this->historialVisible) {
            $this->cargarUltimosCodigos();
        }
    }
    
    public function render()
    {
        return view('livewire.codigo-registro-panel');
    }
}
