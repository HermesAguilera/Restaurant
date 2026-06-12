<div class="space-y-5">
    <div class="grid gap-3 md:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800/70">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Cliente</div>
            <div class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $record->nombre_cliente ?? 'Sin cliente' }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800/70">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Mesa</div>
            <div class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $record->mesa ?? 'N/A' }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800/70">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Fecha</div>
            <div class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ optional($record->fecha_orden)->format('d/m/Y') ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/60">
        <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Notas</div>
        <div class="rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-200">
            {{ $record->notas ?: 'Sin notas' }}
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900/60">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Detalle</div>
                <div class="text-sm font-bold text-gray-900 dark:text-white">Productos del pedido</div>
            </div>
            <div class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                {{ $record->detalles->count() }} item(s)
            </div>
        </div>
        <div class="overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr class="text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3 text-left font-semibold">Producto</th>
                        <th class="px-4 py-3 text-left font-semibold">Cantidad</th>
                        <th class="px-4 py-3 text-left font-semibold">Precio</th>
                        <th class="px-4 py-3 text-left font-semibold">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900/40">
                    @forelse ($record->detalles as $detalle)
                        <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-800/60">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $detalle->platillo->nombre ?? 'Sin producto' }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $detalle->cantidad ?? 0 }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">L {{ number_format($detalle->precio_unitario ?? 0, 2) }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">L {{ number_format($detalle->subtotal ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                Este pedido no tiene detalles registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
