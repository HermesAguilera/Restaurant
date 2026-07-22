<?php

use App\Models\Factura;
use App\Models\OrdenRestaurante;
use Illuminate\Support\Facades\Route;
use App\Filament\Pages\RecibirOrdenCompraInsumos;
use App\Filament\Resources\NominaResource\Pages\ViewNomina;

// ==========================================
// 1. RUTAS DE ADMINISTRACIÓN Y REPORTES
// ==========================================

// Ruta directa para descargar PDF de nómina
Route::get('/admin/nominas/{nomina}/generar-pdf', function ($nomina) {
    $page = app(ViewNomina::class);
    $page->record = \App\Models\Nominas::findOrFail($nomina);
    return $page->generarPDF();
})->name('nominas.generar-pdf')->middleware(['web', 'auth']);

// Rutas de Filament y otras personalizadas
if (file_exists(base_path('routes/filament.php'))) {
    require base_path('routes/filament.php');
}

Route::get('/admin/recibir-orden/{record}', RecibirOrdenCompraInsumos::class)
    ->middleware(['web', 'auth'])
    ->name('filament.pages.recibir-orden');

// ==========================================
// 2. RUTAS DE CLIENTES / FACTURACIÓN
// ==========================================

Route::get('/facturas/{factura}/visualizar', function (Factura $factura) {
    $factura->load([
        'empleado.persona',
        'cai',
        'detalles.producto',
        'empresa',
    ]);
    return view('pdf.factura', compact('factura'));
})->middleware(['web', 'auth'])->name('facturas.visualizar');

Route::get('/admin/historial-pedidos/{orden}/imprimir', function (OrdenRestaurante $orden) {
    abort_unless(auth()->check() && (auth()->user()->hasRole('root') || auth()->user()->can('ventas_ver')), 403);

    $orden->loadMissing('detalles.platillo');

    return view('pdf.historial-pedido', [
        'orden' => $orden,
    ]);
})->middleware(['web', 'auth'])->name('historial-pedidos.imprimir');

// ==========================================
// 2.5 MONITORES DE COCINA PÚBLICOS (SIN LOGIN)
// ==========================================

// Pantallas de cocina de solo lectura, sin mouse ni teclado: no requieren login.
// Rutas: /cocina/comida-general, /cocina/comida-china, /cocina/pizza
// (sin sección abre "Comida General" por defecto). Muestran todos los pedidos
// pendientes reutilizando la misma consulta que el monitor del panel /admin.
Route::get('/cocina/{seccion?}', \App\Livewire\MonitorCocinaPublico::class)
    ->name('cocina.publico');

// ==========================================
// 3. CONTROL DE ACCESO Y REDIRECCIONES (Corregido)
// ==========================================

// Redirección limpia si alguien entra a /login de forma manual
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

// Redirecciona la raíz '/' de forma segura al login de Filament usando GET
Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('welcome');
