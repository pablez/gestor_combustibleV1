<div>
    @if(session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-2">
            {{ session('success') }}
        </div>
    @endif

    @if($codigo)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">🔑 Código de registro vigente</h4>
            <p class="font-mono text-lg mb-1">{{ $codigo }}</p>
            <p class="text-xs text-green-600">Válido hasta: {{ $vigente_hasta->format('d/m/Y H:i') }}</p>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <span class="text-yellow-700">No hay código de registro vigente.</span>
        </div>
    @endif

    @php
        $user = auth()->user();
    @endphp

    @if($user && ($user->hasRole('Admin General') || $user->hasRole('Admin')))
        <button wire:click="generarCodigo" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Generar nuevo código
        </button>
    @endif
</div>
