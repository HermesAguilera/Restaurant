@php
    $heading = $this->getHeading();
    $cardWidth = 'md';
@endphp

<x-filament-panels::page.simple class="login-page">
    
    <!-- Efecto de burbujas flotantes (enviado al fondo) -->
    <div class="bubbles-container">
        @for ($i = 1; $i <= 15; $i++)
            <div class="bubble"></div>
        @endfor
    </div>
    
    <!-- Contenido del Login integrado en la tarjeta de Filament -->
    <div class="glass-card-content">
        <div class="flex justify-center mb-4">
            <img src="https://jadehsystem.com/images/Logo.png" alt="JADEH" class="h-6 w-16 object-contain" />
        </div>
        
        <div class="mb-4 text-center">
            <h2 class="text-xl font-bold text-gray-800">Bienvenido</h2>
            <p class="text-sm text-gray-600">Ingresa tus credenciales para continuar</p>
        </div>
        
        <!-- Formulario estándar de Filament que se auto-integra con Livewire -->
        <x-filament-panels::form wire:submit="authenticate">
            {{ $this->form }}
            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
    </div>
    
    <style>
        /* Aplicamos el fondo animado al cuerpo del documento */
        body, html {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: transparent !important;
        }
        
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -10;
            background: linear-gradient(-45deg, #e5d9c6, #b7c9b0, #f5efc2ff, #e6dca3, #b7c9b0, #166534, #a3c686);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Estilo cristalizado aplicado sobre la tarjeta simple de Filament */
        .fi-simple-layout {
            background: transparent !important;
        }

        .fi-simple-main-card {
            background: rgba(255, 255, 255, 0.4) !important;
            backdrop-filter: blur(10px) !important;
            border-radius: 1rem !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            padding: 2rem !important;
            z-index: 10;
        }

        /* Inputs siempre visibles, fondo blanco y opacidad 1 */
        .fi-input,
        .filament-forms-text-input-component input,
        .filament-forms-password-input-component input,
        .fi-input:focus {
            background: #fff !important;
            opacity: 1 !important;
            border: 2px solid #166534 !important;
            color: #222 !important;
        }

        /* Labels grandes y gruesos */
        .fi-fo-field-wrp-label {
            font-size: 1.05rem !important;
            font-weight: 700 !important;
            color: #166534 !important;
        }

        /* Botón de envío */
        button[type="submit"] {
            margin-top: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 12px rgba(22,163,74,0.2) !important;
            background-color: #166534 !important;
            color: #fff !important;
            font-size: 1.125rem !important;
            font-weight: 600 !important;
        }
        button[type="submit"]:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(22,163,74,0.3) !important;
            background-color: #14532d !important;
        }
        
        /* Efecto de burbujas */
        .bubbles-container {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0; left: 0;
            overflow: hidden;
            z-index: -1;
            pointer-events: none;
        }
        
        .bubble {
            position: absolute;
            bottom: -100px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            opacity: 0.6;
            animation: rise 15s infinite ease-in;
        }
        
        .bubble:nth-child(1) { width: 40px; height: 40px; left: 10%; animation-duration: 8s; }
        .bubble:nth-child(2) { width: 20px; height: 20px; left: 20%; animation-duration: 5s; animation-delay: 1s; }
        .bubble:nth-child(3) { width: 50px; height: 50px; left: 35%; animation-duration: 10s; animation-delay: 2s; }
        .bubble:nth-child(4) { width: 80px; height: 80px; left: 50%; animation-duration: 7s; }
        .bubble:nth-child(5) { width: 35px; height: 35px; left: 55%; animation-duration: 6s; animation-delay: 1s; }
        .bubble:nth-child(6) { width: 45px; height: 45px; left: 65%; animation-duration: 8s; animation-delay: 3s; }
        .bubble:nth-child(7) { width: 25px; height: 25px; left: 75%; animation-duration: 7s; animation-delay: 2s; }
        .bubble:nth-child(8) { width: 30px; height: 30px; left: 80%; animation-duration: 6s; animation-delay: 1s; }
        .bubble:nth-child(9) { width: 15px; height: 15px; left: 70%; animation-duration: 9s; }
        .bubble:nth-child(10) { width: 50px; height: 50px; left: 85%; animation-duration: 5s; animation-delay: 3s; }
        
        @keyframes rise {
            0% { bottom: -100px; transform: translateX(0); }
            50% { transform: translateX(100px); }
            100% { bottom: 100%; transform: translateX(-100px); }
        }
    </style>
</x-filament-panels::page.simple>
