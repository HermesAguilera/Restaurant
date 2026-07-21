<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenRestaurante extends Model
{
    protected $fillable = [
        'nombre_cliente', 'mesa', 'total', 'notas', 'numero_dia', 'fecha_orden', 'entregado_at'
    ];

    protected $casts = [
        'fecha_orden' => 'date',
        'entregado_at' => 'datetime',
    ];

    public function detalles()
    {
        return $this->hasMany(OrdenRestauranteDetalle::class);
    }

    public function numerosCocina()
    {
        return $this->hasMany(OrdenRestauranteCocinaNumero::class)
            ->orderBy('seccion');
    }

    public function numeroCocinaPara(string $seccion): ?int
    {
        return $this->numerosCocina
            ->firstWhere('seccion', $seccion)
            ?->numero;
    }

}
