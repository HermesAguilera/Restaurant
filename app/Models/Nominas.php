<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Nominas extends Model
{
    /** @use HasFactory<\Database\Factories\NominasFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'nominas';

    protected $fillable = [
        'mes',
        'año',
        'descripcion',
        
        'empleado_id',
        'sueldo_bruto',
        'deducciones',
        'percepciones',
        'sueldo_neto',
        'cerrada',
        'tipo_pago',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // Relación con Empleado
    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class, 'empleado_id');
    }

    protected static function boot()
    {
    parent::boot();

    static::creating(function ($model) {
        $model->año = date('Y');
    });
    }

    public function detalleNominas()
    {
        return $this->hasMany(DetalleNominas::class, 'nomina_id');
    }
}

