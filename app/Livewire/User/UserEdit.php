<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\UnidadOrganizacional;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserEdit extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public User $user;
    public $nombre = '';
    public $apellido = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRole = null;
    public $estado = '';
    public $foto_perfil = null;
    public $supervisor_id = null;
    public $admin_id = null;
    public $unidad_organizacional_id = null;

    // Propiedades auxiliares
    public $supervisors;
    public $admins;
    public $adminGenerales;
    public $unidadesOrganizacionales;
    public $conductorRoleId;
    public $adminRoleId;
    public $supervisorRoleId;

    // Propiedades para mostrar selectores condicionalmente
    public $showUnidadSelector = false;
    public $showAdminSelector = false;
    public $showSupervisorSelector = false;
    public $showAdminGeneralSelector = false;

    protected function rules()
    {
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'selectedRole' => ['required', 'integer', 'exists:roles,id'],
            'foto_perfil' => ['nullable', 'image', 'max:2048'],
        ];

        // Validar unidad organizacional según el rol
        if ($this->selectedRole == $this->adminRoleId || 
            $this->selectedRole == $this->supervisorRoleId || 
            $this->selectedRole == $this->conductorRoleId) {
            $rules['unidad_organizacional_id'] = ['required', 'integer', 'exists:unidad_organizacionals,id_unidad_organizacional'];
        }

        // Validaciones condicionales según el rol
        if ($this->selectedRole == $this->adminRoleId && Auth::user()->hasRole('Admin General')) {
            $rules['supervisor_id'] = ['required', 'integer', 'exists:users,id'];
        }

        if ($this->selectedRole == $this->supervisorRoleId && Auth::user()->hasRole(['Admin General', 'Admin'])) {
            $rules['admin_id'] = ['required', 'integer', 'exists:users,id'];
        }

        if ($this->selectedRole == $this->conductorRoleId) {
            $rules['supervisor_id'] = ['required', 'integer', 'exists:users,id'];
            if (Auth::user()->hasRole(['Admin General', 'Admin'])) {
                $rules['admin_id'] = ['required', 'integer', 'exists:users,id'];
            }
        }

        return $rules;
    }

    protected $messages = [
        'foto_perfil.image' => 'El archivo debe ser una imagen válida.',
        'foto_perfil.max' => 'La imagen no puede ser mayor a 2MB.',
    ];

    public function mount(User $user)
    {
        $this->authorize('editar usuarios');

        $this->user = $user;
        $this->nombre = $this->user->nombre;
        $this->apellido = $this->user->apellido;
        $this->email = $this->user->email;
        $this->estado = $this->user->estado;
        $this->selectedRole = $this->user->roles->first()->id ?? null;
        $this->supervisor_id = $this->user->supervisor_id;
        $this->unidad_organizacional_id = $this->user->unidad_organizacional_id;

        // Obtener IDs de roles
        $this->conductorRoleId = Role::where('name', 'Conductor/Operador')->first()?->id;
        $this->adminRoleId = Role::where('name', 'Admin')->first()?->id;
        $this->supervisorRoleId = Role::where('name', 'Supervisor')->first()?->id;

        // Inicializar colecciones
        $this->supervisors = collect();
        $this->admins = collect();
        $this->adminGenerales = collect();

        // Cargar datos iniciales
        $this->loadInitialData();
        
        // Configurar selectores según el rol actual
        $this->configureSelectorsForCurrentRole();
    }

    private function loadInitialData()
    {
        $currentUser = Auth::user();
        
        // Cargar unidades organizacionales
        $this->unidadesOrganizacionales = UnidadOrganizacional::activas()->orderBy('nombre_unidad')->get();
        
        // Cargar Admin Generales
        $this->adminGenerales = User::whereHas('roles', function ($query) {
            $query->where('name', 'Admin General');
        })->get();
        
        // Cargar usuarios según el rol actual del usuario autenticado
        if ($currentUser->hasRole('Admin General')) {
            $this->admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'Admin');
            })->get();
            
            $this->supervisors = User::whereHas('roles', function ($query) {
                $query->where('name', 'Supervisor');
            })->get();
        } elseif ($currentUser->hasRole('Admin')) {
            $this->supervisors = User::whereHas('roles', function ($query) {
                $query->where('name', 'Supervisor');
            })->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id)->get();
        }
    }

    private function configureSelectorsForCurrentRole()
    {
        $currentUser = Auth::user();
        
        // Configurar selectores según el rol del usuario siendo editado
        if ($this->selectedRole == $this->adminRoleId || 
            $this->selectedRole == $this->supervisorRoleId || 
            $this->selectedRole == $this->conductorRoleId) {
            $this->showUnidadSelector = true;
        }
        
        // Cargar usuarios específicos de la unidad si ya tiene unidad asignada
        if ($this->unidad_organizacional_id) {
            $this->loadUsersByUnidad($this->unidad_organizacional_id);
        }
        
        // Configurar selectores específicos
        if ($this->selectedRole == $this->supervisorRoleId) {
            $this->showAdminSelector = true;
            $this->admin_id = $this->user->supervisor_id; // En edición, el supervisor_id puede ser un admin
        } elseif ($this->selectedRole == $this->conductorRoleId) {
            $this->showSupervisorSelector = true;
            if ($currentUser->hasRole(['Admin General', 'Admin'])) {
                $this->showAdminGeneralSelector = true;
            }
        }
    }

    // Hook que se ejecuta cuando cambia el rol seleccionado
    public function updatedSelectedRole($value)
    {
        // Resetear selectores
        $this->resetSelectors();
        
        $currentUser = Auth::user();

        if ($value == $this->adminRoleId && $currentUser->hasRole('Admin General')) {
            $this->showUnidadSelector = true;
            $this->supervisor_id = $currentUser->id;
        } elseif ($value == $this->supervisorRoleId) {
            $this->showUnidadSelector = true;
        } elseif ($value == $this->conductorRoleId) {
            $this->showUnidadSelector = true;
            if ($currentUser->hasRole('Supervisor')) {
                $this->supervisor_id = $currentUser->id;
            }
        }
    }

    // Hook que se ejecuta cuando cambia la unidad organizacional
    public function updatedUnidadOrganizacionalId($value)
    {
        if ($value) {
            $this->loadUsersByUnidad($value);
        }
    }

    /**
     * Resetea todos los selectores y valores relacionados
     */
    private function resetSelectors()
    {
        $this->showUnidadSelector = false;
        $this->showAdminSelector = false;
        $this->showSupervisorSelector = false;
        $this->showAdminGeneralSelector = false;
        $this->supervisor_id = null;
        $this->admin_id = null;
        $this->supervisors = collect();
        $this->admins = collect();
    }

    /**
     * Carga usuarios filtrados por unidad organizacional
     */
    private function loadUsersByUnidad($unidadId)
    {
        $currentUser = Auth::user();

        if ($this->selectedRole == $this->adminRoleId && $currentUser->hasRole('Admin General')) {
            // Para Admin creado por Admin General - no necesita más selectores
            
        } elseif ($this->selectedRole == $this->supervisorRoleId) {
            // Para Supervisor - cargar admins de la unidad seleccionada
            $this->admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'Admin');
            })->where('unidad_organizacional_id', $unidadId)->get();
            
            $this->showAdminSelector = true;
            
        } elseif ($this->selectedRole == $this->conductorRoleId) {
            // Para Conductor - cargar supervisores de la unidad seleccionada
            $this->supervisors = User::whereHas('roles', function ($query) {
                $query->where('name', 'Supervisor');
            })->where('unidad_organizacional_id', $unidadId)->get();
            
            $this->showSupervisorSelector = true;
            
            // Si es Admin General o Admin, mostrar selector de Admin General
            if ($currentUser->hasRole(['Admin General', 'Admin'])) {
                $this->showAdminGeneralSelector = true;
            }
        }
    }

    public function updateUser()
    {
        $this->authorize('editar usuarios');

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = Auth::user();

        // Validaciones de permisos
        if ($authenticatedUser->hasRole('Supervisor') && $this->user->estado === 'Pendiente') {
            throw ValidationException::withMessages([
                'estado' => 'No puede modificar un usuario que está pendiente de aprobación.'
            ]);
        }

        if ($authenticatedUser->hasRole('Supervisor')) {
            $conductorRole = Role::where('name', 'Conductor/Operador')->first();

            if ($this->user->hasRole(['Admin', 'Supervisor'])) {
                throw ValidationException::withMessages([
                    'selectedRole' => 'No tiene permisos para editar a este usuario.'
                ]);
            }

            if (!$conductorRole || $this->selectedRole != $conductorRole->id) {
                throw ValidationException::withMessages([
                    'selectedRole' => 'Como Supervisor, solo puede asignar el rol de Conductor/Operador.'
                ]);
            }
            
            $this->supervisor_id = $authenticatedUser->id;
            $this->unidad_organizacional_id = $authenticatedUser->unidad_organizacional_id;
        }

        $this->validate();

        // Validaciones adicionales
        $this->validateUserAssignments();

        // Procesar la foto de perfil
        if ($this->foto_perfil) {
            // Eliminar la foto anterior si existe
            if ($this->user->foto_perfil && Storage::disk('public')->exists($this->user->foto_perfil)) {
                Storage::disk('public')->delete($this->user->foto_perfil);
            }
            
            // Guardar la nueva foto
            $fotoPath = $this->foto_perfil->store('fotos-perfil', 'public');
            $this->user->foto_perfil = $fotoPath;
        }

        // Actualizar los datos del usuario
        $this->user->nombre = $this->nombre;
        $this->user->apellido = $this->apellido;
        $this->user->email = $this->email;
        $this->user->estado = $this->estado;
        $this->user->supervisor_id = $this->supervisor_id;
        $this->user->unidad_organizacional_id = $this->unidad_organizacional_id;

        // Si se ingresó una nueva contraseña, cifrarla y actualizarla
        if (!empty($this->password)) {
            $this->user->password = bcrypt($this->password);
        }

        $this->user->save();

        // Actualizar el rol
        $role = Role::find($this->selectedRole);
        if ($role) {
            $this->user->syncRoles($role->name);
        }

        // Crear relación adicional admin-supervisor si es necesario
        if ($this->admin_id && $this->selectedRole == $this->supervisorRoleId) {
            $this->user->update(['supervisor_id' => $this->admin_id]);
        }

        session()->flash('message', 'Usuario actualizado correctamente con las relaciones de supervisión.');
        return redirect()->route('admin.users.index');
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
        }

        // Validar admin_id
        if ($this->admin_id) {
            $admin = User::find($this->admin_id);
            if (!$admin) {
                throw ValidationException::withMessages([
                    'admin_id' => 'El admin seleccionado no existe.',
                ]);
            }
        }

        // Validar que el supervisor pertenezca a la misma unidad organizacional
        if ($this->selectedRole == $this->supervisorRoleId && $this->admin_id) {
            $admin = User::find($this->admin_id);
            if ($admin->unidad_organizacional_id != $this->unidad_organizacional_id) {
                throw ValidationException::withMessages([
                    'admin_id' => 'El admin debe pertenecer a la misma unidad organizacional que el supervisor.',
                ]);
            }
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

    public function render()
    {
        $this->authorize('ver roles');

        /** @var \App\Models\User $authenticatedUser */
        $authenticatedUser = Auth::user();
        
        // Obtener roles disponibles según jerarquía
        $roles = $this->getAvailableRoles($authenticatedUser);

        return view('livewire.user.user-edit', [
            'roles' => $roles,
        ]);
    }
}