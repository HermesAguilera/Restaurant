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
        Schema::create('orden_restaurante_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_restaurante_id')->constrained('orden_restaurantes')->onDelete('cascade');
            $table->foreignId('platillo_id')->constrained('platillos');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('notas')->nullable();
            $table->string('tipo_orden')->default('restaurante'); // 'restaurante' o 'llevar'
            $table->integer('numero_personas')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_restaurante_detalles');
    }
};
