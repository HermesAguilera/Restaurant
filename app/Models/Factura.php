<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// El nombre de la clase es "Factura" (Mayúscula inicial)
class Factura extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'facturas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero_factura', // Añadido
        'cai_id',         // Añadido
        'nombre_cliente',
        
        'empleado_id',
        'fecha_factura',
        'estado',
        'subtotal',
        'impuestos',
        'total',
        'apertura_id', // Añadido
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // --- Relaciones ---

    protected $casts = [
    'fecha_factura' => 'date',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleFactura::class, 'factura_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function cajaApertura()
    {
        return $this->belongsTo(CajaApertura::class, 'apertura_id');
    }

    // --- RELACIONES AÑADIDAS ---

    /**
     * Una factura puede tener un CAI (o no).
     */
    public function cai()
    {
        return $this->belongsTo(Cai::class);
    }

}
