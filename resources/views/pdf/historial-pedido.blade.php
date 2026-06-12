<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido #{{ $orden->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111827; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: 700; margin: 0; }
        .muted { color: #6b7280; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 10px 8px; text-align: left; font-size: 14px; }
        th { background: #f9fafb; }
        .summary { margin-top: 18px; text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <div>
            <h1 class="title">Pedido #{{ $orden->id }}</h1>
            <div class="muted">Cliente: {{ $orden->nombre_cliente ?? 'Sin cliente' }}</div>
            <div class="muted">Mesa: {{ $orden->mesa ?? 'N/A' }}</div>
            <div class="muted">Fecha: {{ optional($orden->fecha_orden)->format('d/m/Y') ?? 'N/A' }}</div>
        </div>
        <div class="muted">Estado: {{ $orden->estado ?? 'N/A' }}</div>
    </div>

    <div class="muted">Notas: {{ $orden->notas ?: 'Sin notas' }}</div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orden->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->platillo->nombre ?? 'Sin producto' }}</td>
                    <td>{{ $detalle->cantidad ?? 0 }}</td>
                    <td>L {{ number_format($detalle->precio_unitario ?? 0, 2) }}</td>
                    <td>L {{ number_format($detalle->subtotal ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Este pedido no tiene detalles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <strong>Total: L {{ number_format($orden->total ?? 0, 2) }}</strong>
    </div>
</body>
</html>
