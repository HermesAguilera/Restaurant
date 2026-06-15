<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdelantoSalarial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'adelantos_salariales';

    protected $fillable = [
        'empleado_id',
        'nomina_id',
        'monto',
        'estado',
        'fecha_solicitud',
        'fecha_aplicacion',
        'motivo',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_solicitud' => 'date',
        'fecha_aplicacion' => 'datetime',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function nomina()
    {
        return $this->belongsTo(Nominas::class, 'nomina_id');
    }

    protected static function booted()
    {
        static::creating(function ($adelanto) {
            if (auth()->check()) {
                $adelanto->created_by = auth()->id();
            }
        });

        static::updating(function ($adelanto) {
            if (auth()->check()) {
                $adelanto->updated_by = auth()->id();
            }
        });

        static::deleting(function ($adelanto) {
            if (auth()->check()) {
                $adelanto->deleted_by = auth()->id();
                $adelanto->save();
            }
        });
    }
}
