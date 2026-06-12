<?php

namespace App\Console\Commands;

use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateSampleFacturas extends Command
{
    protected $signature = 'create:sample-facturas';
    protected $description = 'Crear facturas de ejemplo';

    public function handle()
    {
        $this->info('Creando facturas de ejemplo...');

        for ($i = 1; $i <= 10; $i++) {
            $subtotal = rand(500, 5000);

            $factura = Factura::create([
                'nombre_cliente' => 'Cliente de prueba ' . $i,
                'empleado_id' => 1,
                'fecha_factura' => Carbon::now()->subDays(rand(1, 90)),
                'estado' => ['Pendiente', 'Pagada', 'Vencida'][rand(0, 2)],
                'subtotal' => $subtotal,
                'impuestos' => $subtotal * 0.15,
                'total' => $subtotal * 1.15,
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            $this->info("Factura #{$factura->id} creada para {$factura->nombre_cliente}");
        }

        $this->info('Facturas de ejemplo creadas exitosamente.');
    }
}
