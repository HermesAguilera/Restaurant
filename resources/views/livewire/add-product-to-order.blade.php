<div class="mt-4">
    <div class="filament-card p-6 bg-white dark:bg-gray-800 shadow rounded-2xl border border-gray-200 dark:border-gray-700">
        <h3 class="text-xl font-semibold mb-6 text-gray-800 dark:text-white">Añadir Producto</h3>

        <form wire:submit.prevent>
            <div class="space-y-5">
                {{-- Aquí se mostrará cada campo del formulario en una sola columna --}}
                {{ $this->form }}
            </div>

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" color="primary" class="w-full sm:w-auto">
                    Guardar
                </x-filament::button>
            </div>
        </form>
    </div>
</div>
