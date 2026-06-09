<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenRestauranteDetalle extends Model
{
    protected $fillable = [
        'orden_restaurante_id', 'platillo_id', 'cantidad', 'precio_unitario', 'subtotal', 'notas'
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenRestaurante::class);
    }

    public function platillo()
    {
        return $this->belongsTo(Platillo::class);
    }
}
