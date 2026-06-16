<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 max-w-4xl mx-auto w-full">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">Reporte de Arqueo de Caja</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="space-y-3">
            <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Usuario:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $apertura->usuario->name ?? '' }}</span>
            </div>
            <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Monto Inicial:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">L. {{ number_format($apertura->monto_inicial, 2) }}</span>
            </div>
            <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Estado:</span>
                <span class="text-sm font-bold {{ $apertura->estado === 'cerrada' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">{{ ucfirst($apertura->estado) }}</span>
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Fecha Apertura:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $apertura->fecha_apertura ? \Carbon\Carbon::parse($apertura->fecha_apertura)->format('d/m/Y h:i A') : 'N/A' }}</span>
            </div>
            <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Fecha Cierre:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $apertura->fecha_cierre ? \Carbon\Carbon::parse($apertura->fecha_cierre)->format('d/m/Y h:i A') : 'Pendiente' }}</span>
            </div>
        </div>
    </div>

    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Totales por Método de Pago</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="p-4 bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-800/50 rounded-xl text-center">
            <div class="text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wide mb-1">Efectivo</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">L. {{ number_format($totales['efectivo'], 2) }}</div>
        </div>
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl text-center">
            <div class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-1">Tarjeta</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">L. {{ number_format($totales['tarjeta'], 2) }}</div>
        </div>
        <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800/50 rounded-xl text-center">
            <div class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-1">Transferencia</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">L. {{ number_format($totales['transferencia'], 2) }}</div>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-center">
            <div class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Otros</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">L. {{ number_format($totales['otros'], 2) }}</div>
        </div>
    </div>

    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Facturas Registradas</h3>
    @if(count($facturas) > 0)
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full min-w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-left">
                        <th class="p-3 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">Número</th>
                        <th class="p-3 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">Cliente</th>
                        <th class="p-3 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">Método Pago</th>
                        <th class="p-3 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">Total</th>
                        <th class="p-3 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($facturas as $factura)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="p-3 text-sm font-medium text-gray-900 dark:text-white">{{ $factura->numero_factura }}</td>
                            <td class="p-3 text-sm text-gray-600 dark:text-gray-300">{{ $factura->cliente->nombre ?? 'Consumidor Final' }}</td>
                            <td class="p-3 text-sm text-gray-600 dark:text-gray-300">{{ ucfirst($factura->metodo_pago) }}</td>
                            <td class="p-3 text-sm font-bold text-gray-900 dark:text-white">L. {{ number_format($factura->total, 2) }}</td>
                            <td class="p-3 text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($factura->fecha_factura)->format('d/m/Y h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
            <p class="text-gray-500 dark:text-gray-400">No hay facturas registradas en este turno.</p>
        </div>
    @endif
</div>
