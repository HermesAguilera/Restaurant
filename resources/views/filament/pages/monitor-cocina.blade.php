<x-filament-panels::page>
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
        <div class="flex gap-4 w-full sm:w-auto">
            @foreach(['general' => 'Comida General', 'china' => 'Comida China', 'pizza' => 'Pizza'] as $key => $label)
                <button
                    wire:click="$set('seccion', '{{ $key }}')"
                    class="px-6 py-2 rounded-xl font-bold transition {{ $seccion === $key ? 'bg-primary-600 text-white shadow' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- Control de Alerta Sonora -->
        <div class="flex items-center gap-2 bg-white dark:bg-gray-800 px-4 py-2 rounded-xl border dark:border-gray-700 shadow-sm w-full sm:w-auto justify-center sm:justify-start">
            <button id="toggle-sound-btn" class="flex items-center gap-2 text-sm font-semibold transition hover:opacity-80">
                <span id="sound-icon" class="text-xl">🔊</span>
                <span id="sound-text" class="text-green-600 dark:text-green-400 font-bold">Sonido Activado</span>
            </button>
        </div>
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

    <!-- Elemento de Audio Oculto -->
    <audio id="new-order-sound" src="/sounds/new-order.mp3" preload="auto"></audio>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const audio = document.getElementById('new-order-sound');
            const toggleBtn = document.getElementById('toggle-sound-btn');
            const soundIcon = document.getElementById('sound-icon');
            const soundText = document.getElementById('sound-text');

            let soundEnabled = localStorage.getItem('kitchen_sound_enabled') !== 'false';
            let lastOrderId = null;
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
                            
                            // Si ya teníamos registrado un ID anterior y este es mayor, es un pedido nuevo
                            if (lastOrderId !== null && currentId > lastOrderId) {
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
