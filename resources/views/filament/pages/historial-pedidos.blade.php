<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:col-span-3">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <div class="text-sm text-gray-500">Periodo</div>
                    <select wire:model.live="tipo_periodo" class="mt-1 w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="diario">Diario</option>
                        <option value="semanal">Semanal</option>
                        <option value="quincenal">Quincenal</option>
                        <option value="mensual">Mensual</option>
                    </select>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Fecha de referencia</div>
                    <input wire:model.live="fecha_referencia" type="date" class="mt-1 w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div class="flex items-end">
                    <div class="text-sm text-gray-500">
                        El filtro afecta el resumen y la tabla de abajo.
                    </div>
                </div>
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
