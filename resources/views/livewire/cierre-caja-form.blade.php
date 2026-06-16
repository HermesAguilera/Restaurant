<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 max-w-lg mx-auto w-full text-center">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Cierre de Caja</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">¿Estás seguro que deseas realizar el cierre de caja? Esta acción no se puede deshacer.</p>
    <form wire:submit.prevent="cerrarApertura">
        <input type="hidden" wire:model="apertura_id">
        <button type="submit" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-sm">Cerrar Apertura</button>
    </form>
    @if(session()->has('success'))
        <div class="mt-4 p-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium border border-green-200 dark:border-green-800/50">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="mt-4 p-3 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium border border-red-200 dark:border-red-800/50">{{ session('error') }}</div>
    @endif
</div>
