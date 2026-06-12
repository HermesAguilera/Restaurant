<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            if (Schema::hasColumn('empleados', 'departamento_empleado_id')) {
                $table->dropConstrainedForeignId('departamento_empleado_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            if (! Schema::hasColumn('empleados', 'departamento_empleado_id')) {
                $table->foreignId('departamento_empleado_id')->nullable()->constrained('departamento_empleados')->nullOnDelete();
            }
        });
    }
};
