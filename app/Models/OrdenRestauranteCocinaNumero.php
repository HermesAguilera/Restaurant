<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenRestauranteCocinaNumero extends Model
{
    protected $table = 'orden_restaurante_cocina_numeros';

    protected $fillable = [
        'orden_restaurante_id',
        'seccion',
        'fecha_orden',
        'numero',
    ];

    protected $casts = [
        'fecha_orden' => 'date',
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenRestaurante::class, 'orden_restaurante_id');
    }
}
