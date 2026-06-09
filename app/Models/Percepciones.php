<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Percepciones extends Model
{
        use HasFactory, SoftDeletes;

    protected $table = 'percepciones';

        protected $fillable = [
        'percepcion',
        
        'valor',
        'tipo_valor',
        'depende_cantidad',
        'unidad_cantidad',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function percepcionesAplicadas()
    {
        return $this->hasMany(EmpleadoPercepciones::class, 'empleado_id')->with('deduccion');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, );
    }
}
