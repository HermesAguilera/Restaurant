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
        Schema::table('orden_restaurante_detalles', function (Blueprint $table) {
            $table->dropForeign(['platillo_id']);
        });

        Schema::table('orden_restaurante_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('platillo_id')->nullable()->change();
        });

        Schema::table('orden_restaurante_detalles', function (Blueprint $table) {
            // Permite borrar un platillo del menú sin romper el historial de pedidos:
            // el detalle queda con platillo_id null y la vista ya lo maneja como "Platillo Desconocido".
            $table->foreign('platillo_id')->references('id')->on('platillos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orden_restaurante_detalles', function (Blueprint $table) {
            $table->dropForeign(['platillo_id']);
        });

        Schema::table('orden_restaurante_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('platillo_id')->nullable(false)->change();
        });

        Schema::table('orden_restaurante_detalles', function (Blueprint $table) {
            $table->foreign('platillo_id')->references('id')->on('platillos');
        });
    }
};
