@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'បង្កើតការបញ្ជាទិញ (PO)' : 'Create Purchase Order')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="poForm()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'ផ្នែកលទ្ធកម្ម' : 'Procurement Module' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ app()->getLocale() == 'km' ? 'បង្កើតការបញ្ជាទិញ (PO)' : 'Create Purchase Order (PO)' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'ចេញប័ណ្ណបញ្ជាទិញផ្លូវការទៅកាន់អ្នកផ្គត់ផ្គង់ រួមមានតម្លៃ ពន្ធ និងកាលបរិច្ឆេទដឹកជញ្ជូន' : 'Issue official purchase order to vendor with pricing, taxes, and delivery schedules' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('purchase-orders.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to POs' }}
            </a>
        </div>
    </div>

    <form action="{{ route('purchase-orders.store') }}" method="POST" class="glass-card p-6 md:p-8 space-y-8">
        @csrf

        @if($selectedPr)
            <input type="hidden" name="purchase_request_id" value="{{ $selectedPr->id }}">
            <div class="p-4 rounded-2xl bg-sky-50/80 border border-sky-200/70 text-sky-900 text-xs flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-sky-500/20 text-sky-600 flex items-center justify-center font-bold">
                        <i class="bi bi-info-circle-fill text-base"></i>
                    </div>
                    <div>
                        <span class="font-semibold">{{ app()->getLocale() == 'km' ? 'ទិន្នន័យបានទាញយកដោយស្វ័យប្រវត្តិចេញពីសំណើទិញ' : 'Pre-populated from Purchase Request' }}:</span>
                        <span class="font-bold font-mono text-sky-700 bg-sky-100/70 px-2 py-0.5 rounded-lg ml-1">{{ $selectedPr->request_number }}</span>
                    </div>
                </div>
                <span class="badge badge-info">{{ $selectedPr->status ?? 'Approved' }}</span>
            </div>
        @endif

        <!-- Section 1: Order Information -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានប័ណ្ណបញ្ជាទិញ' : 'Order Information' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'កំណត់ព័ត៌មានលម្អិតនៃប័ណ្ណបញ្ជាទិញ និងអ្នកផ្គត់ផ្គង់' : 'Specify PO number, vendor, warehouse, and timeline' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខកូដ PO' : 'PO #' }}</label>
                    <input type="text" name="po_number" value="{{ old('po_number', $nextNumber) }}" required
                           class="form-input font-mono uppercase font-bold text-emerald-700">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'អ្នកផ្គត់ផ្គង់' : 'Vendor / Supplier' }}</label>
                    <select name="supplier_id" required class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសអ្នកផ្គត់ផ្គង់ --' : 'Select Supplier' }}</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឃ្លាំងទទួល' : 'Receiving Warehouse' }}</label>
                    <select name="warehouse_id" required class="form-input">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទបញ្ជាទិញ' : 'Order Date' }}</label>
                    <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទរំពឹងទុក' : 'Expected Delivery' }}</label>
                    <input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'លក្ខខណ្ឌទូទាត់' : 'Payment Terms' }}</label>
                    <input type="text" name="payment_terms" value="{{ old('payment_terms', 'Net 30 Days') }}" placeholder="e.g. Net 30, COD, 50% Advance" class="form-input">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កំណត់សម្គាល់ / សេចក្ដីណែនាំ' : 'Notes / Instructions' }}</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="{{ app()->getLocale() == 'km' ? 'ចំណាំពីការដឹកជញ្ជូន ឬលេខទំនាក់ទំនង...' : 'Delivery instructions, contact details...' }}" class="form-input">
                </div>
            </div>
        </div>

        <!-- Section 2: Line Items -->
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                        <i class="bi bi-cart3"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'មុខទំនិញបញ្ជាទិញ' : 'Order Line Items' }}</h2>
                        <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ បរិមាណ តម្លៃឯកតា ការបញ្ចុះតម្លៃ និងពន្ធ' : 'Items, requested quantities, rates, discounts, and taxes' }}</p>
                    </div>
                </div>
                <button type="button" @click="addItem()" class="btn-secondary !text-xs !py-1.5 !px-3 font-semibold text-emerald-700 hover:bg-emerald-50 border-emerald-200">
                    <i class="bi bi-plus-circle mr-1 text-emerald-600"></i>
                    {{ app()->getLocale() == 'km' ? 'បន្ថែមជួរ' : 'Add Line' }}
                </button>
            </div>

            <div class="glass-card overflow-hidden border border-slate-200/80 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="data-table w-full text-left">
                        <thead>
                            <tr>
                                <th class="w-2/5">{{ app()->getLocale() == 'km' ? 'មុខទំនិញ' : 'Item' }} <span class="text-rose-500">*</span></th>
                                <th class="w-24 text-center">{{ app()->getLocale() == 'km' ? 'បរិមាណ' : 'Qty' }} <span class="text-rose-500">*</span></th>
                                <th class="w-28 text-right">{{ app()->getLocale() == 'km' ? 'តម្លៃឯកតា ($)' : 'Unit Price ($)' }} <span class="text-rose-500">*</span></th>
                                <th class="w-24 text-right">{{ app()->getLocale() == 'km' ? 'បញ្ចុះតម្លៃ ($)' : 'Discount ($)' }}</th>
                                <th class="w-24 text-right">{{ app()->getLocale() == 'km' ? 'ពន្ធ ($)' : 'Tax ($)' }}</th>
                                <th class="w-28 text-right">{{ app()->getLocale() == 'km' ? 'សរុបបន្ទាត់' : 'Line Total' }}</th>
                                <th class="w-12 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="(row, idx) in rows" :key="idx">
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="p-2.5">
                                        <select :name="'items['+idx+'][item_id]'" x-model="row.item_id" @change="onItemSelect(row)" required
                                                class="form-input text-xs py-1.5">
                                            <option value="">{{ app()->getLocale() == 'km' ? '-- ជ្រើសរើសទំនិញ --' : 'Select Item' }}</option>
                                            @foreach($items as $it)
                                                <option value="{{ $it->id }}" data-cost="{{ $it->default_unit_cost }}" data-unit="{{ $it->unit_id }}">
                                                    [{{ $it->code }}] {{ $it->name_en }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-2.5">
                                        <input type="number" step="0.01" min="0.01" :name="'items['+idx+'][quantity]'" x-model="row.qty" required
                                               class="form-input text-xs py-1.5 text-center font-bold font-mono">
                                    </td>
                                    <td class="p-2.5">
                                        <input type="number" step="0.01" min="0" :name="'items['+idx+'][unit_price]'" x-model="row.price" required
                                               class="form-input text-xs py-1.5 text-right font-mono">
                                    </td>
                                    <td class="p-2.5">
                                        <input type="number" step="0.01" min="0" :name="'items['+idx+'][discount]'" x-model="row.discount"
                                               class="form-input text-xs py-1.5 text-right font-mono">
                                    </td>
                                    <td class="p-2.5">
                                        <input type="number" step="0.01" min="0" :name="'items['+idx+'][tax]'" x-model="row.tax"
                                               class="form-input text-xs py-1.5 text-right font-mono">
                                    </td>
                                    <td class="p-2.5 text-right font-bold text-slate-800 font-mono text-xs"
                                        x-text="'$ ' + ((row.qty * row.price - (parseFloat(row.discount) || 0)) + (parseFloat(row.tax) || 0)).toFixed(2)">
                                    </td>
                                    <td class="p-2.5 text-center">
                                        <button type="button" @click="removeItem(idx)" x-show="rows.length > 1"
                                                class="btn-action btn-action-delete" title="Remove line">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50/80 border-t-2 border-slate-200">
                                <td colspan="5" class="p-3 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() == 'km' ? 'តម្លៃសរុបរួម (Grand Total):' : 'Grand Total:' }}
                                </td>
                                <td class="p-3 text-right font-mono font-black text-sm text-emerald-600" x-text="'$ ' + grandTotal.toFixed(2)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('purchase-orders.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary !bg-emerald-600 hover:!bg-emerald-700 shadow-md shadow-emerald-500/20">
                <i class="bi bi-check2-circle mr-1.5 text-sm"></i>
                {{ app()->getLocale() == 'km' ? 'បង្កើត និងចេញប័ណ្ណបញ្ជាទិញ' : 'Create & Issue PO' }}
            </button>
        </div>
    </form>
</div>

<script>
function poForm() {
    return {
        rows: [
            @if(!empty($prItems))
                @foreach($prItems as $pi)
                    { item_id: '{{ $pi->item_id }}', qty: {{ $pi->quantity }}, price: {{ $pi->estimated_unit_price }}, discount: 0, tax: 0 },
                @endforeach
            @else
                { item_id: '', qty: 1, price: 0.00, discount: 0, tax: 0 }
            @endif
        ],
        addItem() {
            this.rows.push({ item_id: '', qty: 1, price: 0.00, discount: 0, tax: 0 });
        },
        removeItem(index) {
            this.rows.splice(index, 1);
        },
        onItemSelect(row) {
            const selectEl = event.target;
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.cost) {
                row.price = parseFloat(selectedOpt.dataset.cost) || 0.00;
            }
        },
        get grandTotal() {
            return this.rows.reduce((sum, r) => {
                const sub = (parseFloat(r.qty) || 0) * (parseFloat(r.price) || 0);
                const disc = parseFloat(r.discount) || 0;
                const tax = parseFloat(r.tax) || 0;
                return sum + ((sub - disc) + tax);
            }, 0);
        }
    }
}
</script>
@endsection
