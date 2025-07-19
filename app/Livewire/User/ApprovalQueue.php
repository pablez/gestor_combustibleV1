<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApprovalQueue extends Component
{
    use AuthorizesRequests;

    public $selectedUser = null;
    public $showUserDetails = false;
    
    // Para aprobación múltiple
    public $selectedUsers = [];
    public $showBulkApproval = false;
    public $bulkEmail = '';
    public $bulkPassword = '';
    
    // Para aprobación individual
    public $showIndividualApproval = false;
    public $individualEmail = '';
    public $individualPassword = '';
    public $userToApprove = null;

    public function mount()
    {
        $this->authorize('aprobar usuarios');
    }

    /**
     * Obtiene la consulta de usuarios pendientes según el rol del usuario autenticado
     */
    private function getPendingUsersQuery()
    {
        $currentUser = Auth::user();
        
        // Admin General puede ver todos los usuarios pendientes (Supervisores y Conductores)
        if ($currentUser->hasRole('Admin General')) {
            return User::where('estado', 'Pendiente')
                ->whereHas('roles', function($query) {
                    $query->whereIn('name', ['Supervisor', 'Conductor/Operador']);
                })
                ->with(['roles', 'supervisor', 'unidadOrganizacional']);
        }
        
        // Admin solo puede ver usuarios pendientes de rol Conductor de su propia unidad organizacional
        // PERO SOLO los que fueron creados por Supervisores de su unidad (NO los que él creó)
        if ($currentUser->hasRole('Admin')) {
            return User::where('estado', 'Pendiente')
                ->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'Conductor/Operador');
                })
                ->whereHas('supervisor', function($query) use ($currentUser) {
                    // Solo usuarios creados por Supervisores de la misma unidad
                    $query->whereHas('roles', function($subQuery) {
                        $subQuery->where('name', 'Supervisor');
                    })->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id);
                })
                ->with(['roles', 'supervisor', 'unidadOrganizacional']);
        }
        
        // Otros roles no tienen acceso
        return User::where('id', -1);
    }

    /**
     * Verifica si el usuario actual puede aprobar/rechazar al usuario específico
     */
    private function canManageUser(User $user)
    {
        $currentUser = Auth::user();
        
        // Admin General puede aprobar usuarios Supervisor y Conductor de todas las unidades
        if ($currentUser->hasRole('Admin General')) {
            return $user->hasRole(['Supervisor', 'Conductor/Operador']);
        }
        
        // Admin solo puede aprobar usuarios Conductor de su propia unidad organizacional
        // Y SOLO si fueron creados por Supervisores de su unidad
        if ($currentUser->hasRole('Admin')) {
            return $user->unidad_organizacional_id === $currentUser->unidad_organizacional_id &&
                   $user->hasRole('Conductor/Operador') &&
                   $user->supervisor && 
                   $user->supervisor->hasRole('Supervisor') &&
                   $user->supervisor->unidad_organizacional_id === $currentUser->unidad_organizacional_id;
        }
        
        return false;
    }

    /**
     * Selecciona o deselecciona un usuario para aprobación múltiple
     */
    public function toggleUserSelection($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        } else {
            $this->selectedUsers[] = $userId;
        }
        
        // Resetear arrays para mantener índices consecutivos
        $this->selectedUsers = array_values($this->selectedUsers);
    }

    /**
     * Selecciona todos los usuarios visibles
     */
    public function selectAllUsers()
    {
        $users = $this->getPendingUsersQuery()->get();
        $this->selectedUsers = $users->pluck('id')->toArray();
    }

    /**
     * Deselecciona todos los usuarios
     */
    public function deselectAllUsers()
    {
        $this->selectedUsers = [];
    }

    /**
     * Abre el modal de aprobación múltiple
     */
    public function openBulkApproval()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'Debes seleccionar al menos un usuario para aprobar.');
            return;
        }
        
        $this->showBulkApproval = true;
        $this->bulkEmail = '';
        $this->bulkPassword = '';
    }

    /**
     * Cierra el modal de aprobación múltiple
     */
    public function closeBulkApproval()
    {
        $this->showBulkApproval = false;
        $this->bulkEmail = '';
        $this->bulkPassword = '';
    }

    /**
     * Procesa la aprobación múltiple
     */
    public function processBulkApproval()
    {
        $this->validate([
            'bulkEmail' => 'required|email',
            'bulkPassword' => 'required|string|min:6',
        ], [
            'bulkEmail.required' => 'El email es obligatorio.',
            'bulkEmail.email' => 'El email debe ser válido.',
            'bulkPassword.required' => 'La contraseña es obligatoria.',
            'bulkPassword.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $currentUser = Auth::user();
        
        // Verificar que el email coincida con el usuario autenticado
        if ($this->bulkEmail !== $currentUser->email) {
            throw ValidationException::withMessages([
                'bulkEmail' => 'El email no coincide con tu cuenta.',
            ]);
        }
        
        // Verificar la contraseña
        if (!Hash::check($this->bulkPassword, $currentUser->password)) {
            throw ValidationException::withMessages([
                'bulkPassword' => 'La contraseña es incorrecta.',
            ]);
        }

        // Procesar aprobación de usuarios seleccionados
        $approvedUsers = [];
        $errors = [];
        
        foreach ($this->selectedUsers as $userId) {
            try {
                $user = User::findOrFail($userId);
                
                // Verificar permisos
                if (!$this->canManageUser($user)) {
                    $errors[] = "No tienes permisos para aprobar a {$user->nombre} {$user->apellido}.";
                    continue;
                }
                
                // Verificar estado
                if ($user->estado !== 'Pendiente') {
                    $errors[] = "{$user->nombre} {$user->apellido} no está en estado pendiente.";
                    continue;
                }
                
                // Aprobar usuario
                $user->update(['estado' => 'Activo']);
                $approvedUsers[] = $user->nombre . ' ' . $user->apellido;
                
            } catch (\Exception $e) {
                $errors[] = "Error al aprobar usuario ID {$userId}: " . $e->getMessage();
            }
        }

        // Preparar mensaje de resultado
        $message = '';
        if (!empty($approvedUsers)) {
            $count = count($approvedUsers);
            $message = "Se aprobaron exitosamente {$count} usuario(s): " . implode(', ', $approvedUsers) . '.';
        }
        
        if (!empty($errors)) {
            $message .= (!empty($message) ? ' ' : '') . 'Errores: ' . implode(' ', $errors);
        }

        // Limpiar selección y cerrar modal
        $this->selectedUsers = [];
        $this->closeBulkApproval();
        
        if (!empty($approvedUsers)) {
            session()->flash('success', $message);
        } else {
            session()->flash('error', $message);
        }
    }

    /**
     * Abre el modal de aprobación individual
     */
    public function openIndividualApproval($userId)
    {
        $user = User::findOrFail($userId);
        
        if (!$this->canManageUser($user)) {
            session()->flash('error', 'No tienes permisos para aprobar a este usuario.');
            return;
        }
        
        $this->userToApprove = $user;
        $this->showIndividualApproval = true;
        $this->individualEmail = '';
        $this->individualPassword = '';
    }

    /**
     * Cierra el modal de aprobación individual
     */
    public function closeIndividualApproval()
    {
        $this->showIndividualApproval = false;
        $this->individualEmail = '';
        $this->individualPassword = '';
        $this->userToApprove = null;
    }

    /**
     * Procesa la aprobación individual
     */
    public function processIndividualApproval()
    {
        $this->validate([
            'individualEmail' => 'required|email',
            'individualPassword' => 'required|string|min:6',
        ], [
            'individualEmail.required' => 'El email es obligatorio.',
            'individualEmail.email' => 'El email debe ser válido.',
            'individualPassword.required' => 'La contraseña es obligatoria.',
            'individualPassword.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $currentUser = Auth::user();
        
        // Verificar que el email coincida con el usuario autenticado
        if ($this->individualEmail !== $currentUser->email) {
            throw ValidationException::withMessages([
                'individualEmail' => 'El email no coincide con tu cuenta.',
            ]);
        }
        
        // Verificar la contraseña
        if (!Hash::check($this->individualPassword, $currentUser->password)) {
            throw ValidationException::withMessages([
                'individualPassword' => 'La contraseña es incorrecta.',
            ]);
        }

        // Verificar permisos y estado
        if (!$this->canManageUser($this->userToApprove)) {
            $this->closeIndividualApproval();
            session()->flash('error', 'No tienes permisos para aprobar a este usuario.');
            return;
        }
        
        if ($this->userToApprove->estado !== 'Pendiente') {
            $this->closeIndividualApproval();
            session()->flash('error', 'Este usuario no está en estado pendiente.');
            return;
        }

        // Aprobar usuario
        $this->userToApprove->update(['estado' => 'Activo']);
        
        $userName = $this->userToApprove->nombre . ' ' . $this->userToApprove->apellido;
        $roleName = $this->userToApprove->getRoleNames()->first();
        $unidadNombre = $this->userToApprove->unidadOrganizacional->siglas ?? 'Sin unidad';
        
        if ($currentUser->hasRole('Admin General')) {
            $contextMessage = "Usuario {$roleName} del sistema (Unidad: {$unidadNombre}) aprobado exitosamente.";
        } else {
            $contextMessage = "Usuario {$roleName} creado por supervisor de tu unidad organizacional ({$unidadNombre}) aprobado exitosamente.";
        }
        
        $this->closeIndividualApproval();
        session()->flash('success', $contextMessage);
        
        // Cerrar modal de detalles si está abierto
        if ($this->selectedUser && $this->selectedUser->id === $this->userToApprove->id) {
            $this->closeUserDetails();
        }
    }

    /**
     * Aprueba un usuario cambiando su estado a 'Activo' (método legacy - mantener para compatibilidad)
     */
    public function approve($userId)
    {
        $this->openIndividualApproval($userId);
    }

    /**
     * Rechaza y elimina permanentemente a un usuario.
     */
    public function reject($userId)
    {
        $user = User::findOrFail($userId);
        
        // Verificar si el usuario actual puede rechazar a este usuario
        if (!$this->canManageUser($user)) {
            session()->flash('error', 'No tienes permisos para rechazar a este usuario.');
            return;
        }
        
        // Verificar que el usuario esté en estado pendiente
        if ($user->estado !== 'Pendiente') {
            session()->flash('error', 'Este usuario no está en estado pendiente.');
            return;
        }
        
        $userName = $user->nombre . ' ' . $user->apellido;
        $roleName = $user->getRoleNames()->first();
        $unidadNombre = $user->unidadOrganizacional->siglas ?? 'Sin unidad';
        
        // Cerrar el modal si está abierto
        if ($this->selectedUser && $this->selectedUser->id === $userId) {
            $this->closeUserDetails();
        }
        
        // Remover de selección múltiple si está seleccionado
        $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        
        $user->delete();
        
        $currentUser = Auth::user();
        
        if ($currentUser->hasRole('Admin General')) {
            $contextMessage = "Usuario {$roleName} del sistema (Unidad: {$unidadNombre}) rechazado y eliminado.";
        } else {
            $contextMessage = "Usuario {$roleName} creado por supervisor de tu unidad organizacional ({$unidadNombre}) rechazado y eliminado.";
        }
        
        session()->flash('success', $contextMessage);
    }

    /**
     * Muestra los detalles de un usuario específico
     */
    public function viewDetails($userId)
    {
        $user = User::with(['roles', 'supervisor', 'unidadOrganizacional'])->findOrFail($userId);
        
        // Verificar si el usuario actual puede ver los detalles de este usuario
        if (!$this->canManageUser($user)) {
            session()->flash('error', 'No tienes permisos para ver los detalles de este usuario.');
            return;
        }
        
        $this->selectedUser = $user;
        $this->showUserDetails = true;
    }

    /**
     * Cierra el modal de detalles del usuario
     */
    public function closeUserDetails()
    {
        $this->selectedUser = null;
        $this->showUserDetails = false;
    }

    /**
     * Obtiene el contexto del usuario actual para mostrar en la interfaz
     */
    public function getCurrentUserContext()
    {
        $currentUser = Auth::user();
        
        if ($currentUser->hasRole('Admin General')) {
            return [
                'tipo' => 'Admin General',
                'descripcion' => 'Puedes ver y aprobar usuarios Supervisor y Conductor de todas las unidades organizacionales del sistema.',
                'color' => 'purple'
            ];
        }
        
        if ($currentUser->hasRole('Admin')) {
            $unidadNombre = $currentUser->unidadOrganizacional->siglas ?? 'Sin unidad';
            return [
                'tipo' => 'Admin',
                'descripcion' => "Puedes ver y aprobar usuarios Conductor creados por Supervisores de tu unidad organizacional ({$unidadNombre}).",
                'color' => 'blue'
            ];
        }
        
        return [
            'tipo' => 'Usuario',
            'descripcion' => 'No tienes permisos para aprobar usuarios.',
            'color' => 'gray'
        ];
    }

    /**
     * Obtiene estadísticas específicas según el rol del usuario
     */
    public function getStatistics()
    {
        $currentUser = Auth::user();
        $visibleUsers = $this->getPendingUsersQuery()->get();
        
        $stats = [
            'total_visible' => $visibleUsers->count(),
            'conductores' => $visibleUsers->filter(function($user) {
                return $user->hasRole('Conductor/Operador');
            })->count(),
            'supervisores' => $visibleUsers->filter(function($user) {
                return $user->hasRole('Supervisor');
            })->count(),
            'admins' => $visibleUsers->filter(function($user) {
                return $user->hasRole('Admin');
            })->count(),
            'selected' => count($this->selectedUsers),
        ];
        
        $stats['can_approve'] = $stats['total_visible'];
        $stats['cannot_approve'] = 0;
        
        return $stats;
    }

    /**
     * Renderiza la vista con la lista de usuarios pendientes filtrados por permisos
     */
    public function render()
    {
        $users = $this->getPendingUsersQuery()
            ->orderBy('created_at', 'desc')
            ->get();

        $userContext = $this->getCurrentUserContext();
        $statistics = $this->getStatistics();

        return view('livewire.user.approval-queue', compact('users', 'userContext', 'statistics'));
    }
}
