<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Nomina - Restaurante Don Jorge</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 24px;
            color: #333;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-section th, .info-section td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .info-section th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 20px;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .deducciones-list, .percepciones-list {
            margin: 0;
            padding: 0 0 0 5px;
            list-style-type: none;
        }
        .strong {
            font-weight: bold;
        }
        .estado-cerrada {
            color: #d00;
            font-weight: bold;
        }
        .estado-abierta {
            color: #0a0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <h1>Restaurante Don Jorge</h1>
            <p></p>
            <h2>Nómina del mes de {{ $mesNombre }} {{ $nomina->año }}</h2>
        </div>

        <!-- Información general de la nómina -->
        <div class="info-section">
            <table>
                <tr>
                    <th>Fecha de generación</th>
                    <td>{{ $fechaGeneracion }}</td>
                </tr>
                <tr>
                    <th>Período</th>
                    <td>{{ $mesNombre }} {{ $nomina->año }}</td>
                </tr>
                <tr>
                    <th>Tipo de Pago</th>
                    <td>{{ ucfirst($tipoPagoNombre) }}</td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td class="{{ $nomina->cerrada ? 'estado-cerrada' : 'estado-abierta' }}">
                        {{ $nomina->cerrada ? 'Cerrada' : 'Abierta' }}
                    </td>
                </tr>
                @if($nomina->descripcion)
                <tr>
                    <th>Descripción</th>
                    <td>{{ $nomina->descripcion }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Detalle de empleados -->
        <h3>Detalle de Empleados</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="30%">Empleado</th>
                    <th width="15%">Salario</th>
                    <th width="22%">Deducciones</th>
                    <th width="22%">Percepciones</th>
                    <th width="11%">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalGeneral = 0; @endphp
                @foreach($empleados as $empleado)
                    @php $totalGeneral += $empleado['total']; @endphp
                    <tr>
                        <td>
                            <strong>{{ $empleado['nombre'] }}</strong>
                            @if(!empty($empleado['departamento']))
                            <br><small>Departamento: {{ $empleado['departamento'] }}</small>
                            @endif
                        </td>
                        <td class="text-right">L. {{ number_format($empleado['salario'], 2) }}</td>
                        <td>
                            @php echo nl2br(e($empleado['deducciones_detalle'] ?? 'Ninguna')); @endphp
                        </td>
                        <td>
                            @php echo nl2br(e($empleado['percepciones_detalle'] ?? 'Ninguna')); @endphp
                        </td>
                        <td class="text-right"><strong>L. {{ number_format($empleado['total'], 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right strong">TOTAL NÓMINA:</td>
                    <td class="text-right strong">L. {{ number_format($totalGeneral, 2) }}</td>
                </tr>
            </tfoot>
        </table>


        <div class="footer">
            <p>Este documento es una representación digital de la nómina de empleados</p>
            <p>Generado el {{ $fechaGeneracion }} - Restaurante Don Jorge</p>
        </div>
    </div>
</body>
</html>
