<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR — {{ $asset->code }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f0f2f5; }
        .card { background: white; border: 2px solid #002046; border-radius: 16px; padding: 32px 28px; text-align: center; width: 280px; box-shadow: 0 8px 32px rgba(0,32,70,0.15); }
        .logo { font-size: 13px; font-weight: 900; color: #002046; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 20px; }
        .qr-wrapper { width: 200px; height: 200px; margin: 0 auto 16px; }
        .qr-wrapper svg { width: 100%; height: 100%; }
        .code { font-size: 20px; font-weight: 800; color: #002046; letter-spacing: 1px; margin-bottom: 4px; font-family: monospace; }
        .name { font-size: 13px; color: #374151; font-weight: 600; margin-bottom: 4px; }
        .location { font-size: 11px; color: #9ca3af; margin-bottom: 20px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 16px 0; }
        .url { font-size: 10px; color: #9ca3af; word-break: break-all; }
        .btn { display: inline-flex; align-items: center; gap: 6px; margin-top: 20px; padding: 8px 24px; background: #002046; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: none; }
        .btn:hover { background: #1b365d; }
        @media print {
            body { background: white; }
            .btn { display: none; }
            .card { box-shadow: none; border: 2px solid #002046; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">CMMS Pro</div>

        <div class="qr-wrapper">
            {!! $qrCode !!}
        </div>

        <div class="code">{{ $asset->code }}</div>
        <div class="name">{{ $asset->name }}</div>
        @if ($asset->location)
            <div class="location">{{ $asset->location->name }}</div>
        @endif

        <hr class="divider">
        <div class="url">{{ route('assets.show', $asset) }}</div>

        <button class="btn" onclick="window.print()">
            🖨 Imprimir
        </button>
    </div>
</body>
</html>
