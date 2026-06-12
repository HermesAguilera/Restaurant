<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orden_restaurantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_cliente');
            $table->string('mesa')->nullable();
            $table->enum('estado', ['pendiente', 'en_cocina', 'listo', 'entregado', 'pagado', 'cancelado'])->default('pendiente');
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->unsignedSmallInteger('numero_dia')->default(1);
            $table->date('fecha_orden')->default(DB::raw('CURDATE()'));
            $table->timestamp('entregado_at')->nullable();
            $table->index('fecha_orden');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_restaurantes');
    }
};
