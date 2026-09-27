<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Asset QR Labels - IMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&family=Noto+Sans+Khmer:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Google Sans', 'Google Sans Khmer', 'Noto Sans Khmer', 'Kantumruy Pro', sans-serif; background: #f1f5f9; padding: 20px; }
        .no-print { margin-bottom: 20px; text-align: center; }
        .btn { padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .btn-back { background: #64748b; margin-left: 8px; text-decoration: none; display: inline-block; }
        .label-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 15px; max-width: 1000px; margin: 0 auto; }
        .label-card { background: white; border: 1px dashed #94a3b8; border-radius: 8px; padding: 14px; display: flex; align-items: center; gap: 12px; page-break-inside: avoid; }
        .qr-box { flex-shrink: 0; width: 80px; height: 80px; }
        .qr-box img { width: 100%; height: 100%; object-fit: contain; }
        .info-box { flex: 1; text-align: left; }
        .asset-tag { font-size: 14px; font-weight: bold; font-family: monospace; color: #4338ca; }
        .asset-name { font-size: 11px; font-weight: 600; color: #1e293b; margin-top: 2px; }
        .asset-sn { font-size: 10px; color: #64748b; font-family: monospace; margin-top: 2px; }
        .property-of { font-size: 9px; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; margin-bottom: 2px; }
        
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none; }
            .label-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; }
            .label-card { border: 1px solid #cbd5e1; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="btn">🖨️ Print Asset Tags</button>
    <a href="{{ route('assets.index') }}" class="btn btn-back">Back to Assets</a>
</div>

<div class="label-grid">
    @forelse($assets as $asset)
    <div class="label-card">
        <div class="qr-box">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(url('/assets/' . $asset->id)) }}" alt="QR {{ $asset->asset_tag }}">
        </div>
        <div class="info-box">
            <div class="property-of">Property of Organization</div>
            <div class="asset-tag">{{ $asset->asset_tag }}</div>
            <div class="asset-name">{{ $asset->name }}</div>
            @if($asset->serial_number)
                <div class="asset-sn">SN: {{ $asset->serial_number }}</div>
            @endif
        </div>
    </div>
    @empty
    <p>No assets selected for label printing.</p>
    @endforelse
</div>

</body>
</html>
