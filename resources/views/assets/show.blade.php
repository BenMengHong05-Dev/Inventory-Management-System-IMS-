@extends('layouts.app')

@section('title', 'Asset - ' . $asset->asset_name)

@section('content')
<div class="space-y-6">
    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-xs font-bold px-2 py-0.5 bg-gray-100 rounded text-gray-700">{{ $asset->asset_code }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                    {{ $asset->status === 'IN_STOCK' ? 'bg-emerald-100 text-emerald-800' : ($asset->status === 'ASSIGNED' ? 'bg-blue-100 text-blue-800' : ($asset->status === 'MAINTENANCE' ? 'bg-rose-100 text-rose-800' : 'bg-gray-100 text-gray-800')) }}">
                    {{ $asset->status }}
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold
                    {{ $asset->condition === 'GOOD' || $asset->condition === 'NEW' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $asset->condition }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mt-1">{{ $asset->asset_name }}</h1>
            <p class="text-xs text-gray-500 font-mono">Serial: {{ $asset->serial_number ?? 'No serial recorded' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            @if($asset->status === 'IN_STOCK')
                <a href="{{ route('asset-assignments.create', ['asset_id' => $asset->id]) }}"
                   class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center">
                    <i data-lucide="user-check" class="w-4 h-4 mr-1.5"></i>
                    Assign Asset
                </a>
            @elseif(in_array($asset->status, ['ASSIGNED', 'IN_USE']))
                <a href="{{ route('asset-returns.create', ['asset_id' => $asset->id]) }}"
                   class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold shadow-sm flex items-center">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 mr-1.5"></i>
                    Return Asset
                </a>
            @endif

            <a href="{{ route('maintenance.create', ['asset_id' => $asset->id]) }}"
               class="px-3.5 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold shadow-sm flex items-center">
                <i data-lucide="wrench" class="w-4 h-4 mr-1.5 text-gray-500"></i>
                Log Repair
            </a>

            @if($asset->status !== 'DISPOSED')
                <a href="{{ route('disposals.create', ['asset_id' => $asset->id]) }}"
                   class="px-3.5 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-xl text-xs font-bold shadow-sm flex items-center">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-1.5"></i>
                    Dispose
                </a>
            @endif

            <a href="{{ route('labels.assets', ['asset_id' => $asset->id]) }}" target="_blank"
               class="px-3.5 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold shadow-sm flex items-center">
                <i data-lucide="qr-code" class="w-4 h-4 mr-1.5 text-gray-500"></i>
                Label
            </a>

            <a href="{{ route('assets.edit', $asset->id) }}"
               class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center">
                <i data-lucide="edit-2" class="w-4 h-4 mr-1.5"></i>
                Edit
            </a>
        </div>
    </div>

    <!-- Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Photo, QR Code, Spec Sheet -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <div class="w-full h-48 bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center border border-gray-200">
                @if($asset->image)
                    <img src="{{ asset('storage/' . $asset->image) }}" class="w-full h-full object-cover">
                @else
                    <i data-lucide="laptop" class="w-12 h-12 text-gray-300"></i>
                @endif
            </div>

            <!-- QR Code render -->
            <div class="border-t pt-4 text-center">
                <span class="text-[10px] text-gray-400 block mb-2 font-mono">Asset QR: {{ $asset->asset_code }}</span>
                <div id="assetQrDiv" class="p-2 bg-white rounded-lg border inline-block"></div>
            </div>

            <div class="border-t pt-4 space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Category:</span>
                    <span class="font-semibold text-gray-800">{{ $asset->category_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Brand / Model:</span>
                    <span class="font-semibold text-gray-800">{{ $asset->brand_name ?? '-' }} {{ $asset->model ?? '' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Purchase Cost:</span>
                    <span class="font-bold text-gray-900">{{ format_currency($asset->purchase_cost) }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Purchase Date:</span>
                    <span class="text-gray-700">{{ $asset->purchase_date ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Warranty Expiry:</span>
                    <span class="font-semibold {{ $asset->warranty_expiry && $asset->warranty_expiry < date('Y-m-d') ? 'text-rose-600' : 'text-gray-800' }}">
                        {{ $asset->warranty_expiry ?? 'None' }}
                    </span>
                </div>
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-400">Current Storage:</span>
                    <span class="text-gray-800">{{ $asset->warehouse_name ?? 'Not in depot' }} ({{ $asset->location_code ?? '-' }})</span>
                </div>
            </div>
        </div>

        <!-- Right: Current Holder, Assignment History, Maintenance History -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Current Holder Card -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                    <i data-lucide="user" class="w-4 h-4 mr-2 text-blue-600"></i>
                    Current Custody / Assignment
                </h3>
                @if($asset->current_employee_id)
                    <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-blue-600 font-bold uppercase tracking-wider block">Assigned Holder</span>
                            <h4 class="text-base font-bold text-gray-900">{{ $asset->employee_name }} ({{ $asset->employee_code }})</h4>
                            <p class="text-xs text-gray-500">{{ $asset->department_name ?? 'General Department' }}</p>
                        </div>
                        <a href="{{ route('employees.show', $asset->current_employee_id) }}" class="text-xs text-blue-600 hover:underline font-semibold">
                            View Employee &rarr;
                        </a>
                    </div>
                @else
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-xs text-gray-500">
                        Asset is currently in stock at <span class="font-bold text-gray-800">{{ $asset->warehouse_name ?? 'Main Warehouse' }}</span> and available for assignment.
                    </div>
                @endif
            </div>

            <!-- Immutable Assignment History -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                    <i data-lucide="history" class="w-4 h-4 mr-2 text-emerald-600"></i>
                    Chain of Custody & Assignment History
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-400 uppercase border-b">
                            <tr>
                                <th class="py-2.5 px-3">Date</th>
                                <th class="py-2.5 px-3">Action</th>
                                <th class="py-2.5 px-3">Holder / Department</th>
                                <th class="py-2.5 px-3">Processed By</th>
                                <th class="py-2.5 px-3">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($assignmentHistories as $ah)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2.5 px-3 whitespace-nowrap text-gray-600">{{ $ah->assignment_date }}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $ah->action === 'ASSIGN' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ $ah->action }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 font-semibold text-gray-800">
                                        {{ $ah->new_holder_name ?? ($ah->prev_holder_name ?? 'Warehouse') }}
                                        <span class="block text-[10px] text-gray-400 font-normal">{{ $ah->department_name ?? '' }}</span>
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-600">{{ $ah->user_name ?? 'System' }}</td>
                                    <td class="py-2.5 px-3 text-gray-500">{{ $ah->note ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-400">No assignment history recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Maintenance History -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                    <i data-lucide="wrench" class="w-4 h-4 mr-2 text-rose-600"></i>
                    Maintenance & Repair History
                </h3>
                <div class="divide-y divide-gray-50 text-xs">
                    @forelse($maintenances as $m)
                        <div class="py-3 flex items-start justify-between">
                            <div>
                                <a href="{{ route('maintenance.show', $m->id) }}" class="font-bold text-gray-900 hover:text-emerald-600 font-mono">
                                    {{ $m->maintenance_number }}
                                </a>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ml-2
                                    {{ $m->status === 'COMPLETED' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $m->status }}
                                </span>
                                <p class="text-gray-700 mt-1 font-medium">{{ $m->problem }}</p>
                                <p class="text-[11px] text-gray-400">Tech: {{ $m->technician ?? 'External' }} &bull; Cost: {{ format_currency($m->cost) }}</p>
                            </div>
                            <span class="text-[10px] text-gray-400">{{ $m->request_date }}</span>
                        </div>
                    @empty
                        <p class="py-4 text-center text-gray-400">No maintenance records for this asset.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        new QRCode(document.getElementById("assetQrDiv"), {
            text: "{{ $asset->asset_code }}",
            width: 100,
            height: 100,
            colorDark : "#1e293b",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });
</script>
@endsection
