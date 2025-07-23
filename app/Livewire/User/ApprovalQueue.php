<?php
namespace App\Livewire\User;


use App\Models\UserApprovalRequest;
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
    public $approvalRequest = null; // Agregar propiedad para la solicitud de aprobación

    public function mount()
    {
        $this->authorize('aprobar usuarios');
    }

    /**
     * Obtiene la consulta de solicitudes de aprobación según el rol del usuario autenticado
     */
    private function getPendingUsersQuery()
    {
        $currentUser = Auth::user();
        
        try {
            if ($currentUser->hasRole('Admin General')) {
                // Admin General ve TODAS las solicitudes pendientes del sistema
                return UserApprovalRequest::with([
                    'usuario.roles', 
                    'usuario.unidadOrganizacional',
                    'creador',
                    'supervisorAsignado'
                ])->where('estado', 'pendiente')
                  ->orderBy('created_at', 'asc');
                
            } elseif ($currentUser->hasRole('Admin')) {
                // Admin puede ver solo usuarios nuevos creados por supervisores de su misma unidad organizacional
                return UserApprovalRequest::with([
                    'usuario.roles', 
                    'usuario.unidadOrganizacional',
                    'creador',
                    'supervisorAsignado'
                ])->where('estado', 'pendiente')
                  ->where('unidad_organizacional_id', $currentUser->unidad_organizacional_id)
                  ->where('rol_creador', 'Supervisor')
                  ->orderBy('created_at', 'asc');
            }
        } catch (\Exception $e) {
            \Log::error('Error en getPendingUsersQuery: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }
        
        // Retornar consulta vacía para otros roles o errores
        return UserApprovalRequest::whereRaw('1 = 0');
    }

    /**
     * Verifica si el usuario actual puede aprobar/rechazar una solicitud específica
     */
    private function canManageRequest(UserApprovalRequest $request)
    {
        $currentUser = Auth::user();
        
        if ($currentUser->hasRole('Admin General')) {
            // Admin General puede aprobar CUALQUIER solicitud pendiente del sistema
            return $request->estado === 'pendiente';
        }
        
        if ($currentUser->hasRole('Admin')) {
            // Admin puede aprobar solo usuarios creados por Supervisores de su unidad organizacional
            // NO puede aprobar usuarios que él mismo creó (requiere aprobación del Admin General)
            return $request->estado === 'pendiente' &&
                   $request->unidad_organizacional_id === $currentUser->unidad_organizacional_id &&
                   $request->rol_creador === 'Supervisor' &&
                   $request->creado_por !== $currentUser->id;
        }
        
        return false;
    }

    /**
     * Selecciona o deselecciona un usuario para aprobación múltiple
     */
    public function toggleUserSelection($requestId)
    {
        if (in_array($requestId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$requestId]);
        } else {
            $this->selectedUsers[] = $requestId;
        }
        
        // Resetear arrays para mantener índices consecutivos
        $this->selectedUsers = array_values($this->selectedUsers);
    }

    /**
     * Selecciona todos los usuarios visibles
     */
    public function selectAllUsers()
    {
        $approvalRequests = $this->getPendingUsersQuery()->get();
        $this->selectedUsers = $approvalRequests->pluck('id')->toArray(); // ID de la solicitud, no del usuario
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
        
        // Emitir evento para gestión de modales
        $this->dispatch('modal-opened', ['type' => 'bulk-approval']);
    }

    /**
     * Cierra el modal de aprobación múltiple
     */
    public function closeBulkApproval()
    {
        $this->showBulkApproval = false;
        $this->bulkEmail = '';
        $this->bulkPassword = '';
        
        // Emitir evento para gestión de modales
        $this->dispatch('modal-closed', ['type' => 'bulk-approval']);
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

        // Procesar aprobación de solicitudes seleccionadas
        $approvedUsers = [];
        $errors = [];
        
        foreach ($this->selectedUsers as $requestId) {
            try {
                $solicitudAprobacion = UserApprovalRequest::findOrFail($requestId);
                
                // Verificar permisos
                if (!$this->canManageRequest($solicitudAprobacion)) {
                    $errors[] = "No tienes permisos para aprobar la solicitud de {$solicitudAprobacion->usuario->nombre} {$solicitudAprobacion->usuario->apellido}.";
                    continue;
                }
                
                // Verificar estado
                if ($solicitudAprobacion->estado !== 'pendiente') {
                    $errors[] = "La solicitud de {$solicitudAprobacion->usuario->nombre} {$solicitudAprobacion->usuario->apellido} no está pendiente.";
                    continue;
                }
                
                // Aprobar solicitud
                $solicitudAprobacion->aprobar($currentUser, 'Aprobación múltiple desde cola de aprobación');
                $approvedUsers[] = $solicitudAprobacion->usuario->nombre . ' ' . $solicitudAprobacion->usuario->apellido;
                
            } catch (\Exception $e) {
                $errors[] = "Error al aprobar solicitud ID {$requestId}: " . $e->getMessage();
            }
        }

        // Preparar mensaje de resultado
        $message = '';
        if (!empty($approvedUsers)) {
            $count = count($approvedUsers);
            $message = "Se aprobaron exitosamente {$count} solicitud(es): " . implode(', ', $approvedUsers) . '.';
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
    public function openIndividualApproval($requestId)
    {
        $solicitudAprobacion = UserApprovalRequest::findOrFail($requestId);
        
        if (!$this->canManageRequest($solicitudAprobacion)) {
            session()->flash('error', 'No tienes permisos para aprobar esta solicitud.');
            return;
        }
        
        // Cerrar el modal de detalles si está abierto para evitar conflictos de z-index
        $this->closeUserDetails();
        
        $this->userToApprove = $solicitudAprobacion->usuario;
        $this->approvalRequest = $solicitudAprobacion;
        $this->showIndividualApproval = true;
        $this->individualEmail = '';
        $this->individualPassword = '';
        
        // Emitir evento para gestión de modales
        $this->dispatch('modal-opened', ['type' => 'individual-approval']);
    }

    /**
     * Cierra el modal de aprobación individual
     */
    public function closeIndividualApproval()
    {
        $this->showIndividualApproval = false;
        $this->userToApprove = null;
        $this->approvalRequest = null;
        $this->individualEmail = '';
        $this->individualPassword = '';
        
        // Emitir evento para gestión de modales
        $this->dispatch('modal-closed', ['type' => 'individual-approval']);
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

        // Verificar permisos y estado usando la solicitud
        if (!$this->canManageRequest($this->approvalRequest)) {
            $this->closeIndividualApproval();
            session()->flash('error', 'No tienes permisos para aprobar esta solicitud.');
            return;
        }
        
        if ($this->approvalRequest->estado !== 'pendiente') {
            $this->closeIndividualApproval();
            session()->flash('error', 'Esta solicitud no está pendiente.');
            return;
        }

        // Aprobar solicitud
        $this->approvalRequest->aprobar($currentUser, 'Aprobado desde cola de aprobación');
        
        $userName = $this->userToApprove->nombre . ' ' . $this->userToApprove->apellido;
        $roleName = $this->approvalRequest->rol_solicitado;
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
    public function approve($requestId)
    {
        $solicitudAprobacion = UserApprovalRequest::findOrFail($requestId);
        
        if (!$this->canManageRequest($solicitudAprobacion)) {
            session()->flash('error', 'No tienes permisos para aprobar esta solicitud.');
            return;
        }
        
        $solicitudAprobacion->aprobar(Auth::user(), 'Aprobado desde la cola de aprobación');
        
        session()->flash('success', "Usuario {$solicitudAprobacion->usuario->nombre} {$solicitudAprobacion->usuario->apellido} aprobado exitosamente.");
    }

    /**
     * Rechaza y elimina permanentemente a un usuario.
     */
    public function reject($requestId)
    {
        $solicitudAprobacion = UserApprovalRequest::findOrFail($requestId);
        
        if (!$this->canManageRequest($solicitudAprobacion)) {
            session()->flash('error', 'No tienes permisos para rechazar esta solicitud.');
            return;
        }
        
        $razon = 'Rechazado desde la cola de aprobación';
        $solicitudAprobacion->rechazar(Auth::user(), $razon);
        
        session()->flash('success', 'Solicitud rechazada y usuario eliminado exitosamente.');
    }

    /**
     * Muestra los detalles de un usuario específico
     */
    public function viewDetails($requestId)
    {
        $solicitudAprobacion = UserApprovalRequest::with([
            'usuario.roles', 
            'usuario.supervisor', 
            'usuario.unidadOrganizacional',
            'creador',
            'supervisorAsignado'
        ])->findOrFail($requestId);
        
        // Verificar si el usuario actual puede ver los detalles de esta solicitud
        if (!$this->canManageRequest($solicitudAprobacion)) {
            session()->flash('error', 'No tienes permisos para ver los detalles de esta solicitud.');
            return;
        }
        
        $this->selectedUser = $solicitudAprobacion->usuario;
        $this->approvalRequest = $solicitudAprobacion; // Almacenar también la solicitud
        $this->showUserDetails = true;
        
        // Emitir evento para gestión de modales
        $this->dispatch('modal-opened', ['type' => 'user-details']);
    }

    /**
     * Cierra el modal de detalles del usuario
     */
    public function closeUserDetails()
    {
        $this->showUserDetails = false;
        $this->selectedUser = null;
        $this->approvalRequest = null;
        
        // Emitir evento para gestión de modales
        $this->dispatch('modal-closed', ['type' => 'user-details']);
    }

    /**
     * Obtiene el contexto del usuario currente para mostrar en la interfaz
     */
    public function getCurrentUserContext()
    {
        $currentUser = Auth::user();
        
        if ($currentUser->hasRole('Admin General')) {
            return [
                'tipo' => 'Admin General',
                'descripcion' => 'Puedes ver y aprobar CUALQUIER usuario pendiente de TODAS las unidades organizacionales del sistema. Cuando creas usuarios Admin, supervisas directamente a ese Admin.',
                'color' => 'purple'
            ];
        }
        
        if ($currentUser->hasRole('Admin')) {
            $unidadNombre = $currentUser->unidadOrganizacional->siglas ?? 'Sin unidad';
            return [
                'tipo' => 'Admin',
                'descripcion' => "Solo puedes ver y aprobar usuarios creados por SUPERVISORES de tu unidad organizacional ({$unidadNombre}). Los usuarios que tú crees requieren aprobación del Admin General.",
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
        try {
            $currentUser = Auth::user();
            $approvalRequests = $this->getPendingUsersQuery()->get();
            
            $stats = [
                'total_visible' => $approvalRequests->count(),
                'conductores' => $approvalRequests->where('rol_solicitado', 'Conductor/Operador')->count(),
                'supervisores' => $approvalRequests->where('rol_solicitado', 'Supervisor')->count(),
                'admins' => $approvalRequests->where('rol_solicitado', 'Admin')->count(),
                'selected' => count($this->selectedUsers),
            ];
            
            $stats['can_approve'] = $stats['total_visible'];
            $stats['cannot_approve'] = 0;
            
            return $stats;
        } catch (\Exception $e) {
            \Log::error('Error en getStatistics: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Retornar estadísticas vacías en caso de error
            return [
                'total_visible' => 0,
                'conductores' => 0,
                'supervisores' => 0,
                'admins' => 0,
                'selected' => 0,
                'can_approve' => 0,
                'cannot_approve' => 0,
            ];
        }
    }

    /**
     * Renderiza la vista con la lista de usuarios pendientes filtrados por permisos
     */
    public function render()
    {
        try {
            // Obtener las solicitudes de aprobación
            $approvalRequests = $this->getPendingUsersQuery()
                ->orderBy('created_at', 'desc')
                ->get();

            // Asegurar que siempre sea una colección
            if (!$approvalRequests) {
                $approvalRequests = collect();
            }

        } catch (\Exception $e) {
            // En caso de error, log y crear colección vacía
            \Log::error('Error en render ApprovalQueue: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            $approvalRequests = collect();
        }

        $userContext = $this->getCurrentUserContext();
        $statistics = $this->getStatistics();

        return view('livewire.user.approval-queue', [
            'approvalRequests' => $approvalRequests,
            'userContext' => $userContext,
            'statistics' => $statistics
        ]);
    }
}
