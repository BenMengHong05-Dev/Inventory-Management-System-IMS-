@extends('layouts.app')

@section('title', 'Item Details - ' . $item->name_en)

@section('content')
<div class="space-y-6">
    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-gray-100 text-gray-700">{{ $item->code }}</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->status === 'ACTIVE' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">{{ $item->status }}</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">{{ $item->item_type }}</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mt-1">{{ $item->name_en }}</h1>
            <p class="text-xs text-gray-500">{{ $item->name_km }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('labels.items', ['item_id' => $item->id]) }}" target="_blank"
               class="px-3.5 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold shadow-sm flex items-center transition">
                <i data-lucide="printer" class="w-4 h-4 mr-1.5 text-gray-500"></i>
                Print Label
            </a>
            <a href="{{ route('items.edit', $item->id) }}"
               class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center transition">
                <i data-lucide="edit-2" class="w-4 h-4 mr-1.5"></i>
                Edit Item
            </a>
            <a href="{{ route('items.index') }}" class="px-3.5 py-2 border rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50">
                &larr; Catalog
            </a>
        </div>
    </div>

    <!-- Product Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Basic Details & Media -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <div class="w-full h-48 bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center border border-gray-200">
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover">
                @else
                    <i data-lucide="package" class="w-12 h-12 text-gray-300"></i>
                @endif
            </div>

            <!-- Barcode & QR code render -->
            <div class="border-t pt-4 space-y-3 text-center">
                <div>
                    <span class="text-[10px] text-gray-400 block mb-1">Barcode: {{ $item->barcode ?? $item->code }}</span>
                    <svg id="barcodeSvg" class="mx-auto"></svg>
                </div>
                <div class="border-t pt-3 flex flex-col items-center">
                    <span class="text-[10px] text-gray-400 block mb-1">QR Code: {{ $item->code }}</span>
                    <div id="qrcodeDiv" class="p-2 bg-white rounded-lg border"></div>
                </div>
            </div>

            <div class="border-t pt-4 space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Category:</span>
                    <span class="font-semibold text-gray-800">{{ $item->category_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Sub-Category:</span>
                    <span class="font-semibold text-gray-800">{{ $item->sub_category_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Brand:</span>
                    <span class="font-semibold text-gray-800">{{ $item->brand_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Model:</span>
                    <span class="font-semibold text-gray-800">{{ $item->model ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Unit of Measure:</span>
                    <span class="font-semibold text-gray-800">{{ $item->unit_name ?? 'Unit' }} ({{ $item->unit_code ?? '-' }})</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Default Cost:</span>
                    <span class="font-bold text-gray-900">{{ format_currency($item->default_unit_cost) }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Warehouse Stocks & Thresholds -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Stock Status Cards -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <span class="text-[11px] font-semibold text-gray-400 uppercase">Min Stock</span>
                    <h4 class="text-xl font-bold text-gray-800 mt-1">{{ number_format($item->min_stock, 0) }}</h4>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <span class="text-[11px] font-semibold text-gray-400 uppercase">Reorder Point</span>
                    <h4 class="text-xl font-bold text-amber-600 mt-1">{{ number_format($item->reorder_level, 0) }}</h4>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                    <span class="text-[11px] font-semibold text-gray-400 uppercase">Max Holding</span>
                    <h4 class="text-xl font-bold text-gray-800 mt-1">{{ number_format($item->max_stock, 0) }}</h4>
                </div>
            </div>

            <!-- Warehouse Quantities Breakdown Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                    <i data-lucide="warehouse" class="w-4 h-4 mr-2 text-emerald-600"></i>
                    Current Warehouse & Location Quantities
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-400 uppercase border-b">
                            <tr>
                                <th class="py-2.5 px-3">Warehouse</th>
                                <th class="py-2.5 px-3">Location</th>
                                <th class="py-2.5 px-3 text-right">On Hand</th>
                                <th class="py-2.5 px-3 text-right">Reserved</th>
                                <th class="py-2.5 px-3 text-right">Available</th>
                                <th class="py-2.5 px-3 text-right">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php
                                $grandQty = 0;
                                $grandValue = 0;
                            @endphp
                            @forelse($inventories as $inv)
                                @php
                                    $grandQty += $inv->quantity;
                                    $grandValue += $inv->total_value;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2.5 px-3 font-semibold text-gray-800">{{ $inv->warehouse_name }}</td>
                                    <td class="py-2.5 px-3 font-mono text-gray-600">{{ $inv->location_code ?? 'Main Depot' }}</td>
                                    <td class="py-2.5 px-3 text-right font-bold text-gray-900">{{ number_format($inv->quantity, 0) }}</td>
                                    <td class="py-2.5 px-3 text-right text-gray-500">{{ number_format($inv->reserved_quantity, 0) }}</td>
                                    <td class="py-2.5 px-3 text-right font-bold text-emerald-600">{{ number_format($inv->available_quantity, 0) }}</td>
                                    <td class="py-2.5 px-3 text-right font-semibold text-gray-800">{{ format_currency($inv->total_value) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-gray-400">No stock recorded in any warehouse yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold border-t">
                            <tr>
                                <td colspan="2" class="py-2.5 px-3 text-gray-800">Total Across All Warehouses</td>
                                <td class="py-2.5 px-3 text-right text-gray-900">{{ number_format($grandQty, 0) }}</td>
                                <td class="py-2.5 px-3 text-right text-gray-500">-</td>
                                <td class="py-2.5 px-3 text-right text-emerald-700">{{ number_format($grandQty, 0) }}</td>
                                <td class="py-2.5 px-3 text-right text-gray-900">{{ format_currency($grandValue) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Stock Movement History -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                    <i data-lucide="history" class="w-4 h-4 mr-2 text-purple-600"></i>
                    Recent Stock Movement History (Audit Trail)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-400 uppercase border-b">
                            <tr>
                                <th class="py-2 px-3">Date</th>
                                <th class="py-2 px-3">Type</th>
                                <th class="py-2 px-3">Warehouse</th>
                                <th class="py-2 px-3 text-right">In</th>
                                <th class="py-2 px-3 text-right">Out</th>
                                <th class="py-2 px-3 text-right">Balance</th>
                                <th class="py-2 px-3">User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-3 text-gray-500 whitespace-nowrap">{{ $tx->date }}</td>
                                    <td class="py-2 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                            {{ str_contains($tx->transaction_type, 'IN') ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $tx->transaction_type }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-gray-700">{{ $tx->warehouse_name }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-emerald-600">{{ $tx->quantity_in > 0 ? '+' . number_format($tx->quantity_in, 0) : '-' }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-rose-600">{{ $tx->quantity_out > 0 ? '-' . number_format($tx->quantity_out, 0) : '-' }}</td>
                                    <td class="py-2 px-3 text-right font-black text-gray-900">{{ number_format($tx->balance_after, 0) }}</td>
                                    <td class="py-2 px-3 text-gray-500">{{ $tx->user_name ?? 'System' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-400">No transactions recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Render Barcode
        const barcodeVal = "{{ $item->barcode ?: $item->code }}";
        JsBarcode("#barcodeSvg", barcodeVal, {
            format: "CODE128",
            width: 1.6,
            height: 40,
            displayValue: true,
            fontSize: 12
        });

        // Render QR Code
        new QRCode(document.getElementById("qrcodeDiv"), {
            text: "{{ $item->code }}",
            width: 90,
            height: 90,
            colorDark : "#1e293b",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });
</script>
@endsection
