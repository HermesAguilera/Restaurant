<div>
    <h3 class="text-lg font-semibold dark:text-white">Agregar Detalles</h3>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-2">
        <div>
            <label class="block text-sm font-medium dark:text-gray-300">Producto</label>
            <select wire:model.defer="producto_id" class="w-full border rounded p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">Seleccione un producto</option>
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                @endforeach
            </select>
            @error('producto_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium dark:text-gray-300">Cantidad</label>
            <input type="number" wire:model.defer="cantidad" class="w-full border rounded p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" min="1">
            @error('cantidad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium dark:text-gray-300">Precio Unitario (HNL)</label>
            <input type="number" wire:model.defer="precio_unitario" class="w-full border rounded p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" step="0.01" min="0">
            @error('precio_unitario') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>
    <button wire:click="addDetalle" class="mt-3 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition w-full sm:w-auto">Agregar Detalle</button>

    @if(!empty($detalles))
        <div class="overflow-x-auto mt-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full min-w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="p-2 border border-gray-200 dark:border-gray-600 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Producto</th>
                        <th class="p-2 border border-gray-200 dark:border-gray-600 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Cantidad</th>
                        <th class="p-2 border border-gray-200 dark:border-gray-600 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Precio Unit.</th>
                        <th class="p-2 border border-gray-200 dark:border-gray-600 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Subtotal</th>
                        <th class="p-2 border border-gray-200 dark:border-gray-600 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($detalles as $index => $detalle)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="p-2 border border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100">{{ $productos->find($detalle['producto_id'])->nombre }}</td>
                            <td class="p-2 border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300">{{ $detalle['cantidad'] }}</td>
                            <td class="p-2 border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300">{{ number_format($detalle['precio_unitario'], 2) }} HNL</td>
                            <td class="p-2 border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-900 dark:text-white">{{ number_format($detalle['subtotal'], 2) }} HNL</td>
                            <td class="p-2 border border-gray-200 dark:border-gray-700 text-center">
                                <button wire:click="removeDetalle({{ $index }})" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm font-medium transition">Eliminar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>