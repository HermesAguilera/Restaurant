@props(['cliente' => null, 'facturas' => null])

<div class="filament-tables-container rounded-xl border border-gray-300 bg-white shadow-sm overflow-hidden dark:bg-gray-800 dark:border-gray-700">
    <table class="w-full text-start divide-y table-auto dark:divide-gray-700">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
                <th class="p-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <span>Fecha</span>
                </th>
                <th class="p-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <span>No. Factura</span>
                </th>
                <th class="p-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <span>Total</span>
                </th>
                <th class="p-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <span>Estado</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y dark:divide-gray-700">
            @php
                $facturas = $facturas ?? ($cliente?->facturas ?? collect());

                if (is_array($facturas)) {
                    $facturas = collect($facturas);
                }

                $facturas = $facturas->sortByDesc(function ($factura) {
                    return $factura->fecha_factura ?? $factura->fecha ?? null;
                })->values();
            @endphp

            @forelse($facturas as $factura)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/70">
                    <td class="p-4 align-middle text-sm">
                        <div class="flex items-center">
                            <span>{{ \Illuminate\Support\Carbon::parse($factura->fecha_factura ?? $factura->fecha)->format('d/m/Y') }}</span>
                        </div>
                    </td>
                    <td class="p-4 align-middle text-sm">
                        <div class="flex items-center font-medium">
                            {{ $factura->numero_factura ?? $factura->numero ?? ('#' . ($factura->id ?? '')) }}
                        </div>
                    </td>
                    <td class="p-4 align-middle text-sm">
                        <div class="flex items-center">
                            <span class="font-medium">L. {{ number_format($factura->total ?? 0, 2) }}</span>
                        </div>
                    </td>
                    <td class="p-4 align-middle text-sm">
                        @php
                            $estado = ucfirst(strtolower((string) ($factura->estado ?? 'Sin estado')));
                            $colorClase = match($estado) {
                                'Pagada', 'Pagado' => 'text-green-800 bg-green-100 dark:bg-green-800/20 dark:text-green-400',
                                'Pendiente' => 'text-yellow-800 bg-yellow-100 dark:bg-yellow-800/20 dark:text-yellow-400',
                                'Cancelada', 'Anulada', 'Vencida' => 'text-red-800 bg-red-100 dark:bg-red-800/20 dark:text-red-400',
                                default => 'text-gray-800 bg-gray-100 dark:bg-gray-800/20 dark:text-gray-400',
                            };
                        @endphp
                        <div class="px-2 py-1 inline-flex items-center gap-1 justify-center rounded-full text-xs font-medium {{ $colorClase }}">
                            {{ $estado }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-sm text-gray-500 text-center">
                        No hay compras registradas para este cliente.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
