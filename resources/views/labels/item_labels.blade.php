<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Item Barcode Labels - IMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&family=Noto+Sans+Khmer:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Google Sans', 'Google Sans Khmer', 'Noto Sans Khmer', 'Kantumruy Pro', sans-serif; background: #f1f5f9; padding: 20px; }
        .no-print { margin-bottom: 20px; text-align: center; }
        .btn { padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .btn-back { background: #64748b; margin-left: 8px; text-decoration: none; display: inline-block; }
        .label-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px; max-width: 1000px; margin: 0 auto; }
        .label-card { background: white; border: 1px dashed #94a3b8; border-radius: 8px; padding: 12px; text-align: center; page-break-inside: avoid; }
        .item-name { font-size: 12px; font-weight: bold; color: #1e293b; margin-bottom: 4px; max-height: 28px; overflow: hidden; }
        .item-sku { font-size: 11px; font-family: monospace; color: #475569; margin-bottom: 8px; }
        .barcode-img { max-width: 100%; height: 50px; margin: 4px auto; display: block; }
        .item-price { font-size: 12px; font-weight: bold; color: #0f172a; margin-top: 4px; }
        
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
    <button onclick="window.print()" class="btn">🖨️ Print Labels</button>
    <a href="{{ route('items.index') }}" class="btn btn-back">Back to Items</a>
</div>

<div class="label-grid">
    @forelse($items as $item)
    <div class="label-card">
        <div class="item-name">{{ $item->name }}</div>
        <div class="item-sku">SKU: {{ $item->sku }}</div>
        
        <!-- Barcode generated using SVG API or QR Code -->
        <img class="barcode-img" src="https://barcodeapi.org/api/128/{{ $item->barcode ?: $item->sku }}" alt="Barcode {{ $item->sku }}">
        
        <div class="item-price">{{ format_currency($item->selling_price ?: $item->cost_price) }}</div>
    </div>
    @empty
    <p>No items selected for label printing.</p>
    @endforelse
</div>

</body>
</html>
