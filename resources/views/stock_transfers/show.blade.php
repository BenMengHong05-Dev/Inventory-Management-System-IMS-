@extends('layouts.app')

@section('title', 'Transfer #' . $transfer->transfer_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-xs font-bold px-2 py-0.5 bg-gray-100 rounded text-gray-700">{{ $transfer->transfer_number }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                    {{ $transfer->status === 'COMPLETED' ? 'bg-emerald-100 text-emerald-800' : ($transfer->status === 'IN_TRANSIT' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ $transfer->status }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mt-1">Inter-Warehouse Transfer Details</h1>
            <p class="text-xs text-gray-500">Initiated on {{ $transfer->transfer_date }} by {{ $transfer->requester_name }}</p>
        </div>
        <div class="flex space-x-2">
            <!-- Action buttons depending on state -->
            @if(in_array($transfer->status, ['PENDING', 'APPROVED']))
                <form action="{{ route('stock-transfers.dispatch', $transfer->id) }}" method="POST" onsubmit="return confirm('Dispatch shipment? This will immediately deduct stock from the source warehouse.');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center">
                        <i data-lucide="truck" class="w-4 h-4 mr-1.5"></i>
                        Dispatch (In Transit)
                    </button>
                </form>
            @elseif($transfer->status === 'IN_TRANSIT')
                <form action="{{ route('stock-transfers.receive', $transfer->id) }}" method="POST" onsubmit="return confirm('Receive shipment? This will increase stock in the destination warehouse and complete the transfer.');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-1.5"></i>
                        Confirm & Receive
                    </button>
                </form>
            @endif

            <a href="{{ route('stock-transfers.index') }}" class="px-3.5 py-1.5 border rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50">
                &larr; Back
            </a>
        </div>
    </div>

    <!-- Transfer Route Summary -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <span class="text-[10px] uppercase font-bold text-gray-400 block mb-1">Source Origin</span>
                <h4 class="font-bold text-gray-900 text-sm">{{ $transfer->from_warehouse_name }}</h4>
                <p class="text-xs text-gray-500 font-mono">{{ $transfer->from_location_code ?? 'Main Depot' }}</p>
            </div>

            <div class="flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mb-1">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">{{ $transfer->status }}</span>
            </div>

            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                <span class="text-[10px] uppercase font-bold text-emerald-600 block mb-1">Destination Target</span>
                <h4 class="font-bold text-gray-900 text-sm">{{ $transfer->to_warehouse_name }}</h4>
                <p class="text-xs text-gray-500 font-mono">{{ $transfer->to_location_code ?? 'Main Depot' }}</p>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-sm font-bold text-gray-800">Transfer Items</h3>
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 uppercase">
                <tr>
                    <th class="py-3 px-4">Item & Code</th>
                    <th class="py-3 px-4 text-right">Transferred Quantity</th>
                    <th class="py-3 px-4 text-right">Unit Value</th>
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
                        <td class="py-3 px-4 text-right font-bold text-sm text-gray-900">
                            {{ number_format($it->quantity, 0) }} {{ $it->unit_name ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-right text-gray-700">{{ format_currency($it->unit_cost) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
