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
        Schema::table('orden_restaurantes', function (Blueprint $table) {
            // Evita duplicados de numero_dia cuando dos cajas envían casi al mismo tiempo.
            $table->unique(['fecha_orden', 'numero_dia']);

            // El estado ya no se usa: el flujo real solo distingue creada (entregado_at null)
            // vs entregada (entregado_at con fecha), controlado por Dashboard::enviarACocina()
            // y Dashboard::marcarComoEntregada().
            $table->dropColumn('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orden_restaurantes', function (Blueprint $table) {
            $table->dropUnique(['fecha_orden', 'numero_dia']);
            $table->enum('estado', ['pendiente', 'en_cocina', 'listo', 'entregado', 'pagado', 'cancelado'])
                ->default('pendiente')
                ->after('mesa');
        });
    }
};
