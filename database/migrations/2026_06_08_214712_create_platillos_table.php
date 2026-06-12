<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platillos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('categoria')->nullable(); // Ej: Entradas, Platos Fuertes, Bebidas
            $table->string('seccion')->default('general'); // general, china, pizza
            $table->string('tipo')->default('comida'); // comida o bebida
            $table->decimal('precio', 10, 2);
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platillos');
    }
};
