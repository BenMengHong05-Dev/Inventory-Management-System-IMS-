@extends('layouts.app')

@section('title', 'Stock In #' . $stockIn->stock_in_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-xs font-bold px-2 py-0.5 bg-gray-100 rounded text-gray-700">{{ $stockIn->stock_in_number }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ $stockIn->status }}</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mt-1">Stock In Receipt Details</h1>
            <p class="text-xs text-gray-500">Recorded on {{ $stockIn->date }} by {{ $stockIn->receiver_name }}</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="px-3.5 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold shadow-sm flex items-center transition">
                <i data-lucide="printer" class="w-4 h-4 mr-1.5 text-gray-500"></i>
                Print Receipt
            </button>
            <a href="{{ route('stock-in.index') }}" class="px-3.5 py-1.5 border rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50">
                &larr; Back
            </a>
        </div>
    </div>

    <!-- Header Summary Card -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div>
            <span class="text-gray-400 block">Supplier:</span>
            <span class="font-bold text-gray-800 text-sm">{{ $stockIn->supplier_name ?? 'Internal Receipt' }}</span>
        </div>
        <div>
            <span class="text-gray-400 block">Destination Warehouse:</span>
            <span class="font-bold text-gray-800 text-sm">{{ $stockIn->warehouse_name }}</span>
            <span class="text-[10px] text-gray-400">{{ $stockIn->location_code ?? 'Main Location' }}</span>
        </div>
        <div>
            <span class="text-gray-400 block">Reference / Invoice #:</span>
            <span class="font-semibold text-gray-800">{{ $stockIn->reference_number ?? '-' }}</span>
        </div>
        <div>
            <span class="text-gray-400 block">Received By:</span>
            <span class="font-semibold text-gray-800">{{ $stockIn->receiver_name }}</span>
        </div>
        @if($stockIn->note)
            <div class="col-span-2 sm:col-span-4 pt-2 border-t text-gray-600">
                <span class="font-semibold text-gray-700">Remarks:</span> {{ $stockIn->note }}
            </div>
        @endif
    </div>

    <!-- Items List Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-sm font-bold text-gray-800">Items Received</h3>
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 uppercase">
                <tr>
                    <th class="py-3 px-4">Item & Code</th>
                    <th class="py-3 px-4 text-right">Quantity</th>
                    <th class="py-3 px-4 text-right">Unit Cost</th>
                    <th class="py-3 px-4 text-right">Total Cost</th>
                    <th class="py-3 px-4">Batch / Serial</th>
                    <th class="py-3 px-4">Expiry Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php $grandTotal = 0; @endphp
                @foreach($items as $it)
                    @php $grandTotal += $it->total_cost; @endphp
                    <tr>
                        <td class="py-3 px-4">
                            <a href="{{ route('items.show', $it->item_id) }}" class="font-bold text-gray-900 hover:text-emerald-600">
                                {{ $it->item_name }}
                            </a>
                            <span class="block text-[10px] text-gray-400 font-mono">{{ $it->item_code }}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-sm text-gray-900">
                            {{ number_format($it->quantity, 0) }} {{ $it->unit_name ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-right text-gray-700">{{ format_currency($it->unit_cost) }}</td>
                        <td class="py-3 px-4 text-right font-black text-gray-900">{{ format_currency($it->total_cost) }}</td>
                        <td class="py-3 px-4 text-gray-600 font-mono">{{ $it->batch_number ?? ($it->serial_number ?? '-') }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ $it->expiry_date ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 font-bold border-t">
                <tr>
                    <td colspan="3" class="py-3 px-4 text-right text-gray-800">Grand Total Received:</td>
                    <td class="py-3 px-4 text-right font-black text-sm text-emerald-700">{{ format_currency($grandTotal) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
