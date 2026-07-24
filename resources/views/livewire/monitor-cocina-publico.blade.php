<div>
    {{--
        Monitor de Cocina PÚBLICO (sin login). Estilos 100% autocontenidos con clases
        .km-* para no depender de utilidades de Tailwind ni del tema de Filament, ya que
        esta pantalla se sirve fuera del panel /admin.
    --}}
    <style>
        .km-wrap { max-width: 100%; }

        /* Barra superior */
        .km-toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .km-tabs { display: flex; gap: 0.6rem; flex-wrap: wrap; }
        .km-tab {
            padding: 0.5rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
            background-color: #1f2937;
            border: 1px solid #374151;
            color: #f3f4f6;
        }
        .km-tab:hover { background-color: #273244; }
        .km-tab--active {
            background-color: #2563eb;   /* azul */
            border-color: #2563eb;
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        .km-sound {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #1f2937;
            border: 1px solid #374151;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
        }
        .km-sound button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            color: inherit;
        }

        /* Grid de tarjetas */
        .km-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (min-width: 640px)  { .km-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (min-width: 1024px) { .km-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (min-width: 1280px) { .km-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

        .km-card {
            background-color: #1f2937;
            border: 1px solid #374151;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .km-card__header {
            padding: 0.65rem 0.9rem;
            background-color: #111827;
            border-bottom: 1px solid #374151;
        }
        .km-card__body { padding: 0.75rem; flex: 1 1 auto; }

        .km-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; }
        .km-title  { color: #f9fafb; font-size: 1rem; font-weight: 700; line-height: 1.2; margin: 0; }
        .km-strong { color: #e5e7eb; font-size: 1.15rem; font-weight: 800; text-align: right; }
        .km-muted  { color: #9ca3af; font-size: 0.85rem; margin-top: 0.25rem; }

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
            margin-top: 0.5rem;
            background-color: #78350f;
            color: #fcd34d;
        }
        .km-badge__personas { font-size: 0.85rem; font-weight: 600; opacity: 0.85; }

        .km-note {
            margin-top: 0.5rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.9rem;
            font-weight: 700;
            background-color: rgba(127, 29, 29, 0.25);
            color: #fca5a5;
            border: 1px solid #7f1d1d;
        }

        .km-items { list-style: none; margin: 0; padding: 0; }
        .km-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            padding: 0.3rem 0;
        }
        .km-qty {
            background-color: #374151;
            color: #f9fafb;
            padding: 0.15rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 800;
            min-width: 2.25rem;
            text-align: center;
        }
        .km-item-name { flex: 1 1 auto; color: #f9fafb; font-weight: 600; }
        .km-item-note { padding-left: 3rem; font-size: 1rem; color: #fca5a5; font-style: italic; font-weight: 500; }

        .km-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }
        .km-empty h3 { color: #f9fafb; font-size: 1.25rem; font-weight: 700; margin: 0.5rem 0; }
    </style>

    <div class="km-wrap">
        <div class="km-toolbar">
            <div class="km-tabs">
                @foreach($this->seccionTabs as $tab)
                    <a href="{{ $tab['url'] }}" class="km-tab {{ $tab['active'] ? 'km-tab--active' : '' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="km-sound">
                <button id="toggle-sound-btn" type="button">
                    <span id="sound-icon" style="font-size:1.25rem;">🔊</span>
                    <span id="sound-text" style="color:#4ade80; font-weight:700;">Sonido Activado</span>
                </button>
            </div>
        </div>

        <div wire:poll.5s>
            <div class="km-grid">
                @forelse($this->ordenes as $orden)
                    <div class="km-card">
                        <div class="km-card__header">
                            <div class="km-row">
                                <h3 class="km-title">Orden #{{ $orden->numeroCocinaPara($seccion) ?? '—' }}</h3>
                                <div class="km-strong">{{ $orden->nombre_cliente }}</div>
                            </div>

                            <div class="km-muted">{{ $orden->created_at->format('h:i A') }} ({{ $orden->created_at->diffForHumans() }})</div>

                            @if($detalleInicial = $orden->detalles->first())
                                <span class="km-badge">
                                    {{ $detalleInicial->tipo_orden === 'restaurante' ? '🍽 Comer Aquí' : '🛍 Para Llevar' }}
                                    @if($detalleInicial->tipo_orden === 'restaurante')
                                        <span class="km-badge__personas">({{ $detalleInicial->numero_personas }} personas)</span>
                                    @endif
                                </span>
                            @endif

                            @if($orden->notas)
                                <div class="km-note">Nota: {{ $orden->notas }}</div>
                            @endif
                        </div>

                        <div class="km-card__body">
                            <ul class="km-items">
                                @foreach($orden->detalles->where('platillo.seccion', $seccion)->where('platillo.tipo', 'comida') as $detalle)
                                    <li class="km-item">
                                        <span class="km-qty">{{ $detalle->cantidad }}</span>
                                        <span class="km-item-name">{{ $detalle->platillo?->nombre ?? 'Platillo Desconocido' }}</span>
                                    </li>
                                    @if($detalle->notas)
                                        <li class="km-item-note">* {{ $detalle->notas }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @empty
                    <div class="km-empty km-card">
                        <div style="font-size:3rem;">😊</div>
                        <h3>¡No hay órdenes activas!</h3>
                        <p>La cocina está al día.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Sonido propio por sección; si el archivo no existe, cae al beep sintético (también distinto por sección). --}}
        <audio id="new-order-sound" src="/sounds/new-order-{{ $seccion }}.mp3" preload="auto"></audio>
    </div>

    <script>
        // Guard de ejecución única: el script vive dentro del componente Livewire
        // (wire:poll.5s), que puede re-ejecutarlo en cada morph. Sin esto se apilan
        // varios setInterval y cada pedido sonaría 3-4 veces.
        if (window.__kitchenMonitorInit) { /* ya inicializado */ } else {
        window.__kitchenMonitorInit = true;

        const SECCION = @json($seccion);

        // Tonos distintos por sección para el beep sintético de respaldo.
        const SYNTH_TONES = {
            general: [[587.33, 0, 0.4], [880.00, 0.15, 0.6]],
            china:   [[440.00, 0, 0.4], [659.25, 0.15, 0.6]],
            pizza:   [[783.99, 0, 0.35], [1046.50, 0.18, 0.6]],
        };

        const runInit = () => {
            const audio = document.getElementById('new-order-sound');
            const toggleBtn = document.getElementById('toggle-sound-btn');
            const soundIcon = document.getElementById('sound-icon');
            const soundText = document.getElementById('sound-text');

            // Endpoint PÚBLICO (sin login) para detectar pedidos nuevos.
            const PENDING_URL = '/api/public/kitchen/latest-pending';

            let soundEnabled = localStorage.getItem('kitchen_sound_enabled') !== 'false';
            let lastOrderId = 0;

            function updateSoundUI() {
                if (soundEnabled) {
                    soundIcon.textContent = '🔊';
                    soundText.textContent = 'Sonido Activado';
                    soundText.style.color = '#4ade80';
                } else {
                    soundIcon.textContent = '🔇';
                    soundText.textContent = 'Sonido Desactivado';
                    soundText.style.color = '#9ca3af';
                }
            }
            updateSoundUI();

            let audioUnlocked = false;
            function unlockAudio() {
                if (audioUnlocked) return;
                audioUnlocked = true;
                audio.play().then(() => { audio.pause(); audio.currentTime = 0; }).catch(() => {});
                document.removeEventListener('click', unlockAudio);
                document.removeEventListener('keydown', unlockAudio);
            }
            document.addEventListener('click', unlockAudio);
            document.addEventListener('keydown', unlockAudio);

            toggleBtn.addEventListener('click', () => {
                soundEnabled = !soundEnabled;
                localStorage.setItem('kitchen_sound_enabled', soundEnabled);
                updateSoundUI();
                if (soundEnabled) playAlert(SECCION, true);
            });

            function playSynthesizedBeep(seccion) {
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
                    (SYNTH_TONES[seccion] || SYNTH_TONES.general).forEach(
                        ([freq, offset, duration]) => playNote(freq, now + offset, duration)
                    );
                } catch (e) {
                    console.error('No se pudo generar el tono sintético:', e);
                }
            }

            // Reproduce el sonido de la sección del pedido; si el mp3 no carga,
            // cae al beep sintético (también propio de esa sección).
            function playAlert(seccion, isTest = false) {
                if (!soundEnabled) return;
                seccion = SYNTH_TONES[seccion] ? seccion : 'general';
                audio.src = '/sounds/new-order-' + seccion + '.mp3';
                audio.currentTime = 0;
                audio.play().catch(error => {
                    if (error.name === 'NotAllowedError') {
                        if (isTest) playSynthesizedBeep(seccion);
                    } else {
                        playSynthesizedBeep(seccion);
                    }
                });
            }

            function checkNewOrders() {
                fetch(PENDING_URL)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.has_pending && data.order) {
                            const currentId = data.order.id;
                            if (currentId > lastOrderId) {
                                console.log('[Monitor Cocina] Pedido nuevo: #' + currentId + ' (' + data.order.seccion + ')');
                                playAlert(data.order.seccion);
                            }
                            lastOrderId = currentId;
                        }
                    })
                    .catch(error => console.error('Error al consultar pedidos pendientes:', error));
            }

            fetch(PENDING_URL)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.has_pending && data.order) {
                        lastOrderId = data.order.id;
                    }
                })
                .catch(error => console.error('Error al inicializar alerta:', error))
                .finally(() => {
                    checkNewOrders();
                    setInterval(checkNewOrders, 3000);
                });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runInit);
        } else {
            runInit();
        }
        } // fin guard __kitchenMonitorInit
    </script>
</div>
