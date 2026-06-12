<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrdenRestaurante extends Model
{
    protected $fillable = [
        'nombre_cliente', 'mesa', 'estado', 'total', 'notas', 'numero_dia', 'fecha_orden', 'entregado_at'
    ];

    protected $casts = [
        'fecha_orden' => 'date',
        'entregado_at' => 'datetime',
    ];

    public function detalles()
    {
        return $this->hasMany(OrdenRestauranteDetalle::class);
    }

    /**
     * Calcula el número de orden para el día actual.
     * Obtiene el último número del día y suma 1.
     */
    public static function siguienteNumeroDia(): int
    {
        $hoy = now()->toDateString();
        $ultimo = static::whereDate('fecha_orden', $hoy)->max('numero_dia');
        return ($ultimo ?? 0) + 1;
    }
}
