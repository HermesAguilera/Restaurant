<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class Empleado extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'empleados';

    protected $fillable = [
        'numero_empleado',
        'fecha_ingreso',
        'salario',
        'deducciones_aplicables',
        'nombre',
        'departamento_empleado_id', // FK a departamentos_empleados
        
        'tipo_empleado_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'datetime',
        'fecha_ingreso' => 'date',
        'salario' => 'decimal:2',
        'deducciones_aplicables' => 'array',
        // 'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
        // 'deleted_at' => 'datetime',
    ];

    /**
     * Un empleado tiene un tipo de empleado.
     */
    public function tipoEmpleado()
    {
        return $this->belongsTo(TipoEmpleado::class);
    }

    public function detalleNominas()
    {
        return $this->hasMany(DetalleNomina::class, 'empleado_id');
    }

    // Relación con empleado_deducciones (1 empleado puede tener muchas deducciones)
    public function deduccionesAplicadas()
    {
        return $this->hasMany(EmpleadoDeducciones::class, 'empleado_id')->with('deduccion');
    }


    public function deducciones()
    {
        return $this->belongsToMany(\App\Models\Deducciones::class, 'empleado_deducciones', 'empleado_id', 'deduccion_id');
    }

        // Relación con empleado_percepciones (1 empleado puede tener muchas percepciones)
    public function percepcionesAplicadas()
    {
        return $this->hasMany(\App\Models\EmpleadoPercepciones::class, 'empleado_id')->with('percepcion');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre;
    }
    /**
     * Cambio jessuri: Esta función booted() asigna automáticamente los campos de auditoría (created_by, updated_by, deleted_by)
     * con el ID del usuario autenticado al crear, actualizar o eliminar un empleado.
     * Así, estos campos se llenan sin intervención manual desde el formulario.
     */
    protected static function booted()
    {
        static::creating(function ($empleado) {
            // Generar número de empleado automáticamente
            $max = static::max('id') + 1;
            $empleado->numero_empleado = 'EMP-' . str_pad($max, 4, '0', STR_PAD_LEFT);

            // Asignar created_by
            if (auth()->check()) {
                $empleado->created_by = auth()->id();
            }
        });

        static::updating(function ($empleado) {
            if (auth()->check()) {
                $empleado->updated_by = auth()->id();
            }
        });

        static::deleting(function ($empleado) {
            if (auth()->check()) {
                $empleado->deleted_by = auth()->id();
                $empleado->save();
            }
        });
    }
}
