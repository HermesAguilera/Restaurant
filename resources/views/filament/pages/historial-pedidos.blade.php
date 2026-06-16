<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm md:col-span-3">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Mostrando pedidos del día actual.
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
            <div class="text-sm text-gray-500 dark:text-gray-400">Total pedidos</div>
            <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $this->resumen['total_ordenes'] ?? 0 }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
            <div class="text-sm text-gray-500 dark:text-gray-400">Monto total</div>
            <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">L {{ number_format($this->resumen['total_monto'] ?? 0, 2) }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
            <div class="text-sm text-gray-500 dark:text-gray-400">Entregados</div>
            <div class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $this->resumen['entregadas'] ?? 0 }}</div>
        </div>
    </div>
    {{ $this->table }}
</x-filament-panels::page>
