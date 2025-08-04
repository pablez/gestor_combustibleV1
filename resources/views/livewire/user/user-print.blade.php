@php
    $logoUrl = asset('images/logo-gobernacion.png');
    $total = count($users);
@endphp
<style>
</style>
<style>
@media print {
    @page {
        size: Letter portrait;
        margin: 1.5cm;
    }
    body {
        background: #fff !important;
        color: #222 !important;
    }
    /* Oculta la barra de navegación y cualquier elemento con la clase no-print */
    .no-print, nav, header, .navbar, .sidebar, .topbar, .app-header, .app-navbar {
        display: none !important;
    }
}
</style>
<script>
    window.onload = function() {
        window.print();
    };
</script>
<div style="font-family: Arial, sans-serif; background: #fff; color: #222;">
    <div style="text-align:center; margin-bottom: 1.5rem;">
        <img src="{{ $logoUrl }}" alt="Logo" style="height: 110px; margin-bottom: 0.7rem;">
        <h1 style="font-size: 2.1rem; margin: 0; font-weight: bold; letter-spacing: 1px; color: #1e293b;">Gobierno Autónomo Departamental de Cochabamba</h1>
        <div style="font-size:1.25rem; color:#2563eb; margin-top:0.2rem; font-weight:600;">Listado de Usuarios</div>
        <div style="font-size:1.05rem; color:#555; margin-top:0.1rem;">Sistema de Gestión de Combustible</div>
    </div>
    <div style="margin-bottom: 1rem; font-size:1rem;">
        <strong>Filtros aplicados:</strong>
        @if($roleFilter) Rol: <span>{{ $roleFilter }}</span> @endif
        @if($unidadFilter)
            | Unidad:
            <span>
                @php
                    $unidad = $unidadesOrganizacionales->where('id_unidad_organizacional', $unidadFilter)->first();
                @endphp
                @if($unidad)
                    {{ $unidad->nombre_unidad }} <span style="color:#2563eb; font-weight:600;">({{ $unidad->siglas }})</span>
                @else
                    {{ $unidadFilter }}
                @endif
            </span>
        @endif
        @if($statusFilter) | Estado: <span>{{ $statusFilter }}</span> @endif
        @if($search) | Búsqueda: <span>{{ $search }}</span> @endif
    </div>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem; font-size: 0.98rem;">
        <thead>
            <tr style="background: #e5e7eb;">
                <th style="border: 1px solid #bbb; padding: 8px 10px;">#</th>
                <th style="border: 1px solid #bbb; padding: 8px 10px;">Nombre</th>
                <th style="border: 1px solid #bbb; padding: 8px 10px;">Apellido</th>
                <th style="border: 1px solid #bbb; padding: 8px 10px;">Email</th>
                <th style="border: 1px solid #bbb; padding: 8px 10px;">Rol</th>
                <th style="border: 1px solid #bbb; padding: 8px 10px;">Unidad</th>
                <th style="border: 1px solid #bbb; padding: 8px 10px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr style="background: {{ $loop->even ? '#f9fafb' : '#fff' }};">
                    <td style="border: 1px solid #ddd; padding: 8px 10px; text-align:center;">{{ $loop->iteration }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px 10px;">{{ $user->nombre }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px 10px;">{{ $user->apellido }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px 10px;">{{ $user->email }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px 10px;">{{ $user->roles->pluck('name')->implode(', ') ?: 'Sin Rol' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px 10px;">{{ $user->unidadOrganizacional->siglas ?? 'Sin Unidad' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px 10px; text-align:center;">{{ $user->estado }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; border: 1px solid #ccc; padding: 12px;">No se encontraron usuarios con los filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div style="font-size: 1rem; color: #222; margin-bottom: 1rem;">
        <strong>Total de usuarios listados: {{ $total }}</strong>
    </div>
    <div style="font-size: 0.95rem; color: #888; text-align: right; margin-bottom: 0.5rem;">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    <div style="font-size:0.95rem; color:#666; text-align:center; margin-bottom:1.5rem;">Gobernación Autónoma Departamental - Todos los derechos reservados</div>
    <div class="no-print" style="margin-top:2rem; text-align:center;">
        <button onclick="window.print()" style="padding: 8px 16px; font-size: 1rem; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Imprimir</button>
        <button onclick="window.close()" style="padding: 8px 16px; font-size: 1rem; background: #e5e7eb; color: #222; border: none; border-radius: 4px; cursor: pointer; margin-left: 1rem;">Cerrar</button>
    </div>
</div>
