@extends('layouts.app')

@section('title', 'Purchase Request #' . $purchaseRequest->request_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-xs font-bold px-2 py-0.5 bg-gray-100 rounded text-gray-700">{{ $purchaseRequest->request_number }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                    {{ $purchaseRequest->status === 'APPROVED' ? 'bg-emerald-100 text-emerald-800' : ($purchaseRequest->status === 'COMPLETED' ? 'bg-purple-100 text-purple-800' : ($purchaseRequest->status === 'REJECTED' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')) }}">
                    {{ $purchaseRequest->status }}
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-800">{{ $purchaseRequest->priority }} Priority</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mt-1">Purchase Request Details</h1>
            <p class="text-xs text-gray-500">Submitted by {{ $purchaseRequest->requester_name }} on {{ $purchaseRequest->request_date }}</p>
        </div>
        <div class="flex items-center space-x-2">
            @if($purchaseRequest->status === 'PENDING')
                <form action="{{ route('purchase-requests.status', $purchaseRequest->id) }}" method="POST" class="inline-block">
                    @csrf
                    <input type="hidden" name="status" value="APPROVED">
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center">
                        <i data-lucide="check" class="w-4 h-4 mr-1.5"></i>
                        Approve PR
                    </button>
                </form>
                <form action="{{ route('purchase-requests.status', $purchaseRequest->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Reject this request?');">
                    @csrf
                    <input type="hidden" name="status" value="REJECTED">
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center">
                        <i data-lucide="x" class="w-4 h-4 mr-1.5"></i>
                        Reject
                    </button>
                </form>
            @elseif($purchaseRequest->status === 'APPROVED')
                <a href="{{ route('purchase-orders.create', ['pr_id' => $purchaseRequest->id]) }}"
                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center">
                    <i data-lucide="shopping-cart" class="w-4 h-4 mr-1.5"></i>
                    Convert to PO
                </a>
            @endif

            <a href="{{ route('purchase-requests.index') }}" class="px-3.5 py-1.5 border rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50">
                &larr; Back
            </a>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div>
            <span class="text-gray-400 block">Department:</span>
            <span class="font-bold text-gray-800 text-sm">{{ $purchaseRequest->department_name ?? 'General' }}</span>
        </div>
        <div>
            <span class="text-gray-400 block">Requester:</span>
            <span class="font-semibold text-gray-800">{{ $purchaseRequest->requester_name }}</span>
        </div>
        <div>
            <span class="text-gray-400 block">Required By Date:</span>
            <span class="font-semibold text-gray-800">{{ $purchaseRequest->required_date ?? 'Standard' }}</span>
        </div>
        <div>
            <span class="text-gray-400 block">Approver:</span>
            <span class="font-semibold text-gray-800">{{ $purchaseRequest->approver_name ?? 'Pending' }}</span>
        </div>
        @if($purchaseRequest->purpose)
            <div class="col-span-2 sm:col-span-4 pt-2 border-t text-gray-600">
                <span class="font-semibold text-gray-700">Purpose / Justification:</span> {{ $purchaseRequest->purpose }}
            </div>
        @endif
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-sm font-bold text-gray-800">Requested Items</h3>
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 uppercase">
                <tr>
                    <th class="py-3 px-4">Item & SKU</th>
                    <th class="py-3 px-4 text-right">Quantity</th>
                    <th class="py-3 px-4 text-right">Est. Unit Price</th>
                    <th class="py-3 px-4 text-right">Est. Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php $grandEstTotal = 0; @endphp
                @foreach($items as $it)
                    @php $grandEstTotal += $it->estimated_total; @endphp
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
                        <td class="py-3 px-4 text-right text-gray-700">{{ format_currency($it->estimated_unit_price) }}</td>
                        <td class="py-3 px-4 text-right font-black text-gray-900">{{ format_currency($it->estimated_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 font-bold border-t">
                <tr>
                    <td colspan="3" class="py-3 px-4 text-right text-gray-800">Estimated Requisition Total:</td>
                    <td class="py-3 px-4 text-right font-black text-sm text-emerald-700">{{ format_currency($grandEstTotal) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Status History Trail -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
            <i data-lucide="history" class="w-4 h-4 mr-2 text-gray-500"></i>
            Status Transition History
        </h3>
        <div class="space-y-3">
            @forelse($histories as $h)
                <div class="flex items-start space-x-3 text-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5"></span>
                    <div>
                        <p class="font-semibold text-gray-800">
                            Status changed to <span class="font-bold text-emerald-700">{{ $h->to_status }}</span> by {{ $h->user_name ?? 'System' }}
                        </p>
                        @if($h->comment)
                            <p class="text-gray-500 mt-0.5">{{ $h->comment }}</p>
                        @endif
                        <span class="text-[10px] text-gray-400">{{ $h->created_at }}</span>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-xs">No status transitions recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
