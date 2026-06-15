<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:col-span-3">
            <div class="text-sm text-gray-500">
                Mostrando pedidos del día actual.
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500">Total pedidos</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">{{ $this->resumen['total_ordenes'] ?? 0 }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500">Monto total</div>
            <div class="mt-1 text-2xl font-bold text-gray-900">L {{ number_format($this->resumen['total_monto'] ?? 0, 2) }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500">Entregados</div>
            <div class="mt-1 text-2xl font-bold text-emerald-600">{{ $this->resumen['entregadas'] ?? 0 }}</div>
        </div>
    </div>
    {{ $this->table }}
</x-filament-panels::page>
