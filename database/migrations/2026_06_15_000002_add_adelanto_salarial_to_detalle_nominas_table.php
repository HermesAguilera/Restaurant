<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_nominas', function (Blueprint $table) {
            $table->decimal('adelanto_salarial', 10, 2)->default(0)->after('percepciones');
        });
    }

    public function down(): void
    {
        Schema::table('detalle_nominas', function (Blueprint $table) {
            $table->dropColumn('adelanto_salarial');
        });
    }
};
