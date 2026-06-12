<?php

namespace App\Filament\Resources\NominaResource\Pages;

use App\Filament\Resources\NominaResource;
use App\Models\DetalleNominas;
use App\Models\Empleado;
use Filament\Resources\Pages\CreateRecord;

class CreateNomina extends CreateRecord
{
    protected static string $resource = NominaResource::class;

    public array $empleadosSeleccionados = [];

    public function mount(): void
    {
        parent::mount();

        $this->empleadosSeleccionados = Empleado::with(['deduccionesAplicadas.deduccion', 'percepcionesAplicadas.percepcion'])
            ->get()
            ->map(function (Empleado $empleado) {
                $salario = $empleado->salario;

                $deduccionesArray = $empleado->deduccionesAplicadas->map(function ($relacion) use ($salario) {
                    $deduccion = $relacion->deduccion;
                    if (! $deduccion) {
                        return null;
                    }

                    $tipo = trim(strtolower($deduccion->tipo_valor ?? ''));
                    $valorCalculado = $tipo === 'porcentaje' ? ($salario * ($deduccion->valor / 100)) : $deduccion->valor;

                    return [
                        'id' => $deduccion->id,
                        'nombre' => $deduccion->deduccion ?? '',
                        'tipo' => $tipo,
                        'valor' => $deduccion->valor,
                        'aplicada' => true,
                        'valorMostrado' => $tipo === 'porcentaje' ? rtrim(rtrim((string) $deduccion->valor, '0'), '.') . '%' : 'L. ' . number_format($deduccion->valor, 2),
                        'valorCalculado' => $valorCalculado,
                    ];
                })->filter()->values()->toArray();

                $percepcionesArray = $empleado->percepcionesAplicadas->map(function ($relacion) use ($salario) {
                    $percepcion = $relacion->percepcion;
                    if (! $percepcion) {
                        return null;
                    }

                    $tipo = trim(strtolower($percepcion->tipo_valor ?? ''));
                    $valorCalculado = $tipo === 'porcentaje' ? ($salario * ($percepcion->valor / 100)) : ($percepcion->valor ?? 0);

                    return [
                        'id' => $percepcion->id,
                        'nombre' => $percepcion->percepcion ?? '',
                        'valorMostrado' => $tipo === 'porcentaje' ? ($percepcion->valor . '%') : 'L. ' . number_format($percepcion->valor ?? 0, 2),
                        'valorCalculado' => $valorCalculado,
                        'aplicada' => true,
                    ];
                })->filter()->values()->toArray();

                $totalDeducciones = collect($deduccionesArray)->sum(fn ($item) => $item['aplicada'] ? $item['valorCalculado'] : 0);
                $totalPercepciones = collect($percepcionesArray)->sum(fn ($item) => $item['aplicada'] ? $item['valorCalculado'] : 0);

                return [
                    'empleado_id' => $empleado->id,
                    'nombre' => $empleado->nombre,
                    'salario' => $salario,
                    'salario_base' => $salario,
                    'deduccionesArray' => $deduccionesArray,
                    'percepcionesArray' => $percepcionesArray,
                    'total' => $salario + $totalPercepciones - $totalDeducciones,
                    'seleccionado' => true,
                ];
            })
            ->toArray();
    }

    public function create(bool $another = false): void
    {
        $nomina = \App\Models\Nominas::create([
            'mes' => $this->data['mes'],
            'año' => $this->data['año'] ?? date('Y'),
            'descripcion' => $this->data['descripcion'] ?? null,
            'tipo_pago' => $this->data['tipo_pago'] ?? 'mensual',
            'cerrada' => $this->data['cerrada'] ?? false,
            'created_by' => auth()->id(),
        ]);

        foreach ($this->empleadosSeleccionados as $empleadoInput) {
            if (empty($empleadoInput['seleccionado'])) {
                continue;
            }

            $empleado = Empleado::find($empleadoInput['empleado_id']);
            if (! $empleado) {
                continue;
            }

            $salario = (float) $empleado->salario;
            if (($this->data['tipo_pago'] ?? 'mensual') === 'quincenal') {
                $salario /= 2;
            } elseif (($this->data['tipo_pago'] ?? 'mensual') === 'semanal') {
                $salario /= 4.33;
            }

            $deducciones = collect($empleadoInput['deduccionesArray'] ?? [])->sum(fn ($item) => ($item['aplicada'] ?? false) ? ($item['valorCalculado'] ?? 0) : 0);
            $percepciones = collect($empleadoInput['percepcionesArray'] ?? [])->sum(fn ($item) => ($item['aplicada'] ?? false) ? ($item['valorCalculado'] ?? 0) : 0);
            $total = $salario + $percepciones - $deducciones;

            DetalleNominas::create([
                'nomina_id' => $nomina->id,
                'empleado_id' => $empleado->id,
                'sueldo_bruto' => $salario,
                'deducciones' => $deducciones,
                'percepciones' => $percepciones,
                'sueldo_neto' => $total,
                'created_by' => auth()->id(),
            ]);
        }

        $this->redirect($this->getResource()::getUrl('index'));
    }
}
