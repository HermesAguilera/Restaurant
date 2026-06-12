<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'clientes', 'empleados', 'cajas', 'caja_aperturas', 'cais', 
            'facturas', 'nominas', 'deducciones', 'percepciones', 
            'inventario_insumos', 'movimientos_inventario', 'orden_compras', 
            'orden_compras_detalles', 'orden_compras_insumos', 
            'orden_compras_insumos_detalles', 'productos', 'proveedores',
            'tipo_empleados', 'tipo_orden_compras',
            'users'
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'empresa_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('empresa_id');
                });
            }
        }
    }

    public function down(): void
    {
        // No implementado para simplificar la purga definitiva.
    }

};
