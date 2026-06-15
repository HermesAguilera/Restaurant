<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DetalleNominas;
use App\Models\Nominas;
use App\Models\Empleado;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;

class TablaEmpleadosNomina extends Component
{
    public $nominaId;
    public $empleados = [];

    public function mount($nominaId)
    {
        $this->nominaId = $nominaId;
        $this->cargarEmpleados();
    }

    #[On('agregarEmpleados')]

    public function cargarEmpleados()
    {
        $nomina = Nominas::find($this->nominaId);
        if ($nomina) {
            $this->empleados = $nomina->detalleNominas()->with('empleado.persona')->get();
        }
    }

    public function eliminarEmpleado($detalleId)
    {
        $detalle = DetalleNominas::find($detalleId);

        if ($detalle && $detalle->nomina_id == $this->nominaId) {
            $detalle->delete();

            Notification::make()
                ->title('Registro eliminado')
                ->body('Registro de pago eliminado correctamente del historial.')
                ->success()
                ->send();

            $this->cargarEmpleados();
        } else {
            Notification::make()
                ->title('Error')
                ->body('No se pudo eliminar el empleado de la nómina.')
                ->danger()
                ->send();
        }
    }

    public function agregarEmpleados($empleadosIds)
    {
        if (empty($empleadosIds)) {
            return;
        }

        $nomina = Nominas::find($this->nominaId);
        if (!$nomina) return;

        foreach ($empleadosIds as $empleadoId) {
            $empleado = Empleado::find($empleadoId);

            if (!$empleado) {
                continue;
            }

            $sueldo = $empleado->salario;

            $deduccionesArray = $empleado->deduccionesAplicadas->map(function ($relacion) use ($sueldo) {
                $deduccion = $relacion->deduccion;
                if (!$deduccion) return null;
                $esPorcentaje = trim(strtolower($deduccion->tipo_valor)) === 'porcentaje';
                $valorCalculado = $esPorcentaje ? ($sueldo * ($deduccion->valor / 100)) : $deduccion->valor;
                return [
                    'nombre' => $deduccion->deduccion,
                    'valorMostrado' => $esPorcentaje ? ($deduccion->valor . '%') : ('L. ' . number_format($deduccion->valor, 2)),
                    'valorCalculado' => $valorCalculado
                ];
            })->filter()->toArray();

            $percepcionesArray = $empleado->percepcionesAplicadas->map(function ($relacion) use ($sueldo) {
                $percepcion = $relacion->percepcion;
                if (!$percepcion) return null;
                
                $valorCalculado = $percepcion->valor ?? 0;
                if (($percepcion->percepcion ?? '') === 'Horas Extras') {
                    $cantidad = $relacion->cantidad_horas ?? 0;
                    $valorCalculado = $cantidad * $valorCalculado;
                }
                
                return [
                    'nombre' => $percepcion->percepcion,
                    'valorMostrado' => 'L. ' . number_format($valorCalculado, 2),
                    'valorCalculado' => $valorCalculado
                ];
            })->filter()->toArray();

            $totalDeducciones = collect($deduccionesArray)->sum('valorCalculado');
            $totalPercepciones = collect($percepcionesArray)->sum('valorCalculado');

            $total = $sueldo + $totalPercepciones - $totalDeducciones;

            DetalleNominas::create([
                'nomina_id' => $nomina->id,
                'empleado_id' => $empleadoId,
                'empresa_id' => $nomina->empresa_id,
                'sueldo_bruto' => $sueldo,
                'deducciones' => $totalDeducciones,
                'deducciones_detalle' => collect($deduccionesArray)
                    ->map(fn($item) => $item['nombre'] . ': ' . $item['valorMostrado'])
                    ->implode("\n"),
                'percepciones' => $totalPercepciones,
                'percepciones_detalle' => collect($percepcionesArray)
                    ->map(fn($item) => $item['nombre'] . ': ' . $item['valorMostrado'])
                    ->implode("\n"),
                'sueldo_neto' => $total,
                'created_by' => auth()->id(),
            ]);
        }

        Notification::make()
            ->title('Registros de pago agregados')
            ->body('Registros de pago agregados correctamente al historial.')
            ->success()
            ->send();
        $this->cargarEmpleados();
    }

    public function render()
    {
        return view('livewire.tabla-empleados-nomina');
    }
}
