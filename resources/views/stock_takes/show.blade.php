@extends('layouts.app')

@section('title', 'Stock Take Audit #' . $stockTake->stock_take_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-xs font-bold px-2 py-0.5 bg-gray-100 rounded text-gray-700">{{ $stockTake->stock_take_number }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                    {{ $stockTake->status === 'APPROVED' ? 'bg-emerald-100 text-emerald-800' : ($stockTake->status === 'REVIEWED' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ $stockTake->status }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mt-1">Stock Take Audit & Reconciliation</h1>
            <p class="text-xs text-gray-500">Auditing {{ $stockTake->warehouse_name }} on {{ $stockTake->date }}</p>
        </div>
        <div class="flex space-x-2">
            @if(in_array($stockTake->status, ['IN_PROGRESS', 'REVIEWED']))
                <form action="{{ route('stock-takes.approve', $stockTake->id) }}" method="POST" onsubmit="return confirm('Approve this stock take? This will immediately apply reconciliation adjustments to live inventory.');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center">
                        <i data-lucide="check-check" class="w-4 h-4 mr-1.5"></i>
                        Approve & Apply Adjustments
                    </button>
                </form>
            @endif
            <a href="{{ route('stock-takes.index') }}" class="px-3.5 py-1.5 border rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50">
                &larr; Back
            </a>
        </div>
    </div>

    <!-- Count Entry Form -->
    <form action="{{ route('stock-takes.update-counts', $stockTake->id) }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800">Counted Items & Variance Calculation</h3>
                @if($stockTake->status !== 'APPROVED')
                    <button type="submit" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold">
                        Save Physical Counts
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase">
                        <tr>
                            <th class="py-3 px-4">Item & Code</th>
                            <th class="py-3 px-4 text-right">System Qty (Book)</th>
                            <th class="py-3 px-4 text-right w-36">Counted Qty <span class="text-red-500">*</span></th>
                            <th class="py-3 px-4 text-right">Variance Qty</th>
                            <th class="py-3 px-4 text-right">Unit Cost</th>
                            <th class="py-3 px-4 text-right">Variance Value</th>
                            <th class="py-3 px-4 w-48">Discrepancy Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $idx => $it)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <input type="hidden" name="counts[{{ $idx }}][item_id]" value="{{ $it->item_id }}">
                                    <a href="{{ route('items.show', $it->item_id) }}" class="font-bold text-gray-900 hover:text-emerald-600 block">
                                        {{ $it->item_name }}
                                    </a>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $it->item_code }}</span>
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-gray-700">
                                    {{ number_format($it->system_quantity, 0) }} {{ $it->unit_name ?? '' }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($stockTake->status === 'APPROVED')
                                        <span class="font-black text-sm text-gray-900">{{ number_format($it->counted_quantity ?? $it->system_quantity, 0) }}</span>
                                    @else
                                        <input type="number" step="0.01" min="0" name="counts[{{ $idx }}][counted_quantity]"
                                               value="{{ old('counts.'.$idx.'.counted_quantity', $it->counted_quantity ?? $it->system_quantity) }}" required
                                               class="w-full px-2 py-1 border rounded-lg text-right font-bold focus:border-emerald-500 focus:outline-none">
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-sm {{ $it->difference < 0 ? 'text-rose-600' : ($it->difference > 0 ? 'text-emerald-600' : 'text-gray-500') }}">
                                    {{ $it->difference > 0 ? '+' : '' }}{{ number_format($it->difference, 0) }}
                                </td>
                                <td class="py-3 px-4 text-right text-gray-600">{{ format_currency($it->unit_cost) }}</td>
                                <td class="py-3 px-4 text-right font-bold {{ $it->difference_value < 0 ? 'text-rose-600' : ($it->difference_value > 0 ? 'text-emerald-600' : 'text-gray-900') }}">
                                    {{ format_currency($it->difference_value) }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($stockTake->status === 'APPROVED')
                                        <span class="text-gray-600">{{ $it->reason ?? '-' }}</span>
                                    @else
                                        <input type="text" name="counts[{{ $idx }}][reason]" value="{{ old('counts.'.$idx.'.reason', $it->reason) }}" placeholder="Reason if variance..."
                                               class="w-full px-2 py-1 border rounded-lg focus:border-emerald-500 focus:outline-none">
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
@endsection
