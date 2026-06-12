<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Caja extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        
        'nombre',
        'descripcion',
        'estado',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function cajaAperturas()
    {
        return $this->hasMany(CajaApertura::class);
    }
}
