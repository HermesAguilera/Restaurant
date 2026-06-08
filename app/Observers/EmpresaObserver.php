<?php

namespace App\Observers;

use App\Models\Empresa;
use App\Models\Persona;
use App\Models\Cliente;
use App\Models\CategoriaCliente;
use Illuminate\Support\Facades\DB;

class EmpresaObserver
{
    /**
     * Handle the Empresa "created" event.
     *
     * @param  \App\Models\Empresa  $empresa
     * @return void
     */
    public function created(Empresa $empresa): void
    {
        // Usamos una transacción para asegurar que todo se cree o nada se cree.
        DB::transaction(function () use ($empresa) {
            try {
                // --- PERSONAS GENÉRICAS GLOBALES ---
                // Se busca o crea una única persona "Consumidor Final" para toda la app.
                $personaCF = Persona::firstOrCreate(
                    ['dni' => '0000000000000'],
                    [
                        'primer_nombre'   => 'Consumidor',
                        'primer_apellido' => 'Final',
                        'tipo_persona'    => 'natural',
                        'sexo'            => 'MASCULINO',
                        'fecha_nacimiento'=> '1990-01-01', // Usar fecha fija en lugar de now()
                        'direccion'       => 'Ciudad',
                        'telefono'        => '0000-0000',
                        // Aseguramos valores por defecto si la empresa no los tiene.
                        // ¡IMPORTANTE! Cambia el '1' por un ID que sí exista en tus tablas de paises, etc.
                        'pais_id'         => $empresa->pais_id ?? 1,
                        'departamento_id' => $empresa->departamento_id ?? 1,
                        'municipio_id'    => $empresa->municipio_id ?? 1,
                    ]
                );

                // Se busca o crea una única persona "Mayorista" para toda la app.
                $personaMayorista = Persona::firstOrCreate(
                    ['dni' => '1111111111111'],
                    [
                        'primer_nombre'   => 'Consumidor',
                        'primer_apellido' => 'Mayorista',
                        'tipo_persona'    => 'natural',
                        'sexo'            => 'MASCULINO',
                        'fecha_nacimiento'=> '1990-01-01',
                        'direccion'       => 'Ciudad',
                        'telefono'        => '1111-1111',
                        'pais_id'         => $empresa->pais_id ?? 1,
                        'departamento_id' => $empresa->departamento_id ?? 1,
                        'municipio_id'    => $empresa->municipio_id ?? 1,
                    ]
                );

                // --- CATEGORÍAS DE CLIENTE ---
                // Usamos firstOrCreate para que si no existen, se creen automáticamente.
                $catCF = CategoriaCliente::firstOrCreate(['nombre' => 'Consumidor Final']);
                $catMayorista = CategoriaCliente::firstOrCreate(['nombre' => 'Mayorista']);

                // --- CLIENTES POR DEFECTO PARA LA NUEVA EMPRESA ---
                
                // Cliente Consumidor Final
                Cliente::firstOrCreate(
                    [
                        'empresa_id' => $empresa->id,
                        'persona_id' => $personaCF->id,
                    ],
                    [
                        // NO pasamos 'numero_cliente'. El modelo Cliente se encargará de generarlo.
                        'rtn'                  => '00000000000000',
                        'categoria_cliente_id' => $catCF->id,
                    ]
                );

                // Cliente Consumidor Mayorista
                Cliente::firstOrCreate(
                    [
                        'empresa_id' => $empresa->id,
                        'persona_id' => $personaMayorista->id,
                    ],
                    [
                        // NO pasamos 'numero_cliente'. El modelo Cliente se encargará de generarlo.
                        'rtn'                  => null,
                        'categoria_cliente_id' => $catMayorista->id,
                    ]
                );

            } catch (\Exception $e) {
                // Si algo falla, registramos el error para poder depurarlo.
                Log::error('Error al crear clientes por defecto para la empresa ID ' . $empresa->id . ': ' . $e->getMessage());
                // La transacción hará rollback automáticamente, revirtiendo los cambios.
                throw $e;
            }
        });
    }

    /**
     * Handle the Empresa "updated" event.
     *
     * @param  \App\Models\Empresa  $empresa
     * @return void
     */
    public function updated(Empresa $empresa): void
    {
        //
    }

    /**
     * Handle the Empresa "deleted" event.
     *
     * @param  \App\Models\Empresa  $empresa
     * @return void
     */
    public function deleted(Empresa $empresa): void
    {
        //
    }

    /**
     * Handle the Empresa "restored" event.
     *
     * @param  \App\Models\Empresa  $empresa
     * @return void
     */
    public function restored(Empresa $empresa): void
    {
        //
    }

    /**
     * Handle the Empresa "force deleted" event.
     *
     * @param  \App\Models\Empresa  $empresa
     * @return void
     */
    public function forceDeleted(Empresa $empresa): void
    {
        //
    }
}

