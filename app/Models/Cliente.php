<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\TenantScoped;

class Cliente extends Model
{
    use HasFactory, TenantScoped, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'numero_cliente',
        'rtn',
        'persona_id',
        'empresa_id',
        'categoria_cliente_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        // 'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
        // 'deleted_at' => 'datetime',
    ];

    /**
     * Un cliente tiene muchas facturas (historial de compras).
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class, 'cliente_id');
    }

    /**
     * Un cliente pertenece a una persona (relación uno a uno inversa).
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Un cliente puede pertenecer opcionalmente a una empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Un cliente pertenece a una categoría de cliente.
     */
    public function categoriaCliente()
    {
        return $this->belongsTo(CategoriaCliente::class);
    }

    protected static function booted(): void
    {
        /**
         * Se ejecuta ANTES de que el modelo se guarde en la base de datos.
         * Esta es la solución definitiva: la lógica se ejecuta siempre,
         * sobrescribiendo cualquier valor incorrecto de otro lugar.
         */
        static::creating(function (Cliente $cliente) {
            // Calculamos el siguiente ID disponible, incluyendo los registros con borrado lógico.
            $maxId = self::withTrashed()->max('id') + 1;
            
            // Formateamos y asignamos el número de cliente SIEMPRE.
            $cliente->numero_cliente = 'CLI-' . str_pad($maxId, 8, '0', STR_PAD_LEFT);

            // Asignamos el usuario que lo creó.
            if (auth()->check() && !$cliente->created_by) {
                $cliente->created_by = auth()->id();
            }
        });
        
        /**
         * Se ejecuta ANTES de que un modelo existente se actualice.
         */
        static::updating(function(Cliente $cliente){
             if (auth()->check() && !$cliente->isDirty('updated_by')) {
                $cliente->updated_by = auth()->id();
            }
        });
    }
}
