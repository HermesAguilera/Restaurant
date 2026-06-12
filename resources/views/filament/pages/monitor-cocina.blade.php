<x-filament-panels::page>
    <div class="flex gap-4 mb-6">
        @foreach(['general' => 'Comida General', 'china' => 'Comida China', 'pizza' => 'Pizza'] as $key => $label)
            <button
                wire:click="$set('seccion', '{{ $key }}')"
                class="px-6 py-2 rounded-xl font-bold transition {{ $seccion === $key ? 'bg-primary-600 text-white shadow' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div wire:poll.5s>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->ordenes as $orden)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border dark:border-gray-700 flex flex-col">
                    <div class="p-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-t-xl">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold">Orden #{{ $orden->id }}</h3>
                                <div class="text-sm text-gray-500">{{ $orden->created_at->format('h:i A') }} ({{ $orden->created_at->diffForHumans() }})</div>

                                @if($detalleInicial = $orden->detalles->first())
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-primary-100 text-primary-800">
                                            {{ $detalleInicial->tipo_orden === 'restaurante' ? '🍽 Comer Aquí' : '🛍 Para Llevar' }}
                                        </span>
                                        @if($detalleInicial->tipo_orden === 'restaurante')
                                            <span class="ml-1 text-xs text-gray-600 font-medium">({{ $detalleInicial->numero_personas }} personas)</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="font-bold">{{ $orden->nombre_cliente }}</div>
                                <div class="text-sm text-gray-400">#{{ $orden->numero_dia }}</div>
                                @if($orden->mesa)
                                    <div class="mt-1 text-xs font-semibold text-primary-600 dark:text-primary-400">
                                        Mesa: {{ $orden->mesa }}
                                    </div>
                                @endif
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
                            @foreach($orden->detalles->where('platillo.seccion', $seccion)->where('platillo.tipo', 'comida') as $detalle)
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
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-500 bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700">
                    <x-heroicon-o-face-smile class="w-16 h-16 mx-auto mb-4 text-gray-400"/>
                    <h3 class="text-xl font-bold">¡No hay órdenes activas!</h3>
                    <p>La cocina está al día.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
