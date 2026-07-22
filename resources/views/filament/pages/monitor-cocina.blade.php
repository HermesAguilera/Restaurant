<x-filament-panels::page>
    {{--
        Estilos propios del Monitor de Cocina. Se definen aquí (en la página) con selectores
        basados en la clase .dark de Filament para no depender de utilidades Tailwind que
        no vienen compiladas en el CSS de Filament (ej. dark:bg-gray-900/50), lo que hacía
        que en modo oscuro la card quedara clara y el texto claro resultara invisible.
    --}}
    <style>
        .km-toolbar-btn {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            color: #111827;
        }
        .dark .km-toolbar-btn {
            background-color: #1f2937;
            border-color: #374151;
            color: #f3f4f6;
        }

        .km-card {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .dark .km-card {
            background-color: #1f2937;   /* gray-800 */
            border-color: #374151;       /* gray-700 */
        }

        .km-card__header {
            padding: 0.65rem 0.9rem;
            background-color: #f3f4f6;   /* gray-100 */
            border-bottom: 1px solid #e5e7eb;
        }
        .dark .km-card__header {
            background-color: #111827;   /* gray-900 */
            border-bottom-color: #374151;
        }

        /* Textos que deben adaptarse al fondo de la card */
        .km-title  { color: #111827; }
        .dark .km-title  { color: #f9fafb; }
        .km-strong { color: #1f2937; }
        .dark .km-strong { color: #e5e7eb; }
        .km-muted  { color: #6b7280; }
        .dark .km-muted  { color: #9ca3af; }
        .km-accent { color: #b45309; }   /* amber-700 */
        .dark .km-accent { color: #fbbf24; } /* amber-400 */

        /* Etiqueta tipo de orden (Comer Aquí / Para Llevar) — barra a lo ancho de la card */
        .km-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            width: 100%;
            padding: 0.35rem 0.6rem;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            font-weight: 800;
            background-color: #fde68a;   /* amber-200 */
            color: #92400e;              /* amber-800 */
        }
        .dark .km-badge {
            background-color: #78350f;   /* amber-900 */
            color: #fcd34d;              /* amber-300 */
        }
        .km-badge__personas {
            font-size: 0.85rem;
            font-weight: 600;
            opacity: 0.85;
        }

        /* Cantidad de cada platillo */
        .km-qty {
            background-color: #e5e7eb;
            color: #111827;
            padding: 0.15rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 800;
            min-width: 2.25rem;
            text-align: center;
        }
        .dark .km-qty {
            background-color: #374151;
            color: #f9fafb;
        }

        .km-item-name {
            color: #111827;
            font-weight: 600;
        }
        .dark .km-item-name { color: #f9fafb; }

        /* Nota de la orden */
        .km-note {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .dark .km-note {
            background-color: rgba(127, 29, 29, 0.25);
            color: #fca5a5;
            border-color: #7f1d1d;
        }
    </style>

    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
        {{--
            Cada sección tiene su propia ruta (ej. /admin/monitor-cocina/pizza) para poder
            abrir monitores directamente en una sección desde un script/acceso directo.
            Los botones ahora son enlaces que navegan a esas rutas.
        --}}
        <div class="flex gap-2 sm:gap-3 w-full sm:w-auto">
            @foreach($this->seccionTabs as $tab)
                <a
                    href="{{ $tab['url'] }}"
                    class="px-5 py-2 rounded-xl font-bold transition {{ $tab['active'] ? 'bg-primary-600 text-white shadow' : 'km-toolbar-btn' }}"
                >
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        <!-- Control de Alerta Sonora -->
        <div class="flex items-center gap-2 km-toolbar-btn px-4 py-2 rounded-xl shadow-sm w-full sm:w-auto justify-center sm:justify-start">
            <button id="toggle-sound-btn" class="flex items-center gap-2 text-sm font-semibold transition hover:opacity-80">
                <span id="sound-icon" class="text-xl">🔊</span>
                <span id="sound-text" class="text-green-600 dark:text-green-400 font-bold">Sonido Activado</span>
            </button>
        </div>
    </div>

    <div wire:poll.5s>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($this->ordenes as $orden)
                <div class="km-card">
                    <div class="km-card__header">
                        <div class="flex justify-between items-start gap-2">
                            <div class="min-w-0">
                                <h3 class="text-base font-bold km-title leading-tight">Orden #{{ $orden->numeroCocinaPara($seccion) ?? '—' }}</h3>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-lg font-extrabold km-strong">{{ $orden->nombre_cliente }}</div>
                            </div>
                        </div>

                        <div class="mt-1 text-sm km-muted">{{ $orden->created_at->format('h:i A') }} ({{ $orden->created_at->diffForHumans() }})</div>

                        @if($detalleInicial = $orden->detalles->first())
                            <div class="mt-2">
                                <span class="km-badge">
                                    {{ $detalleInicial->tipo_orden === 'restaurante' ? '🍽 Comer Aquí' : '🛍 Para Llevar' }}
                                    @if($detalleInicial->tipo_orden === 'restaurante')
                                        <span class="km-badge__personas">({{ $detalleInicial->numero_personas }} personas)</span>
                                    @endif
                                </span>
                            </div>
                        @endif

                        @if($orden->notas)
                            <div class="mt-2 p-2 km-note rounded text-sm font-bold">
                                Nota: {{ $orden->notas }}
                            </div>
                        @endif
                    </div>

                    <div class="p-3 flex-1">
                        <ul class="space-y-2.5">
                            @foreach($orden->detalles->where('platillo.seccion', $seccion)->where('platillo.tipo', 'comida') as $detalle)
                                <li class="flex items-center gap-3 text-xl">
                                    <span class="km-qty">{{ $detalle->cantidad }}</span>
                                    <span class="flex-1 km-item-name">{{ $detalle->platillo?->nombre ?? 'Platillo Desconocido' }}</span>
                                </li>
                                @if($detalle->notas)
                                    <li class="pl-12 text-base text-danger-500 dark:text-danger-400 italic font-medium">
                                        * {{ $detalle->notas }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center km-muted km-card">
                    <x-heroicon-o-face-smile class="w-16 h-16 mx-auto mb-4 text-gray-400"/>
                    <h3 class="text-xl font-bold km-title">¡No hay órdenes activas!</h3>
                    <p>La cocina está al día.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Elemento de Audio Oculto -->
    <audio id="new-order-sound" src="/sounds/new-order.mp3" preload="auto"></audio>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const audio = document.getElementById('new-order-sound');
            const toggleBtn = document.getElementById('toggle-sound-btn');
            const soundIcon = document.getElementById('sound-icon');
            const soundText = document.getElementById('sound-text');

            let soundEnabled = localStorage.getItem('kitchen_sound_enabled') !== 'false';
            // 0 en vez de null: como los IDs autoincrementales siempre son >= 1, este valor
            // funciona como "no hay pedido pendiente todavía" y permite que la comparación
            // `currentId > lastOrderId` detecte el primer pedido del día sin una guarda aparte
            // (con `null` esa guarda impedía que sonara la alerta del primer pedido).
            let lastOrderId = 0;
            let mp3Failed = false;

            // Detectar si el archivo MP3 falla al cargar (ej. 404)
            audio.addEventListener('error', () => {
                mp3Failed = true;
                console.warn('No se pudo cargar /sounds/new-order.mp3. Se usará el pitido de la API Web Audio como alternativa.');
            });

            function updateSoundUI() {
                if (soundEnabled) {
                    soundIcon.textContent = '🔊';
                    soundText.textContent = 'Sonido Activado';
                    soundText.className = 'text-green-600 dark:text-green-400 font-bold text-sm';
                } else {
                    soundIcon.textContent = '🔇';
                    soundText.textContent = 'Sonido Desactivado';
                    soundText.className = 'text-gray-400 dark:text-gray-500 font-normal text-sm';
                }
            }

            // Inicializar UI
            updateSoundUI();

            // Los navegadores bloquean audio.play() hasta que el usuario interactúe con la
            // página. Desbloqueamos el <audio> en la primera interacción (clic o tecla) con un
            // play/pause silencioso, para que la alerta del primer pedido sí suene.
            let audioUnlocked = false;
            function unlockAudio() {
                if (audioUnlocked) return;
                audioUnlocked = true;

                audio.play().then(() => {
                    audio.pause();
                    audio.currentTime = 0;
                }).catch(() => {
                    // Si falla igual queda desbloqueado el AudioContext para el beep sintético.
                });

                document.removeEventListener('click', unlockAudio);
                document.removeEventListener('keydown', unlockAudio);
            }
            document.addEventListener('click', unlockAudio);
            document.addEventListener('keydown', unlockAudio);

            // Alternar sonido activado/desactivado
            toggleBtn.addEventListener('click', () => {
                soundEnabled = !soundEnabled;
                localStorage.setItem('kitchen_sound_enabled', soundEnabled);
                updateSoundUI();

                if (soundEnabled) {
                    // Intentar reproducir para desbloquear la política de audio del navegador
                    playAlert(true);
                }
            });

            // Pitido sintético como fallback utilizando la API de Audio Web
            function playSynthesizedBeep() {
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return;
                    
                    const audioCtx = new AudioContext();
                    
                    const playNote = (freq, startTime, duration) => {
                        const osc = audioCtx.createOscillator();
                        const gainNode = audioCtx.createGain();
                        
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, startTime);
                        
                        gainNode.gain.setValueAtTime(0.3, startTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
                        
                        osc.connect(gainNode);
                        gainNode.connect(audioCtx.destination);
                        
                        osc.start(startTime);
                        osc.stop(startTime + duration);
                    };
                    
                    const now = audioCtx.currentTime;
                    // Chime agradable: Dos notas seguidas (Re5 y La5)
                    playNote(587.33, now, 0.4);
                    playNote(880.00, now + 0.15, 0.6);
                } catch (e) {
                    console.error('No se pudo generar el tono sintético de audio:', e);
                }
            }

            // Función para reproducir la alerta (MP3 o Sintético)
            function playAlert(isTest = false) {
                if (!soundEnabled) {
                    console.warn('[Monitor Cocina] Llegó un pedido nuevo pero el sonido está DESACTIVADO (botón arriba a la derecha).');
                    return;
                }

                if (mp3Failed) {
                    playSynthesizedBeep();
                    return;
                }

                audio.currentTime = 0;
                audio.play().catch(error => {
                    if (error.name === 'NotAllowedError') {
                        console.warn('La reproducción automática fue bloqueada por el navegador. Se requiere interacción del usuario.');
                        if (isTest) {
                            // Si es un test directo, intentamos forzar beep por si acaso
                            playSynthesizedBeep();
                        }
                    } else {
                        // Otro error (ej. archivo no encontrado), usar pitido sintético
                        playSynthesizedBeep();
                    }
                });
            }

            // Polling para consultar la API cada 3 segundos
            function checkNewOrders() {
                fetch('/api/orders/latest-pending')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.has_pending && data.order) {
                            const currentId = data.order.id;
                            
                            // Si este ID es mayor al último registrado, es un pedido nuevo
                            if (currentId > lastOrderId) {
                                console.log('[Monitor Cocina] Pedido nuevo detectado: #' + currentId + ' (anterior: #' + lastOrderId + ')');
                                playAlert();
                            }
                            
                            // Actualizar el último ID visto
                            lastOrderId = currentId;
                        }
                    })
                    .catch(error => console.error('Error al consultar pedidos pendientes:', error));
            }

            // Inicializar el baseline con el último ID existente sin reproducir sonido.
            // El polling arranca DESPUÉS de que el baseline se resuelve (incluso si falla),
            // para que nunca compita con checkNewOrders() por escribir lastOrderId primero.
            fetch('/api/orders/latest-pending')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.has_pending && data.order) {
                        lastOrderId = data.order.id;
                    }
                    console.log('[Monitor Cocina] Baseline inicial, último pedido pendiente:', lastOrderId);
                })
                .catch(error => console.error('Error al inicializar alerta de cocina:', error))
                .finally(() => {
                    checkNewOrders();
                    setInterval(checkNewOrders, 3000);
                });
        });
    </script>
</x-filament-panels::page>
