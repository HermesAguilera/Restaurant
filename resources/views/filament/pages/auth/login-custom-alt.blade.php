@php
    $heading = $this->getHeading();
@endphp

@extends('layouts.auth')

@section('content')
    <div class="login-shell">
        <div class="bubbles-container" aria-hidden="true">
            @for ($i = 1; $i <= 15; $i++)
                <span class="bubble"></span>
            @endfor
        </div>

        <div class="glass-card-content">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/JADE.png') }}" alt="JADEH" class="h-16 w-auto object-contain" />
            </div>

            <div class="mb-6 text-center">
                <h2 class="text-3xl font-bold text-slate-900">Bienvenido</h2>
                <p class="mt-2 text-sm text-slate-600">Ingresa tus credenciales para continuar</p>
            </div>

            <x-filament-panels::form wire:submit="authenticate">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>
        </div>
    </div>

    <style>
        html, body {
            min-height: 100%;
        }

        body {
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(255,255,255,0.65), transparent 30%),
                linear-gradient(135deg, #e8dcab 0%, #d6c36f 35%, #f2efbf 100%) !important;
        }

        .login-shell {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: hidden;
        }

        .glass-card-content {
            position: relative;
            width: min(100%, 28rem);
            padding: 2rem;
            border-radius: 1.5rem;
            background: rgba(255, 255, 255, 0.42);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            z-index: 2;
        }

        .bubbles-container {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
        }

        .bubble {
            position: absolute;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.28);
            animation: floatUp 14s infinite ease-in;
            bottom: -120px;
        }

        .bubble:nth-child(1) { width: 42px; height: 42px; left: 8%; animation-duration: 8s; }
        .bubble:nth-child(2) { width: 20px; height: 20px; left: 16%; animation-duration: 6s; animation-delay: 1s; }
        .bubble:nth-child(3) { width: 56px; height: 56px; left: 30%; animation-duration: 10s; animation-delay: 2s; }
        .bubble:nth-child(4) { width: 84px; height: 84px; left: 48%; animation-duration: 7s; }
        .bubble:nth-child(5) { width: 34px; height: 34px; left: 58%; animation-duration: 9s; animation-delay: 1s; }
        .bubble:nth-child(6) { width: 46px; height: 46px; left: 66%; animation-duration: 8s; animation-delay: 3s; }
        .bubble:nth-child(7) { width: 26px; height: 26px; left: 74%; animation-duration: 7s; animation-delay: 2s; }
        .bubble:nth-child(8) { width: 30px; height: 30px; left: 82%; animation-duration: 6s; animation-delay: 1s; }
        .bubble:nth-child(9) { width: 18px; height: 18px; left: 70%; animation-duration: 9s; }
        .bubble:nth-child(10) { width: 50px; height: 50px; left: 88%; animation-duration: 5s; animation-delay: 3s; }
        .bubble:nth-child(n+11) { width: 22px; height: 22px; opacity: 0.2; }
        .bubble:nth-child(11) { left: 12%; animation-duration: 12s; animation-delay: 4s; }
        .bubble:nth-child(12) { left: 22%; animation-duration: 11s; animation-delay: 5s; }
        .bubble:nth-child(13) { left: 42%; animation-duration: 13s; animation-delay: 2s; }
        .bubble:nth-child(14) { left: 54%; animation-duration: 10s; animation-delay: 6s; }
        .bubble:nth-child(15) { left: 92%; animation-duration: 12s; animation-delay: 2s; }

        @keyframes floatUp {
            0% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-55vh) translateX(35px); }
            100% { transform: translateY(-115vh) translateX(-25px); }
        }
    </style>
@endsection
