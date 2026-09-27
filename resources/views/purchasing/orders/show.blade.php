@extends('layouts.app')

@section('title', 'Purchase Order #' . $order->po_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-xs font-bold px-2 py-0.5 bg-gray-100 rounded text-gray-700">{{ $order->po_number }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                    {{ $order->status === 'RECEIVED' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'PARTIALLY_RECEIVED' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ $order->status }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mt-1">Purchase Order Details</h1>
            <p class="text-xs text-gray-500">Ordered on {{ $order->order_date }} for {{ $order->supplier_name }}</p>
        </div>
        <div class="flex items-center space-x-2">
            @if($order->status !== 'RECEIVED')
                <a href="{{ route('goods-receipts.create', ['po_id' => $order->id]) }}"
                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center">
                    <i data-lucide="package-check" class="w-4 h-4 mr-1.5"></i>
                    Receive Goods
                </a>
            @endif
            <button onclick="window.print()" class="px-3.5 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold shadow-sm flex items-center transition">
                <i data-lucide="printer" class="w-4 h-4 mr-1.5 text-gray-500"></i>
                Print PO
            </button>
            <a href="{{ route('purchase-orders.index') }}" class="px-3.5 py-1.5 border rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50">
                &larr; Back
            </a>
        </div>
    </div>

    <!-- Vendor & Delivery Summary Card -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div>
            <span class="text-gray-400 block">Vendor:</span>
            <span class="font-bold text-gray-800 text-sm">{{ $order->supplier_name }}</span>
            <span class="text-[10px] text-gray-500 block">{{ $order->supplier_phone }}</span>
        </div>
        <div>
            <span class="text-gray-400 block">Delivery Warehouse:</span>
            <span class="font-bold text-gray-800 text-sm">{{ $order->warehouse_name }}</span>
        </div>
        <div>
            <span class="text-gray-400 block">Expected Delivery:</span>
            <span class="font-semibold text-gray-800">{{ $order->expected_delivery_date ?? 'Immediate' }}</span>
        </div>
        <div>
            <span class="text-gray-400 block">Payment Terms:</span>
            <span class="font-semibold text-gray-800">{{ $order->payment_terms ?? 'Standard' }}</span>
        </div>
    </div>

    <!-- Line Items Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-sm font-bold text-gray-800">Ordered Items & Receiving Status</h3>
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 uppercase">
                <tr>
                    <th class="py-3 px-4">Item & SKU</th>
                    <th class="py-3 px-4 text-right">Ordered</th>
                    <th class="py-3 px-4 text-right">Received</th>
                    <th class="py-3 px-4 text-right">Outstanding</th>
                    <th class="py-3 px-4 text-right">Unit Price</th>
                    <th class="py-3 px-4 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($items as $it)
                    <tr>
                        <td class="py-3 px-4">
                            <a href="{{ route('items.show', $it->item_id) }}" class="font-bold text-gray-900 hover:text-emerald-600">
                                {{ $it->item_name }}
                            </a>
                            <span class="block text-[10px] text-gray-400 font-mono">{{ $it->item_code }}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-gray-800">{{ number_format($it->quantity, 0) }}</td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-600">{{ number_format($it->received_quantity, 0) }}</td>
                        <td class="py-3 px-4 text-right font-black {{ $it->remaining_quantity > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                            {{ number_format($it->remaining_quantity, 0) }}
                        </td>
                        <td class="py-3 px-4 text-right text-gray-700">{{ format_currency($it->unit_price) }}</td>
                        <td class="py-3 px-4 text-right font-black text-gray-900">{{ format_currency($it->total) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 font-bold border-t">
                <tr>
                    <td colspan="5" class="py-3 px-4 text-right text-gray-800">Grand Total PO Amount:</td>
                    <td class="py-3 px-4 text-right font-black text-sm text-emerald-700">{{ format_currency($order->total) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Goods Receiving History -->
    @if(count($receipts) > 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                <i data-lucide="package-check" class="w-4 h-4 mr-2 text-emerald-600"></i>
                Goods Receipts Log
            </h3>
            <div class="divide-y divide-gray-50 text-xs">
                @foreach($receipts as $rc)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <a href="{{ route('goods-receipts.show', $rc->id) }}" class="font-bold text-gray-800 hover:text-emerald-600 font-mono">
                                {{ $rc->receipt_number }}
                            </a>
                            <span class="text-gray-500 ml-2">Received by {{ $rc->receiver_name }} on {{ $rc->receipt_date }}</span>
                        </div>
                        <a href="{{ route('goods-receipts.show', $rc->id) }}" class="text-xs text-emerald-600 hover:underline">View GRN &rarr;</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
