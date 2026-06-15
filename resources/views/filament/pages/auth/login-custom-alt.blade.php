@php
    $heading = $this->getHeading();
@endphp

<x-filament-panels::page.simple>
    <x-slot name="heading">
        <div class="flex flex-col items-center gap-3 text-center">
            <img src="{{ asset('images/JADE.png') }}" alt="JADEH" class="h-14 w-auto object-contain" />
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Bienvenido</h1>
                <p class="mt-1 text-sm text-slate-600">Ingresa tus credenciales para continuar</p>
            </div>
        </div>
    </x-slot>

    <x-filament-panels::form wire:submit="authenticate" class="space-y-6">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <style>
        .fi-simple-layout {
            background:
                radial-gradient(circle at top left, rgba(255,255,255,0.55), transparent 30%),
                linear-gradient(135deg, #e7dbaf 0%, #cdb65d 40%, #f2efc2 100%) !important;
        }

        .fi-simple-main-ctn {
            align-items: center !important;
            justify-content: center !important;
        }

        .fi-simple-main {
            width: min(100%, 30rem) !important;
        }

        .fi-simple-main-card {
            border-radius: 1.5rem !important;
            background: rgba(255, 255, 255, 0.44) !important;
            backdrop-filter: blur(18px) !important;
            border: 1px solid rgba(255, 255, 255, 0.35) !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12) !important;
            padding: 2rem !important;
        }

        .fi-input,
        .fi-input:focus,
        .fi-fo-field-wrp-input input {
            border-color: #166534 !important;
            background: #fff !important;
            color: #111827 !important;
        }

        .fi-fo-field-wrp-label {
            color: #14532d !important;
            font-weight: 700 !important;
        }

        .fi-btn {
            background: #166534 !important;
        }
    </style>
</x-filament-panels::page.simple>
