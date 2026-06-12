<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orden_restaurantes', function (Blueprint $table) {
            $table->index('fecha_orden');
        });
    }

    public function down(): void
    {
        Schema::table('orden_restaurantes', function (Blueprint $table) {
            $table->dropIndex(['fecha_orden']);
        });
    }
};
