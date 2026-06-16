<x-filament::page>
    <x-filament::card class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
        @if($orden)
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Recibir Orden de Compra Insumos #{{ $orden->id }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold mb-1">Proveedor</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">{{ $orden->proveedor->nombre_proveedor }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold mb-1">Fecha</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($orden->fecha_realizada)->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold mb-1">Estado</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">{{ $orden->estado }}</p>
                    </div>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Detalles de la Orden</h3>
            
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-[800px] text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        <tr>
                            <th class="p-3 font-semibold border-b border-gray-200 dark:border-gray-600">Producto</th>
                            <th class="p-3 font-semibold border-b border-gray-200 dark:border-gray-600">Tipo de Orden</th>
                            <th class="p-3 font-semibold border-b border-gray-200 dark:border-gray-600">Cantidad</th>
                            <th class="p-3 font-semibold border-b border-gray-200 dark:border-gray-600">Precio Unitario</th>
                            <th class="p-3 font-semibold border-b border-gray-200 dark:border-gray-600">Subtotal</th>
                            <th class="p-3 font-semibold border-b border-gray-200 dark:border-gray-600">Grasa (%)</th>
                            <th class="p-3 font-semibold border-b border-gray-200 dark:border-gray-600">Proteína (%)</th>
                            <th class="p-3 font-semibold border-b border-gray-200 dark:border-gray-600">Humedad (%)</th>
                            <th class="p-3 font-semibold border-b border-gray-200 dark:border-gray-600">Anomalías</th>
                            <th class="p-3 font-semibold border-b border-gray-200 dark:border-gray-600">Detalles</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @foreach($orden->detalles as $detalle)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="p-3 text-gray-900 dark:text-white font-medium">{{ $detalle->producto->nombre }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-300">{{ $detalle->tipoOrdenCompra->nombre }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-300">{{ $detalle->cantidad }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-300">{{ number_format($detalle->precio_unitario, 2) }} HNL</td>
                                <td class="p-3 text-gray-900 dark:text-white font-semibold">{{ number_format($detalle->subtotal, 2) }} HNL</td>
                                <td class="p-3 text-gray-600 dark:text-gray-300">{{ $detalle->porcentaje_grasa ? number_format($detalle->porcentaje_grasa, 2) . '%' : 'N/A' }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-300">{{ $detalle->porcentaje_proteina ? number_format($detalle->porcentaje_proteina, 2) . '%' : 'N/A' }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-300">{{ $detalle->porcentaje_humedad ? number_format($detalle->porcentaje_humedad, 2) . '%' : 'N/A' }}</td>
                                <td class="p-3">
                                    @if($detalle->anomalias)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Sí</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">No</span>
                                    @endif
                                </td>
                                <td class="p-3 text-gray-600 dark:text-gray-300 text-xs max-w-[200px] truncate" title="{{ $detalle->detalles_anomalias }}">{{ $detalle->detalles_anomalias ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                @if($orden->estado === 'Pendiente')
                    <x-filament::button wire:click="recibir" color="success" class="w-full sm:w-auto">
                        Recibir en Inventario
                    </x-filament::button>
                @else
                    <x-filament::badge color="success" class="w-full sm:w-auto justify-center text-center">
                        Orden ya recibida en inventario
                    </x-filament::badge>
                @endif
            </div>
        @else
            <div class="text-center py-8">
                <x-heroicon-o-exclamation-circle class="mx-auto h-12 w-12 text-red-500 mb-4"/>
                <p class="text-red-500 dark:text-red-400 font-semibold text-lg">No se encontró la orden.</p>
            </div>
        @endif
    </x-filament::card>
</x-filament::page>