<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 max-w-lg mx-auto w-full">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Apertura de Caja</h2>
    <form wire:submit.prevent="aperturar" class="space-y-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Monto Inicial</label>
            <input type="number" step="0.01" wire:model="monto_inicial" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 px-4 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Empleado</label>
            <input type="text" value="{{ $empleado->nombre ?? $empleado->name ?? '' }}" readonly class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 px-4 py-2 cursor-not-allowed">
        </div>
        <button type="submit" class="w-full py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition mt-2 shadow-sm">Aperturar</button>
    </form>
    @if(session()->has('success'))
        <div class="mt-4 p-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium border border-green-200 dark:border-green-800/50">{{ session('success') }}</div>
    @endif
</div>
