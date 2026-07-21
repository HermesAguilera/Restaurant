<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tieneIndiceGlobal = collect(Schema::getIndexes('orden_restaurantes'))
            ->contains(fn (array $indice): bool => ($indice['name'] ?? null) === 'orden_restaurantes_fecha_orden_numero_dia_unique');

        if ($tieneIndiceGlobal) {
            Schema::table('orden_restaurantes', function (Blueprint $table) {
                // La secuencia deja de ser global: ahora vive por sección de cocina.
                $table->dropUnique(['fecha_orden', 'numero_dia']);
            });
        }

        Schema::create('orden_restaurante_cocina_numeros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_restaurante_id')
                ->constrained('orden_restaurantes')
                ->cascadeOnDelete();
            $table->string('seccion');
            $table->date('fecha_orden');
            $table->unsignedSmallInteger('numero');
            $table->timestamps();

            $table->unique(['orden_restaurante_id', 'seccion'], 'orcn_orden_seccion_unique');
            $table->unique(['fecha_orden', 'seccion', 'numero'], 'orcn_fecha_seccion_numero_unique');
        });

        // Conserva una numeración útil para los pedidos existentes al desplegar el cambio.
        $pedidosPorSeccion = DB::table('orden_restaurante_detalles as detalle')
            ->join('orden_restaurantes as orden', 'orden.id', '=', 'detalle.orden_restaurante_id')
            ->join('platillos as platillo', 'platillo.id', '=', 'detalle.platillo_id')
            ->where('platillo.tipo', 'comida')
            ->whereNotNull('platillo.seccion')
            ->select('orden.id as orden_id', 'orden.fecha_orden', 'orden.numero_dia', 'platillo.seccion')
            ->distinct()
            ->orderBy('orden.fecha_orden')
            ->orderBy('platillo.seccion')
            ->orderBy('orden.numero_dia')
            ->orderBy('orden.id')
            ->get();

        $secuencias = [];
        $ahora = now();

        foreach ($pedidosPorSeccion as $pedido) {
            $clave = $pedido->fecha_orden . '|' . $pedido->seccion;
            $secuencias[$clave] = ($secuencias[$clave] ?? 0) + 1;

            DB::table('orden_restaurante_cocina_numeros')->insert([
                'orden_restaurante_id' => $pedido->orden_id,
                'seccion' => $pedido->seccion,
                'fecha_orden' => $pedido->fecha_orden,
                'numero' => $secuencias[$clave],
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_restaurante_cocina_numeros');

        Schema::table('orden_restaurantes', function (Blueprint $table) {
            $table->unique(['fecha_orden', 'numero_dia']);
        });
    }
};
