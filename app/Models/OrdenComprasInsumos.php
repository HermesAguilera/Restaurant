<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class OrdenComprasInsumos extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orden_compras_insumos';

    protected $fillable = [
        'proveedor_id',
        
        'fecha_realizada',
        'estado',
        'descripcion',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class, 'proveedor_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, );
    }

      public function detalles()
    {
        return $this->hasMany(OrdenComprasInsumosDetalle::class, 'orden_compra_insumo_id');
    }
}
