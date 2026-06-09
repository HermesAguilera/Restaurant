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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura');
            $table->foreignId('cai_id')->nullable()->constrained('cais');
            // --- Relaciones ---
            $table->string('nombre_cliente')->default('Consumidor Final');
            $table->foreignId('empleado_id')->constrained('empleados');

            // --- Datos de la Factura ---
            $table->date('fecha_factura');
            $table->enum('estado', ['Pendiente', 'Pagada', 'Anulada', 'Vencida'])->default('Pendiente');

            // --- Totales Calculados ---
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuestos', 10, 2);
            $table->decimal('total', 10, 2);
            $table->foreignId('apertura_id')->nullable()->constrained('caja_aperturas');
            
            // --- Auditoría y Timestamps ---
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->unique('numero_factura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
