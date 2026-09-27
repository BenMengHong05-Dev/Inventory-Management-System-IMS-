@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'កត់ត្រាការទទួលទំនិញ (GRN)' : 'Receive Goods (GRN)')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'ផ្នែកលទ្ធកម្ម & ឃ្លាំង' : 'Procurement & Warehouse' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ app()->getLocale() == 'km' ? 'ប័ណ្ណទទួលទំនិញចូល (GRN)' : 'Goods Receiving Note (GRN)' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'កត់ត្រាការទទួលទំនិញពិតជាក់ស្ដែងតាមប័ណ្ណបញ្ជាទិញ PO ជាមួយនឹងការទទួលជាដំណាក់កាល' : 'Record physical item receipt against open Purchase Orders with partial receiving' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('goods-receipts.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to Receipts' }}
            </a>
        </div>
    </div>

    @if(!$selectedPo)
        <!-- Step 1: Select Open PO -->
        <div class="glass-card max-w-xl mx-auto p-8 text-center space-y-5 shadow-lg border border-slate-200/80">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mx-auto text-2xl shadow-sm">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">
                    {{ app()->getLocale() == 'km' ? 'ជ្រើសរើសប័ណ្ណបញ្ជាទិញ (PO)' : 'Select Purchase Order to Receive' }}
                </h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    {{ app()->getLocale() == 'km' ? 'ជ្រើសរើសប័ណ្ណបញ្ជាទិញដែលបានអនុម័តដើម្បីចាប់ផ្ដើមកត់ត្រាទំនិញដែលបានដឹកមកដល់' : 'Choose an approved or ordered PO to begin logging incoming deliveries' }}
                </p>
            </div>
            <form action="{{ route('goods-receipts.create') }}" method="GET" class="space-y-4 pt-2">
                <div class="text-left">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខកូដ PO' : 'Purchase Order #' }}</label>
                    <select name="po_id" required class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសប័ណ្ណបញ្ជាទិញ --' : '-- Choose Purchase Order --' }}</option>
                        @foreach($orders as $o)
                            <option value="{{ $o->id }}">{{ $o->po_number }} - {{ $o->supplier->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full btn-primary !py-2.5 justify-center shadow-md shadow-emerald-500/20">
                    <span>{{ app()->getLocale() == 'km' ? 'ទាញទិន្នន័យមុខទំនិញក្នុង PO' : 'Load PO Items' }}</span>
                    <i class="bi bi-arrow-right ml-1.5"></i>
                </button>
            </form>
        </div>
    @else
        <!-- Step 2: Record Partial Receiving -->
        <form action="{{ route('goods-receipts.store') }}" method="POST" class="glass-card p-6 md:p-8 space-y-8">
            @csrf
            <input type="hidden" name="purchase_order_id" value="{{ $selectedPo->id }}">

            <!-- Header Info -->
            <div class="space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានប័ណ្ណទទួលទំនិញ' : 'Receipt Details' }}</h2>
                        <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទ លេខប័ណ្ណ និងឃ្លាំងផ្ទុកទំនិញ' : 'GRN number, date, destination warehouse, and storage location' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខកូដ GRN' : 'GRN #' }}</label>
                        <input type="text" name="receipt_number" value="{{ old('receipt_number', $nextNumber) }}" required
                               class="form-input font-mono uppercase font-bold text-emerald-700">
                    </div>

                    <div>
                        <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទទទួល' : 'Receipt Date' }}</label>
                        <input type="date" name="receipt_date" value="{{ old('receipt_date', date('Y-m-d')) }}" required class="form-input font-mono">
                    </div>

                    <div>
                        <label class="form-label">{{ app()->getLocale() == 'km' ? 'ប័ណ្ណបញ្ជាទិញ' : 'Purchase Order' }}</label>
                        <input type="text" value="{{ $selectedPo->po_number }}" disabled
                               class="form-input bg-slate-100 font-mono font-bold text-slate-700 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="form-label">{{ app()->getLocale() == 'km' ? 'ឃ្លាំងទិសដៅ' : 'Destination Warehouse' }}</label>
                        <input type="text" value="{{ $selectedPo->warehouse_name }}" disabled
                               class="form-input bg-slate-100 font-bold text-slate-700 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="form-label">{{ app()->getLocale() == 'km' ? 'ទីតាំងផ្ទុកជាក់លាក់' : 'Storage Location' }}</label>
                        <select name="location_id" class="form-input">
                            <option value="">{{ app()->getLocale() == 'km' ? 'កន្លែងផ្ទុកចម្បង (Main Storage)' : 'Main Storage' }}</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="form-label">{{ app()->getLocale() == 'km' ? 'សម្គាល់ការទទួល' : 'Receiving Remarks' }}</label>
                        <input type="text" name="note" value="{{ old('note') }}" placeholder="{{ app()->getLocale() == 'km' ? 'ស្ថានភាពទំនិញពេលមកដល់ លេខវិក្កយបត្រអ្នកដឹក...' : 'Condition upon arrival, delivery note #...' }}" class="form-input">
                    </div>
                </div>
            </div>

            <!-- Items Table with Partial Receiving validation -->
            <div class="space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                    <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ទំនិញដែលនៅសល់ក្នុង PO ត្រូវទទួល' : 'Outstanding PO Items to Receive' }}</h2>
                        <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ពិនិត្យបរិមាណដែលនៅសល់ និងបញ្ចូលបរិមាណទទួលលើកនេះ' : 'Inspect outstanding quantities and input incoming quantities, batch and expiry' }}</p>
                    </div>
                </div>

                <div class="glass-card overflow-hidden border border-slate-200/80 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="data-table w-full text-left">
                            <thead>
                                <tr>
                                    <th>{{ app()->getLocale() == 'km' ? 'មុខទំនិញ & កូដ' : 'Item & SKU' }}</th>
                                    <th class="text-right w-24">{{ app()->getLocale() == 'km' ? 'សរុប PO' : 'PO Total' }}</th>
                                    <th class="text-right w-24">{{ app()->getLocale() == 'km' ? 'បានទទួលរួច' : 'Already Recv' }}</th>
                                    <th class="text-right w-28">{{ app()->getLocale() == 'km' ? 'នៅខ្វះ' : 'Outstanding' }}</th>
                                    <th class="text-right w-36">{{ app()->getLocale() == 'km' ? 'ទទួលលើកនេះ' : 'Receiving Now' }} <span class="text-rose-500">*</span></th>
                                    <th class="w-32">{{ app()->getLocale() == 'km' ? 'លេខឡូតិ៍ (Batch)' : 'Batch #' }}</th>
                                    <th class="w-36">{{ app()->getLocale() == 'km' ? 'ផុតកំណត់' : 'Expiry Date' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($poItems as $idx => $pi)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="p-3">
                                            <input type="hidden" name="items[{{ $idx }}][po_item_id]" value="{{ $pi->id }}">
                                            <span class="font-bold text-slate-800 block text-xs">{{ $pi->name_en }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $pi->item_code }}</span>
                                        </td>
                                        <td class="p-3 text-right font-semibold text-slate-700 font-mono text-xs">
                                            {{ number_format($pi->quantity, 0) }}
                                        </td>
                                        <td class="p-3 text-right text-slate-500 font-mono text-xs">
                                            {{ number_format($pi->received_quantity, 0) }}
                                        </td>
                                        <td class="p-3 text-right font-black text-amber-600 font-mono text-xs">
                                            {{ number_format($pi->outstanding_quantity, 0) }}
                                        </td>
                                        <td class="p-3 text-right">
                                            <input type="number" step="0.01" min="0" max="{{ $pi->outstanding_quantity }}"
                                                   name="items[{{ $idx }}][quantity_received]"
                                                   value="{{ old('items.'.$idx.'.quantity_received', $pi->outstanding_quantity) }}"
                                                   class="form-input text-xs py-1.5 text-right font-bold font-mono !border-emerald-300 focus:!border-emerald-500">
                                        </td>
                                        <td class="p-3">
                                            <input type="text" name="items[{{ $idx }}][batch_number]" placeholder="Batch #"
                                                   class="form-input text-xs py-1.5 font-mono">
                                        </td>
                                        <td class="p-3">
                                            <input type="date" name="items[{{ $idx }}][expiry_date]"
                                                   class="form-input text-xs py-1.5 font-mono">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
                <a href="{{ route('goods-receipts.index') }}" class="btn-secondary">
                    {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
                </a>
                <button type="submit" class="btn-primary !bg-emerald-600 hover:!bg-emerald-700 shadow-md shadow-emerald-500/20">
                    <i class="bi bi-check2-circle mr-1.5 text-sm"></i>
                    {{ app()->getLocale() == 'km' ? 'បញ្ជាក់ការទទួលទំនិញចូល' : 'Confirm Goods Receipt' }}
                </button>
            </div>
        </form>
    @endif
</div>
@endsection
