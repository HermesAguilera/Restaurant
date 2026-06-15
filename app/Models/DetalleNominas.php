<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DetalleNominas extends Model
{

        use HasFactory, SoftDeletes;

    protected $table = 'detalle_nominas';

    protected $fillable = [
        'nomina_id',
        'empleado_id',
        'sueldo_bruto',
        'deducciones',
        'deducciones_detalle',
        'percepciones',
        'adelanto_salarial',
        'percepciones_detalle',
        'sueldo_neto',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'sueldo_bruto' => 'decimal:2',
        'deducciones' => 'decimal:2',
        'adelanto_salarial' => 'decimal:2',
        'percepciones' => 'decimal:2',
        'sueldo_neto' => 'decimal:2',
    ];

    // Variable para almacenar deducciones excluidas en la sesión
    protected $deduccionesExcluidas = [];

    public function nomina()
    {
        return $this->belongsTo(Nominas::class, 'nomina_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    // Relación para deducciones asociadas a este detalle de nómina
    public function empleadoDeducciones()
    {
        return $this->hasMany(\App\Models\EmpleadoDeducciones::class, 'empleado_id', 'empleado_id');
    }

    // Relación para percepciones asociadas a este detalle de nómina
    public function empleadoPercepciones()
    {
        return $this->hasMany(\App\Models\EmpleadoPercepciones::class, 'empleado_id', 'empleado_id');
    }
}
