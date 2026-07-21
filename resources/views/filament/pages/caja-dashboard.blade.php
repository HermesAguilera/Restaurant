<x-filament-panels::page>
    {{-- CSS propio del POS: el theme del panel no incluye las variantes responsive de Tailwind,
         por eso el layout se define aquí con clases pos-* --}}
    <style>
        .pos-layout { display: flex; flex-direction: column; gap: 1rem; }
        @media (min-width: 768px) {
            .pos-layout { flex-direction: row; align-items: stretch; min-height: 65vh; }
        }
        .pos-menu-col { display: flex; flex-direction: column; flex: 1 1 auto; min-width: 0; }
        .pos-menu-scroll { flex: 1 1 auto; overflow-y: auto; max-height: 65vh; padding-right: 0.25rem; }
        .pos-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem; }
        @media (min-width: 640px) { .pos-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (min-width: 1280px) { .pos-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        .pos-cart-col { display: flex; flex-direction: column; width: 100%; flex-shrink: 0; }
        @media (min-width: 768px) { .pos-cart-col { width: 20rem; } }
        .pos-two-cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.5rem; }

        /* Botones de acción de las tablas de órdenes: colores centralizados aquí
           en vez de estilos inline con !important, para respetar el tema. */
        .pos-action {
            display: inline-flex; align-items: center; gap: 0.375rem;
            border-radius: 9999px; padding: 0.5rem 0.875rem;
            font-size: 0.75rem; line-height: 1rem; font-weight: 600;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            transition: transform .15s ease, background-color .15s ease, border-color .15s ease;
            cursor: pointer; white-space: nowrap; border: 1px solid transparent;
        }
        .pos-action:hover { transform: translateY(-1px); }
        .pos-action-primary { background: #059669; color: #fff; border-color: #047857; }
        .pos-action-primary:hover { background: #047857; }
        .pos-action-info { background: #2563eb; color: #fff; border-color: #1d4ed8; }
        .pos-action-info:hover { background: #1d4ed8; }
        .pos-action-danger { background: #e11d48; color: #fff; border-color: #be123c; }
        .pos-action-danger:hover { background: #be123c; }

        /* Steppers del carrito con área táctil cómoda para tablet (min ~40px). */
        .pos-step { width: 2.25rem; height: 2.25rem; border-radius: 9999px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.125rem; line-height: 1; transition: background-color .15s ease; }
    </style>

    {{-- Wrapper principal con height controlado --}}
    <div class="flex flex-col gap-6">

        {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
             SECCIÃ“N 1: CAJA/POS (MENÃš + CARRITO)
         â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
        <div class="pos-layout">

            {{-- â”€â”€ IZQUIERDA: MENÃš â”€â”€ --}}
            <div class="pos-menu-col">

                {{-- SelecciÃ³n de SecciÃ³n --}}
                <div class="flex gap-2 mb-4">
                    <button
                        wire:click="$set('filtro_seccion', 'comida'); $set('subfiltro_cocina', 'todos'); $set('busqueda', ''); $set('filtro_categoria', '')"
                        class="px-6 py-2 rounded-xl font-bold transition
                            {{ $filtro_seccion === 'comida' ? 'bg-primary-600 text-white shadow' : 'bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-700 dark:text-gray-300' }}"
                    >Comidas</button>
                    <button
                        wire:click="$set('filtro_seccion', 'bebida'); $set('subfiltro_cocina', 'todos'); $set('busqueda', ''); $set('filtro_categoria', '')"
                        class="px-6 py-2 rounded-xl font-bold transition
                            {{ $filtro_seccion === 'bebida' ? 'bg-primary-600 text-white shadow' : 'bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-700 dark:text-gray-300' }}"
                    >Bebidas</button>
                </div>

                {{-- Subfiltros y BÃºsqueda --}}
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @if($filtro_seccion === 'comida' && count($this->subfiltrosCocina) > 1)
                        <div class="flex flex-wrap gap-2">
                            @foreach($this->subfiltrosCocina as $key => $label)
                                <button
                                    wire:click="$set('subfiltro_cocina', '{{ $key }}')"
                                    class="px-3 py-1.5 rounded-lg text-sm font-semibold transition
                                        {{ $subfiltro_cocina === $key ? 'bg-primary-600 text-white shadow' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }}"
                                >{{ $label }}</button>
                            @endforeach
                        </div>
                    @endif

                    <div class="relative flex-1 min-w-[200px]">
    <div class="absolute inset-y-0 flex items-center pointer-events-none z-10" style="left: 14px;">
        <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 dark:text-gray-500"/>
    </div>
    <input
        type="text"
        wire:model.live.debounce.300ms="busqueda"
        wire:keydown.enter="agregarPrimerResultado"
        autofocus
        placeholder="Buscar y presiona Enter para agregar..."
        style="padding-left: 42px !important;"
        class="w-full pr-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm transition"
    />
</div>
                </div>

                {{-- Grid de platillos --}}
                <div class="pos-menu-scroll">
                    @if($this->platillos->isEmpty())
                        @if(filled($busqueda) || $subfiltro_cocina !== 'todos' || filled($filtro_categoria))
                        {{-- Hay platillos cargados, pero ninguno coincide con la búsqueda/filtro actual --}}
                        <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-14 h-14 mb-3"/>
                            <p class="font-semibold">Sin resultados</p>
                            <p class="text-sm mt-1 text-center">Ningún platillo coincide con tu búsqueda o filtro.</p>
                            <button
                                wire:click="$set('busqueda', ''); $set('subfiltro_cocina', 'todos'); $set('filtro_categoria', '')"
                                class="mt-3 px-4 py-1.5 rounded-lg text-sm font-semibold bg-primary-600 text-white hover:bg-primary-500 transition"
                            >Limpiar filtros</button>
                        </div>
                        @else
                        {{-- Realmente no hay platillos cargados en esta sección --}}
                        <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                            <x-heroicon-o-clipboard-document-list class="w-14 h-14 mb-3"/>
                            <p class="font-semibold">No hay platillos disponibles</p>
                            <p class="text-sm mt-1 text-center">Ve a <strong>Restaurante → Platillos del Menú</strong> para agregarlos.</p>
                        </div>
                        @endif
                    @else
                        <div class="pos-grid">
                            @foreach($this->platillos as $platillo)
                            @php($enCarrito = $carrito[$platillo->id]['cantidad'] ?? 0)
                            <button
                                wire:click="agregarItem({{ $platillo->id }})"
                                class="group relative bg-white dark:bg-white/5 rounded-xl border p-4 text-left hover:border-primary-400 hover:shadow-md active:scale-95 transition focus:outline-none focus:ring-2 focus:ring-primary-500
                                    {{ $enCarrito > 0 ? 'border-primary-500 ring-1 ring-primary-500/40' : 'border-gray-200 dark:border-white/10' }}"
                            >
                                @if($enCarrito > 0)
                                <span class="absolute -top-2 -right-2 min-w-[1.5rem] h-6 px-1.5 rounded-full bg-primary-600 text-white text-xs font-bold flex items-center justify-center shadow-md ring-2 ring-white dark:ring-gray-900">
                                    {{ $enCarrito }}
                                </span>
                                @endif
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
            <div class="pos-cart-col bg-white dark:bg-white/5 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 shadow-sm overflow-hidden">

                {{-- Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-transparent space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Orden actual</h2>
                        @if($this->totalItems > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
                            {{ $this->totalItems }} {{ $this->totalItems === 1 ? 'platillo' : 'platillos' }}
                        </span>
                        @endif
                    </div>

                    {{-- Input del Cliente --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Nombre del cliente</label>
                        <input
                            type="text"
                            wire:model.blur="nombre_cliente"
                            placeholder="Consumidor Final"
                            class="w-full rounded-lg border-none ring-1 ring-gray-950/10 dark:ring-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-primary-500 px-3 py-2"
                        />
                    </div>

                    {{-- Selección de Tipo de Orden --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Tipo de orden</label>
                        <div class="pos-two-cols">
                            {{-- Botón Comer Aquí --}}
                            <button
                                type="button"
                                wire:click="$set('tipo_orden', 'restaurante')"
                                class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg transition shadow-sm
                                    {{ $tipo_orden === 'restaurante'
                                        ? 'bg-primary-600 text-white ring-1 ring-primary-600'
                                        : 'bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 ring-1 ring-gray-950/10 dark:ring-white/20 hover:bg-gray-50 dark:hover:bg-white/10' }}"
                            >
                                <x-heroicon-m-building-storefront class="w-4 h-4"/>
                                Comer Aquí
                            </button>

                            {{-- Botón Para Llevar --}}
                            <button
                                type="button"
                                wire:click="$set('tipo_orden', 'llevar')"
                                class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg transition shadow-sm
                                    {{ $tipo_orden === 'llevar'
                                        ? 'bg-primary-600 text-white ring-1 ring-primary-600'
                                        : 'bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 ring-1 ring-gray-950/10 dark:ring-white/20 hover:bg-gray-50 dark:hover:bg-white/10' }}"
                            >
                                <x-heroicon-m-shopping-bag class="w-4 h-4"/>
                                Para Llevar
                            </button>
                        </div>
                    </div>

                    {{-- Comensales: stepper táctil (Solo se muestra si es Restaurante) --}}
                    @if($tipo_orden === 'restaurante')
                    <div class="pt-1">
                        <label for="numero_personas" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Número de Personas</label>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                wire:click="$set('numero_personas', {{ max(1, (int) $numero_personas - 1) }})"
                                aria-label="Menos personas"
                                class="pos-step bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 text-gray-800 dark:text-gray-200 flex-shrink-0"
                            >−</button>
                            <input
                                type="number"
                                id="numero_personas"
                                wire:model.live.debounce.500ms="numero_personas"
                                min="1"
                                class="flex-1 text-center rounded-lg border-none ring-1 ring-gray-950/10 dark:ring-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white text-base font-bold focus:ring-2 focus:ring-primary-500 px-3 py-2"
                            />
                            <button
                                type="button"
                                wire:click="$set('numero_personas', {{ (int) $numero_personas + 1 }})"
                                aria-label="Más personas"
                                class="pos-step bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 text-gray-800 dark:text-gray-200 flex-shrink-0"
                            >+</button>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Items del carrito --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    @forelse($carrito as $key => $item)
                    <div class="flex items-start gap-2">
                        <div class="flex items-center gap-1">
                            <button wire:click="decrementar('{{ $key }}')" aria-label="Quitar uno" class="pos-step bg-gray-100 dark:bg-white/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-gray-800 dark:text-gray-200">−</button>
                            <span class="w-7 text-center font-bold text-sm text-gray-900 dark:text-white">{{ $item['cantidad'] }}</span>
                            <button wire:click="incrementar('{{ $key }}')" aria-label="Agregar uno" class="pos-step bg-gray-100 dark:bg-white/10 hover:bg-green-100 dark:hover:bg-green-500/20 text-gray-800 dark:text-gray-200">+</button>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 dark:text-white leading-tight truncate">{{ $item['nombre'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">L. {{ number_format($item['precio'], 2) }} c/u</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-sm text-gray-900 dark:text-white">L. {{ number_format($item['precio'] * $item['cantidad'], 2) }}</p>
                            <button wire:click="remover('{{ $key }}')" class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 transition mt-0.5">
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
                <div class="p-4 border-t border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-transparent space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Notas</label>
                        <textarea
                            wire:model.blur="notas"
                            rows="2"
                            placeholder="Sin cebolla, extra salsa..."
                            class="w-full rounded-lg border-none ring-1 ring-gray-950/10 dark:ring-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-gray-500 resize-none focus:ring-2 focus:ring-primary-500 px-3 py-2"
                        ></textarea>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-base font-bold text-gray-700 dark:text-gray-300">Total</span>
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">L. {{ number_format($this->total, 2) }}</span>
                    </div>

                    {{-- BOTÓN ENVIAR: siempre visible con color explícito --}}
                    <button
                        wire:click="enviarACocina"
                        @if(empty($carrito)) disabled @endif
                        class="w-full py-3 rounded-xl font-bold text-base transition active:scale-95
                            {{ empty($carrito)
                                ? 'bg-gray-200 dark:bg-white/10 text-gray-400 dark:text-gray-500 cursor-not-allowed'
                                : 'bg-primary-600 hover:bg-primary-500 text-white' }}"
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
                        class="w-full py-2 rounded-xl font-semibold text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 border border-red-200 dark:border-red-500/30 hover:bg-red-50 dark:hover:bg-red-500/10 transition"
                    >
                        Cancelar orden
                    </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
             SECCIÃ“N 2: Ã“RDENES PENDIENTES DEL DÃA EN CAJA/POS
         â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
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
                                <th class="px-4 py-3">Cocina</th>
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
                                <td class="px-3 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($orden->numerosCocina as $numeroCocina)
                                            <span class="inline-flex items-center rounded-full px-2 py-1 font-bold text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                {{ ucfirst($numeroCocina->seccion) }} #{{ $numeroCocina->numero }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-3 py-3 font-medium text-gray-900 dark:text-white text-sm">{{ $orden->nombre_cliente }}</td>
                                <td class="px-3 py-3 text-gray-600 dark:text-gray-300 text-sm max-w-xs">
                                    @foreach($orden->detalles as $d)
                                        <span class="inline-block text-xs">{{ $d->cantidad }}× {{ $d->platillo?->nombre ?? '?' }}</span>@if(!$loop->last), @endif
                                    @endforeach
                                    @if($orden->notas)
                                    <br><span class="text-xs text-red-500 italic">{{ $orden->notas }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 font-bold text-gray-900 dark:text-white text-sm whitespace-nowrap">L. {{ number_format($orden->total, 2) }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                    {{-- Acción principal destacada --}}
                                    <button
                                        wire:click="marcarComoEntregada({{ $orden->id }})"
                                        class="pos-action pos-action-primary"
                                    >
                                        <x-heroicon-o-check class="h-4 w-4" />
                                        Entregar
                                    </button>
                                    {{-- Acciones secundarias como iconos con tooltip para no saturar la fila --}}
                                    <button
                                        wire:click="mountAction('viewOrder', { orderId: {{ $orden->id }} })"
                                        title="Ver detalle" aria-label="Ver detalle"
                                        class="pos-action border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 !p-2"
                                    >
                                        <x-heroicon-o-eye class="h-4 w-4" />
                                    </button>
                                    <button
                                        wire:click="mountAction('editOrder', { orderId: {{ $orden->id }} })"
                                        title="Editar pedido" aria-label="Editar pedido"
                                        class="pos-action pos-action-info !p-2"
                                    >
                                        <x-heroicon-o-pencil-square class="h-4 w-4" />
                                    </button>
                                    <button
                                        wire:click="mountAction('deleteOrder', { orderId: {{ $orden->id }} })"
                                        title="Eliminar pedido" aria-label="Eliminar pedido"
                                        class="pos-action pos-action-danger !p-2"
                                    >
                                        <x-heroicon-o-trash class="h-4 w-4" />
                                    </button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $orden->created_at->format('h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
             SECCIÃ“N 3: Ã“RDENES ENTREGADAS HOY
         â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
        <div wire:poll.15s class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-500"/>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Órdenes Entregadas Hoy</h2>
                </div>
                @if($this->ordenesEntregadas->count() > 0)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                    {{ $this->ordenesEntregadas->count() }} entregada(s)
                </span>
                @endif
            </div>

            @if($this->ordenesEntregadas->isEmpty())
                <div class="py-6 text-center text-gray-400 dark:text-gray-500 text-sm">
                    No hay órdenes entregadas aún.
                </div>
            @else
                <div class="max-h-60 overflow-y-auto">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($this->ordenesEntregadas as $orden)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-4 py-2 font-bold text-gray-500 dark:text-gray-400">
                                    @forelse($orden->numerosCocina as $numeroCocina)
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs dark:bg-gray-700">
                                            {{ ucfirst($numeroCocina->seccion) }} #{{ $numeroCocina->numero }}
                                        </span>
                                    @empty
                                        —
                                    @endforelse
                                </td>
                                <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $orden->nombre_cliente }}</td>
                                <td class="px-4 py-2">
                                    <button
                                        wire:click="mountAction('viewOrder', { orderId: {{ $orden->id }} })"
                                        class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-200 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                    >
                                        <x-heroicon-o-eye class="h-4 w-4" />
                                        Ver detalle
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-xs text-right">{{ $orden->updated_at->format('h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
