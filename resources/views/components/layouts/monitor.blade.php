<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Monitor de Cocina' }}</title>
    <style>
        /* Layout base autocontenido: la pantalla de cocina no depende de Tailwind
           compilado, para que se vea correcta sin importar el estado del build. */
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            background-color: #0b1220;
            color: #f3f4f6;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif,
                'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
            padding: 1rem;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body>
    {{ $slot }}
</body>
</html>
