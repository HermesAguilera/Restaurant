<x-filament-panels::page>
    <div wire:poll.5s>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->ordenes as $orden)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border dark:border-gray-700 flex flex-col">
                    <div class="p-4 border-b dark:border-gray-700 @if($orden->estado === 'en_cocina') bg-warning-50 dark:bg-warning-900/20 @else bg-gray-50 dark:bg-gray-900/50 @endif rounded-t-xl">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold">Orden #{{ $orden->id }}</h3>
                                <div class="text-sm text-gray-500">{{ $orden->created_at->format('h:i A') }} ({{ $orden->created_at->diffForHumans() }})</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold">{{ $orden->nombre_cliente }}</div>
                                <div class="text-sm text-gray-400">#{{ $orden->id }}</div>
                            </div>
                        </div>
                        @if($orden->notas)
                            <div class="mt-2 p-2 bg-danger-50 dark:bg-danger-900/20 text-danger-600 rounded text-sm font-bold border border-danger-200 dark:border-danger-800">
                                Nota: {{ $orden->notas }}
                            </div>
                        @endif
                    </div>

                    <div class="p-4 flex-1">
                        <ul class="space-y-3">
                            @foreach($orden->detalles as $detalle)
                                <li class="flex items-center space-x-3 text-lg">
                                    <span class="bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-lg font-bold">{{ $detalle->cantidad }}</span>
                                    <span class="flex-1">{{ $detalle->platillo?->nombre ?? 'Platillo Desconocido' }}</span>
                                </li>
                                @if($detalle->notas)
                                    <li class="pl-12 text-sm text-danger-500 italic">
                                        * {{ $detalle->notas }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    <div class="p-4 border-t dark:border-gray-700">
                        @if($orden->estado === 'pendiente')
                            <button wire:click="marcarEnCocina({{ $orden->id }})" class="w-full py-2 bg-warning-500 hover:bg-warning-400 text-gray-400 font-bold rounded-lg transition">
                                Empezar a Preparar
                            </button>
                        @elseif($orden->estado === 'en_cocina')
                            <button wire:click="marcarListo({{ $orden->id }})" class="w-full py-3 bg-success-600 hover:bg-success-500 text-gray-400 font-bold rounded-lg shadow-lg transition transform hover:scale-105">
                                ✔ Marcar como Listo
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-500 bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700">
                    <x-heroicon-o-face-smile class="w-16 h-16 mx-auto mb-4 text-gray-400"/>
                    <h3 class="text-xl font-bold">¡No hay órdenes pendientes!</h3>
                    <p>La cocina está al día.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
