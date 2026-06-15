<table>
    <thead>
        <tr>
            <th colspan="5" style="text-align: center; font-size: 16px; font-weight: bold;">Restaurante Don Jorge</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center;"></th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-size: 14px; font-weight: bold;">Nómina del mes de {{ $mesNombre }} {{ $nomina->año }}</th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th style="font-weight: bold;">Fecha de generación</th>
            <td colspan="4">{{ $fechaGeneracion }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Período</th>
            <td colspan="4">{{ $mesNombre }} {{ $nomina->año }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Tipo de Pago</th>
            <td colspan="4">{{ ucfirst($tipoPagoNombre) }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Estado</th>
            <td colspan="4">{{ $nomina->cerrada ? 'Cerrada' : 'Abierta' }}</td>
        </tr>
        @if($nomina->descripcion)
        <tr>
            <th style="font-weight: bold;">Descripción</th>
            <td colspan="4">{{ $nomina->descripcion }}</td>
        </tr>
        @endif
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <th colspan="5" style="text-align: left; font-weight: bold;">Detalle de Empleados</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f2f2f2;">Empleado</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Salario</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Deducciones</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Percepciones</th>
            <th style="font-weight: bold; background-color: #f2f2f2;">Total</th>
        </tr>
        @foreach($empleados as $empleado)
            <tr>
                <td>{{ $empleado['nombre'] }}{{ !empty($empleado['departamento']) ? ' (Depto: '.$empleado['departamento'].')' : '' }}</td>
                <td style="text-align: right;">L. {{ number_format($empleado['salario'], 2) }}</td>
                <td>
                    @php echo nl2br(e($empleado['deducciones_detalle'] ?? 'Ninguna')); @endphp
                </td>
                <td>
                    @php echo nl2br(e($empleado['percepciones_detalle'] ?? 'Ninguna')); @endphp
                </td>
                <td style="text-align: right; font-weight: bold;">L. {{ number_format($empleado['total'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL NÓMINA:</td>
            <td style="text-align: right; font-weight: bold;">L. {{ number_format($totalNomina, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: center;">Este documento es una representación digital de la nómina de empleados</td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: center;">Generado el {{ $fechaGeneracion }} - Restaurante Don Jorge</td>
        </tr>
    </tbody>
</table>
