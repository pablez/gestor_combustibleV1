<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\UnidadOrganizacional;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserCreate extends Component
{
    use WithFileUploads, AuthorizesRequests;

    // Propiedades del formulario
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $password_confirmation;
    public $estado = 'Activo';
    public $selectedRole;
    public $supervisor_id;
    public $unidad_organizacional_id;
    public $foto_perfil;

    // Propiedades auxiliares
    public $roles;
    public $supervisors;
    public $admins; // Nueva propiedad para admins de la unidad
    public $unidadesOrganizacionales;
    public $conductorRoleId;
    public $adminRoleId;
    public $supervisorRoleId;

    // Propiedades para mostrar selectores condicionalmente
    public $showUnidadSelector = false;
    public $showSupervisorSelector = false;
    public $showAdminSelector = false; // Nuevo selector para Admin General creando Supervisor

    /**
     * Inicializa el componente con los datos necesarios
     */
    public function mount()
    {
        $this->authorize('crear usuarios');
        
        $currentUser = Auth::user();
        
        // Cargar roles disponibles según jerarquía
        $this->roles = $this->getAvailableRoles($currentUser);
        
        // Inicializar colecciones vacías
        $this->supervisors = collect();
        $this->admins = collect();
        
        // Obtener IDs de roles
        $this->conductorRoleId = Role::where('name', 'Conductor/Operador')->first()?->id;
        $this->adminRoleId = Role::where('name', 'Admin')->first()?->id;
        $this->supervisorRoleId = Role::where('name', 'Supervisor')->first()?->id;

        // Cargar unidades organizacionales activas solo para Admin General
        if ($currentUser->hasRole('Admin General')) {
            // Usar la columna que realmente existe en la tabla
            $this->unidadesOrganizacionales = UnidadOrganizacional::where('activa', true)
                ->orderBy('nombre_unidad')
                ->get();
        } else {
            $this->unidadesOrganizacionales = collect();
        }
    }

    /**
     * Obtiene los roles disponibles según la jerarquía del usuario actual
     */
    private function getAvailableRoles($currentUser)
    {
        if ($currentUser->hasRole('Admin General')) {
            return Role::whereIn('name', ['Admin', 'Supervisor', 'Conductor/Operador'])->get();
        } elseif ($currentUser->hasRole('Admin')) {
            return Role::whereIn('name', ['Supervisor', 'Conductor/Operador'])->get();
        } elseif ($currentUser->hasRole('Supervisor')) {
            return Role::where('name', 'Conductor/Operador')->get();
        }
        
        return collect();
    }

    // Reglas de validación para el formulario
    protected function rules()
    {
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'selectedRole' => ['required', 'integer', 'exists:roles,id'],
            'foto_perfil' => ['nullable', 'image', 'max:2048'],
        ];

        $currentUser = Auth::user();

        // Validaciones específicas según el rol del usuario autenticado y el rol seleccionado
        if ($currentUser->hasRole('Admin General')) {
            // Admin General creando cualquier rol
            if ($this->selectedRole == $this->adminRoleId || 
                $this->selectedRole == $this->supervisorRoleId || 
                $this->selectedRole == $this->conductorRoleId) {
                $rules['unidad_organizacional_id'] = ['required', 'integer', 'exists:unidad_organizacionals,id_unidad_organizacional'];
            }
            
            // Admin General creando Supervisor necesita seleccionar Admin de la unidad
            if ($this->selectedRole == $this->supervisorRoleId) {
                $rules['supervisor_id'] = ['required', 'integer', 'exists:users,id'];
            }
            
            // Admin General creando Conductor necesita seleccionar Supervisor de la unidad
            if ($this->selectedRole == $this->conductorRoleId) {
                $rules['supervisor_id'] = ['required', 'integer', 'exists:users,id'];
            }
        } elseif ($currentUser->hasRole('Admin')) {
            // Admin creando Conductor necesita seleccionar Supervisor
            if ($this->selectedRole == $this->conductorRoleId) {
                $rules['supervisor_id'] = ['required', 'integer', 'exists:users,id'];
            }
            // Para Supervisor creado por Admin, la unidad y supervisor se asignan automáticamente
        } elseif ($currentUser->hasRole('Supervisor')) {
            // Supervisor solo puede crear Conductor/Operador
            // La unidad y supervisor se asignan automáticamente
        }

        // Solo validar estado si el usuario es Admin General
        if ($currentUser->hasRole('Admin General')) {
            $rules['estado'] = ['required', Rule::in(['Activo', 'Pendiente', 'Inactivo'])];
        }

        return $rules;
    }

    // Hook para previsualizar la foto
    public function updatedFotoPerfil()
    {
        $this->validate([
            'foto_perfil' => 'image|max:2048',
        ]);
    }

    // Hook que se ejecuta cuando cambia el rol seleccionado
    public function updatedSelectedRole($value)
    {
        // Resetear todo
        $this->resetSelectors();
        
        $currentUser = Auth::user();

        if ($currentUser->hasRole('Admin General')) {
            // Admin General puede crear cualquier rol
            if ($value == $this->adminRoleId || 
                $value == $this->supervisorRoleId || 
                $value == $this->conductorRoleId) {
                $this->showUnidadSelector = true;
            }
        } elseif ($currentUser->hasRole('Admin')) {
            if ($value == $this->supervisorRoleId) {
                // Admin creando Supervisor: asignar unidad automáticamente y el Admin como supervisor/creador
                $this->unidad_organizacional_id = $currentUser->unidad_organizacional_id;
                $this->supervisor_id = $currentUser->id; // CRÍTICO: El Admin es el creador y supervisor
                // No mostrar selectores, todo es automático
                $this->showSupervisorSelector = false;
                $this->showUnidadSelector = false;
            } elseif ($value == $this->conductorRoleId) {
                // Admin creando Conductor: asignar unidad automáticamente y mostrar selector de supervisor
                $this->unidad_organizacional_id = $currentUser->unidad_organizacional_id;
                $this->loadSupervisorsForCurrentUnidad();
                $this->showSupervisorSelector = true;
            }
        } elseif ($currentUser->hasRole('Supervisor')) {
            if ($value == $this->conductorRoleId) {
                // Supervisor creando Conductor: asignar todo automáticamente
                $this->unidad_organizacional_id = $currentUser->unidad_organizacional_id;
                $this->supervisor_id = $currentUser->id;
                // No mostrar selectores, todo es automático
            }
        }
    }

    // Hook que se ejecuta cuando cambia la unidad organizacional (solo para Admin General)
    public function updatedUnidadOrganizacionalId($value)
    {
        if ($value && Auth::user()->hasRole('Admin General')) {
            // Resetear supervisor
            $this->supervisor_id = null;
            $this->supervisors = collect();
            $this->admins = collect();
            $this->showSupervisorSelector = false;
            $this->showAdminSelector = false;
            
            if ($this->selectedRole == $this->supervisorRoleId) {
                // Admin General creando Supervisor: cargar Admins de la unidad
                $this->loadAdminsForUnidad($value);
                $this->showAdminSelector = true;
            } elseif ($this->selectedRole == $this->conductorRoleId) {
                // Admin General creando Conductor: cargar Supervisores de la unidad
                $this->loadSupervisorsForUnidad($value);
                $this->showSupervisorSelector = true;
            }
        }
    }

    /**
     * Resetea todos los selectores y valores relacionados
     */
    private function resetSelectors()
    {
        $this->showUnidadSelector = false;
        $this->showSupervisorSelector = false;
        $this->showAdminSelector = false;
        $this->unidad_organizacional_id = null;
        $this->supervisor_id = null;
        $this->supervisors = collect();
        $this->admins = collect();
    }

    /**
     * Carga supervisores de la unidad organizacional del usuario actual (para Admin)
     */
    private function loadSupervisorsForCurrentUnidad()
    {
        $currentUser = Auth::user();
        
        $this->supervisors = User::whereHas('roles', function ($query) {
            $query->where('name', 'Supervisor');
        })->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id)
          ->where('estado', 'Activo')
          ->orderBy('nombre')
          ->get();
    }

    /**
     * Carga supervisores de una unidad organizacional específica (para Admin General)
     */
    private function loadSupervisorsForUnidad($unidadId)
    {
        $this->supervisors = User::whereHas('roles', function ($query) {
            $query->where('name', 'Supervisor');
        })->where('unidad_organizacional_id', $unidadId)
          ->where('estado', 'Activo')
          ->orderBy('nombre')
          ->get();
    }

    /**
     * Carga admins de una unidad organizacional específica (para Admin General creando Supervisor)
     */
    private function loadAdminsForUnidad($unidadId)
    {
        $this->admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'Admin');
        })->where('unidad_organizacional_id', $unidadId)
          ->where('estado', 'Activo')
          ->orderBy('nombre')
          ->get();
    }

    // Método para guardar el nuevo usuario
    public function saveUser()
    {
        $this->authorize('crear usuarios');
        
        $authenticatedUser = Auth::user();

        // Validación específica para el Supervisor
        if ($authenticatedUser->hasRole('Supervisor')) {
            $conductorRole = Role::where('name', 'Conductor/Operador')->first();
            if (!$conductorRole || $this->selectedRole != $conductorRole->id) {
                throw ValidationException::withMessages([
                    'selectedRole' => 'Como Supervisor, solo puedes crear usuarios con el rol Conductor/Operador.',
                ]);
            }
            $this->supervisor_id = $authenticatedUser->id;
            $this->unidad_organizacional_id = $authenticatedUser->unidad_organizacional_id;
        }

        // Validación específica para el Admin
        if ($authenticatedUser->hasRole('Admin')) {
            // Para ambos roles (Supervisor y Conductor), asignar unidad automáticamente
            $this->unidad_organizacional_id = $authenticatedUser->unidad_organizacional_id;
            
            // CRÍTICO: Si está creando un Supervisor, el Admin DEBE ser el supervisor/creador
            if ($this->selectedRole == $this->supervisorRoleId) {
                $this->supervisor_id = $authenticatedUser->id;
            }
            
            // Para Conductor, el supervisor debe ser seleccionado manualmente desde la interfaz
            // pero el Admin sigue siendo el creador conceptual
        }

        $this->validate();

        // Validaciones adicionales
        $this->validateUserAssignments();

        // Preparar los datos del usuario
        $userData = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'unidad_organizacional_id' => $this->unidad_organizacional_id,
        ];

        // CRÍTICO: Asignar supervisor según el rol - Esta es la clave
        if ($this->supervisor_id) {
            $userData['supervisor_id'] = $this->supervisor_id;
        }

        // Procesar la foto de perfil
        if ($this->foto_perfil) {
            $fotoPath = $this->foto_perfil->store('fotos-perfil', 'public');
            $userData['foto_perfil'] = $fotoPath;
        }

        // **NUEVA LÓGICA DE ESTADO**: Determinar el estado basado en jerarquía y aprobación
        $userData['estado'] = $this->determineUserState($authenticatedUser);

        // Crear el nuevo usuario
        $newUser = User::create($userData);

        // Asignar el rol seleccionado
        $role = Role::find($this->selectedRole);
        if ($role) {
            $newUser->assignRole($role);
        }

        // Mensaje de éxito personalizado según el rol del creador
        $mensaje = $this->getSuccessMessage($authenticatedUser, $newUser);
        
        session()->flash('success', $mensaje);

        return redirect()->route('admin.users.index');
    }

    /**
     * Determina el estado del usuario según la nueva lógica de aprobación
     */
    private function determineUserState($authenticatedUser)
    {
        // Admin General puede crear usuarios directamente activos
        if ($authenticatedUser->hasRole('Admin General')) {
            return $this->estado; // Respeta la selección del Admin General
        }
        
        // Admin creando usuarios - NUEVA LÓGICA: Requiere aprobación del Admin General
        if ($authenticatedUser->hasRole('Admin')) {
            return 'Pendiente'; // Todos los usuarios creados por Admin necesitan aprobación
        }
        
        // Supervisor creando usuarios - Ya requería aprobación
        if ($authenticatedUser->hasRole('Supervisor')) {
            return 'Pendiente'; // Todos los usuarios creados por Supervisor necesitan aprobación
        }
        
        // Fallback por defecto
        return 'Pendiente';
    }

    /**
     * Genera mensaje de éxito personalizado según el contexto
     */
    private function getSuccessMessage($authenticatedUser, $newUser)
    {
        $roleName = $newUser->getRoleNames()->first();
        $unidadName = $newUser->unidadOrganizacional->siglas ?? 'Sin unidad';
        
        if ($authenticatedUser->hasRole('Admin General')) {
            $mensaje = "Usuario {$newUser->nombre} {$newUser->apellido} creado exitosamente";
            $mensaje .= " con rol {$roleName} en la unidad {$unidadName}";
            
            if ($this->selectedRole == $this->supervisorRoleId && $this->supervisor_id) {
                $admin = User::find($this->supervisor_id);
                $adminName = $admin ? $admin->nombre . ' ' . $admin->apellido : 'Admin';
                $mensaje .= " bajo la supervisión del Admin {$adminName}";
            } elseif ($this->selectedRole == $this->conductorRoleId && $this->supervisor_id) {
                $supervisor = User::find($this->supervisor_id);
                $supervisorName = $supervisor ? $supervisor->nombre . ' ' . $supervisor->apellido : 'Supervisor';
                $mensaje .= " bajo la supervisión del Supervisor {$supervisorName}";
            }
            
            if ($newUser->estado === 'Activo') {
                $mensaje .= " y activado directamente.";
            } else {
                $mensaje .= " con estado {$newUser->estado}.";
            }
            
            return $mensaje;
        }
        
        if ($authenticatedUser->hasRole('Admin')) {
            $mensaje = "Usuario {$newUser->nombre} {$newUser->apellido} creado exitosamente";
            $mensaje .= " con rol {$roleName} en tu unidad organizacional ({$unidadName})";
            
            if ($this->selectedRole == $this->supervisorRoleId) {
                $mensaje .= " y asignado bajo tu supervisión directa";
            } elseif ($this->selectedRole == $this->conductorRoleId && $this->supervisor_id) {
                $supervisor = User::find($this->supervisor_id);
                $supervisorName = $supervisor ? $supervisor->nombre . ' ' . $supervisor->apellido : 'Supervisor';
                $mensaje .= " bajo la supervisión de {$supervisorName}";
            }
            
            // **MENSAJE ACTUALIZADO**: Informar sobre necesidad de aprobación
            $mensaje .= ". El usuario está en estado Pendiente y requerirá aprobación del Admin General del sistema.";
            
            return $mensaje;
        }
        
        if ($authenticatedUser->hasRole('Supervisor')) {
            $mensaje = "Usuario {$newUser->nombre} {$newUser->apellido} creado exitosamente";
            $mensaje .= " con rol {$roleName} en tu unidad organizacional ({$unidadName})";
            $mensaje .= " y bajo tu supervisión directa.";
            $mensaje .= " El usuario está en estado Pendiente y requerirá aprobación de un administrador de la unidad.";
            
            return $mensaje;
        }
        
        return "Usuario creado exitosamente.";
    }

    /**
     * Valida las asignaciones de usuarios según las reglas de negocio
     */
    private function validateUserAssignments()
    {
        // Validar supervisor_id
        if ($this->supervisor_id) {
            $supervisor = User::find($this->supervisor_id);
            if (!$supervisor) {
                throw ValidationException::withMessages([
                    'supervisor_id' => 'El supervisor seleccionado no existe.',
                ]);
            }

            // Validar que el supervisor esté activo
            if ($supervisor->estado !== 'Activo') {
                throw ValidationException::withMessages([
                    'supervisor_id' => 'El supervisor seleccionado no está activo.',
                ]);
            }

            // Validar que el supervisor pertenezca a la misma unidad organizacional
            if ($supervisor->unidad_organizacional_id != $this->unidad_organizacional_id) {
                throw ValidationException::withMessages([
                    'supervisor_id' => 'El supervisor debe pertenecer a la misma unidad organizacional.',
                ]);
            }

            // Validar que el supervisor tenga el rol correcto según el contexto
            $currentUser = Auth::user();
            
            if ($currentUser->hasRole('Admin General') && $this->selectedRole == $this->supervisorRoleId) {
                // Admin General creando Supervisor: el supervisor debe ser Admin
                if (!$supervisor->hasRole('Admin')) {
                    throw ValidationException::withMessages([
                        'supervisor_id' => 'Para crear un Supervisor, debe seleccionar un Admin de la unidad.',
                    ]);
                }
            } elseif ($currentUser->hasRole('Admin') && $this->selectedRole == $this->supervisorRoleId) {
                // Admin creando Supervisor: el supervisor debe ser Admin (él mismo)
                if (!$supervisor->hasRole('Admin') || $supervisor->id != $currentUser->id) {
                    throw ValidationException::withMessages([
                        'supervisor_id' => 'Error: Como Admin, debes ser tú mismo el supervisor del Supervisor que creas.',
                    ]);
                }
            } elseif ($this->selectedRole == $this->conductorRoleId) {
                // Para Conductor, el supervisor debe ser Supervisor
                if (!$supervisor->hasRole('Supervisor')) {
                    throw ValidationException::withMessages([
                        'supervisor_id' => 'El usuario seleccionado no tiene el rol de Supervisor.',
                    ]);
                }
            }
        }

        // Validar unidad organizacional
        if ($this->unidad_organizacional_id) {
            $unidad = UnidadOrganizacional::find($this->unidad_organizacional_id);
            if (!$unidad) {
                throw ValidationException::withMessages([
                    'unidad_organizacional_id' => 'La unidad organizacional seleccionada no existe.',
                ]);
            }

            // Usar la columna que realmente existe
            if (!$unidad->activa) {
                throw ValidationException::withMessages([
                    'unidad_organizacional_id' => 'La unidad organizacional seleccionada no está activa.',
                ]);
            }
        }

        // Validar que Admin no pueda crear usuarios para otras unidades
        $currentUser = Auth::user();
        if ($currentUser->hasRole('Admin') && 
            $this->unidad_organizacional_id != $currentUser->unidad_organizacional_id) {
            throw ValidationException::withMessages([
                'unidad_organizacional_id' => 'Como Admin, solo puedes crear usuarios para tu propia unidad organizacional.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.user.user-create');
    }
}