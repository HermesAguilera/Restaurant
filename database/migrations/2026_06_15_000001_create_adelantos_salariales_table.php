<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adelantos_salariales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('nomina_id')->nullable()->constrained('nominas')->nullOnDelete();
            $table->decimal('monto', 12, 2);
            $table->string('estado', 20)->default('pendiente');
            $table->date('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_aplicacion')->nullable();
            $table->text('motivo')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adelantos_salariales');
    }
};
