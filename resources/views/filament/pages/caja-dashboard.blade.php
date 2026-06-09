<x-filament-panels::page>
    {{-- Wrapper principal con height controlado --}}
    <div class="flex flex-col gap-6">

        {{-- ═══════════════════════════════════════════════════════════════
             SECCIÓN 1: POS (MENÚ + CARRITO)
        ═══════════════════════════════════════════════════════════════ --}}
        <div class="flex gap-4" style="min-height:65vh">

            {{-- ── IZQUIERDA: MENÚ ── --}}
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

                {{-- Filtros de categoría --}}
                @if(count($this->categorias) > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    <button
                        wire:click="$set('filtro_categoria', '')"
                        class="px-4 py-1.5 rounded-full text-sm font-semibold transition
                            {{ $filtro_categoria === '' ? 'bg-primary-600 text-white shadow' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                    >Todos</button>
                    @foreach($this->categorias as $cat)
                    <button
                        wire:click="$set('filtro_categoria', '{{ $cat }}')"
                        class="px-4 py-1.5 rounded-full text-sm font-semibold transition
                            {{ $filtro_categoria === $cat ? 'bg-primary-600 text-white shadow' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                    >{{ $cat }}</button>
                    @endforeach
                </div>
                @endif

                {{-- Grid de platillos --}}
                <div class="flex-1 overflow-y-auto pr-1">
                    @if($this->platillos->isEmpty())
                        <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                            <x-heroicon-o-clipboard-document-list class="w-14 h-14 mb-3"/>
                            <p class="font-semibold">No hay platillos disponibles</p>
                            <p class="text-sm mt-1 text-center">Ve a <strong>Restaurante → Platillos del Menú</strong> para agregarlos.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                            @foreach($this->platillos as $platillo)
                            <button
                                wire:click="agregarItem({{ $platillo->id }})"
                                class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-left hover:border-primary-400 hover:shadow-md active:scale-95 transition focus:outline-none focus:ring-2 focus:ring-primary-500"
                            >
                                @if($platillo->categoria)
                                <span class="text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wide">{{ $platillo->categoria }}</span>
                                @endif
                                <p class="font-bold text-gray-900 dark:text-white mt-1 leading-tight">{{ $platillo->nombre }}</p>
                                @if($platillo->descripcion)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $platillo->descripcion }}</p>
                                @endif
                                <p class="text-lg font-bold text-primary-600 dark:text-primary-400 mt-2">L. {{ number_format($platillo->precio, 2) }}</p>
                            </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── DERECHA: CARRITO / ORDEN ── --}}
            <div class="w-80 flex flex-col bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden flex-shrink-0">

                {{-- Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Orden Actual</h2>
                    <div class="mt-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Cliente</label>
                        <input
                            type="text"
                            wire:model.blur="nombre_cliente"
                            placeholder="Consumidor Final"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500"
                        />
                    </div>
                </div>

                {{-- Items del carrito --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    @forelse($carrito as $key => $item)
                    <div class="flex items-start gap-2">
                        <div class="flex items-center gap-1">
                            <button wire:click="decrementar('{{ $key }}')" class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-red-100 dark:hover:bg-red-900/50 text-gray-800 dark:text-gray-100 flex items-center justify-center font-bold text-base leading-none transition">−</button>
                            <span class="w-6 text-center font-bold text-sm text-gray-900 dark:text-white">{{ $item['cantidad'] }}</span>
                            <button wire:click="incrementar('{{ $key }}')" class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-green-100 dark:hover:bg-green-900/50 text-gray-800 dark:text-gray-100 flex items-center justify-center font-bold text-base leading-none transition">+</button>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 dark:text-white leading-tight truncate">{{ $item['nombre'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">L. {{ number_format($item['precio'], 2) }} c/u</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-sm text-gray-900 dark:text-white">L. {{ number_format($item['precio'] * $item['cantidad'], 2) }}</p>
                            <button wire:click="remover('{{ $key }}')" class="text-red-400 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 transition mt-0.5">
                                <x-heroicon-o-trash class="w-4 h-4"/>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center h-28 text-gray-400 dark:text-gray-500">
                        <x-heroicon-o-shopping-cart class="w-10 h-10 mb-2"/>
                        <p class="text-sm">Selecciona platillos del menú</p>
                    </div>
                    @endforelse
                </div>

                {{-- Footer: notas + total + botones --}}
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Notas</label>
                        <textarea
                            wire:model.blur="notas"
                            rows="2"
                            placeholder="Sin cebolla, extra salsa..."
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm resize-none focus:ring-primary-500 focus:border-primary-500"
                        ></textarea>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-base font-bold text-gray-700 dark:text-gray-200">Total</span>
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">L. {{ number_format($this->total, 2) }}</span>
                    </div>

                    {{-- BOTÓN ENVIAR: siempre visible con color explícito --}}
                    <button
                        wire:click="enviarACocina"
                        @if(empty($carrito)) disabled @endif
                        style="{{ empty($carrito) ? 'background-color:#9ca3af;cursor:not-allowed;' : 'background-color:#d97706;' }}"
                        class="w-full py-3 rounded-xl font-bold text-white text-base transition active:scale-95"
                    >
                        <span wire:loading.remove wire:target="enviarACocina">🍽 Enviar a Cocina</span>
                        <span wire:loading wire:target="enviarACocina" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                            Procesando...
                        </span>
                    </button>

                    @if(!empty($carrito))
                    <button
                        wire:click="limpiarCarrito"
                        wire:confirm="¿Limpiar toda la orden?"
                        class="w-full py-2 rounded-xl font-semibold text-sm text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                    >
                        Cancelar orden
                    </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════
             SECCIÓN 2: ÓRDENES PENDIENTES DEL DÍA
        ═══════════════════════════════════════════════════════════════ --}}
        <div wire:poll.8s class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-fire class="w-5 h-5 text-orange-500"/>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Órdenes en Progreso Hoy</h2>
                </div>
                @if($this->ordenesPendientes->count() > 0)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">
                    {{ $this->ordenesPendientes->count() }} activa(s)
                </span>
                @endif
            </div>

            @if($this->ordenesPendientes->isEmpty())
                <div class="py-8 text-center text-gray-400 dark:text-gray-500">
                    <x-heroicon-o-check-circle class="w-10 h-10 mx-auto mb-2 text-green-400"/>
                    <p class="font-semibold text-green-600 dark:text-green-400">¡Todo en orden! No hay órdenes pendientes.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-100 dark:border-gray-700">
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Cliente</th>
                                <th class="px-4 py-3">Platillos</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Hora</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($this->ordenesPendientes as $orden)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                        {{ $orden->estado === 'en_cocina' ? 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                        {{ $orden->numero_dia }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $orden->nombre_cliente }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    @foreach($orden->detalles as $d)
                                        <span class="inline-block">{{ $d->cantidad }}× {{ $d->platillo?->nombre ?? '?' }}</span>@if(!$loop->last), @endif
                                    @endforeach
                                    @if($orden->notas)
                                    <br><span class="text-xs text-red-500 italic">{{ $orden->notas }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">L. {{ number_format($orden->total, 2) }}</td>
                                <td class="px-4 py-3">
                                    @if($orden->estado === 'pendiente')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">⏳ Pendiente</span>
                                    @elseif($orden->estado === 'en_cocina')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">🔥 En Cocina</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $orden->created_at->format('h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
