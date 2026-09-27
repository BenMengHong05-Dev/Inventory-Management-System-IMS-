@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'កត់ត្រាទំនិញចូលស្តុក' : 'New Stock In')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="stockInForm()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'កត់ត្រាទំនិញចូលស្តុក' : 'New Stock In (Receive Goods)' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កត់ត្រាទំនិញចូលឃ្លាំង ព័ត៌មានអ្នកផ្គត់ផ្គង់ លេខបាច់ កាលបរិច្ឆេទផុតកំណត់ និងតម្លៃដើម' : 'Record incoming stock with supplier references, batch/expiry dates, and unit costs' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stock-in.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ទៅបញ្ជី' : 'Back to List' }}
            </a>
        </div>
    </div>

    <!-- Form Card (Modern Clear Glass) -->
    <form action="{{ route('stock-in.store') }}" method="POST" class="glass-card p-6 md:p-8 space-y-8">
        @csrf

        <!-- Section 1: Header Info -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានទូទៅនៃការទទួល' : 'Receipt Details & Destination' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'កំណត់លេខឯកសារ ឃ្លាំងគោលដៅ និងព័ត៌មានអ្នកផ្គត់ផ្គង់' : 'Document numbers, target storage warehouse, and supplier invoices' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខប័ណ្ណទំនិញចូល #' : 'Stock In #' }}</label>
                    <input type="text" name="stock_in_number" value="{{ old('stock_in_number', $nextNumber) }}" required
                           class="form-input font-mono uppercase" placeholder="IN-00001">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'លេខយោង / វិក្កយបត្រ' : 'Reference / Invoice #' }}</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                           class="form-input" placeholder="e.g. INV-98721">
                </div>

                <div>
                    <label class="form-label">{{ t('messages.suppliers') }}</label>
                    <select name="supplier_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'ផ្ទៃក្នុង / ស្តុកដើមគ្រា' : 'Direct / Opening Balance' }}</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទចូល' : 'Stock In Date' }}</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="form-input">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឃ្លាំងគោលដៅ' : 'Destination Warehouse' }}</label>
                    <select name="warehouse_id" required class="form-input">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'ទីតាំងក្នុងឃ្លាំង' : 'Warehouse Location' }}</label>
                    <select name="location_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'តំបន់ទូទៅ' : 'Main Storage' }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កំណត់ចំណាំ' : 'Notes / Remarks' }}</label>
                    <input type="text" name="note" value="{{ old('note') }}" placeholder="Receipt notes, delivery driver info..." class="form-input">
                </div>
            </div>
        </div>

        <!-- Section 2: Items Received (Dynamic Line Items) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        <i class="bi bi-list-check"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'មុខទំនិញទទួលចូល' : 'Items Received' }}</h2>
                        <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ បរិមាណ តម្លៃឯកតា និងព័ត៌មានបាច់' : 'Select items, quantities, costs, and batch details' }}</p>
                    </div>
                </div>

                <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 text-emerald-700 hover:bg-emerald-500/20 border border-emerald-500/20 rounded-xl text-xs font-semibold transition">
                    <i class="bi bi-plus-lg"></i>
                    <span>{{ app()->getLocale() == 'km' ? 'បន្ថែមជួរទំនិញ' : 'Add Item Row' }}</span>
                </button>
            </div>

            <!-- Table Card for Line Items -->
            <div class="glass-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="data-table w-full">
                        <thead>
                            <tr>
                                <th class="w-2/5">{{ t('messages.items') }} <span class="text-rose-500">*</span></th>
                                <th class="w-24">{{ app()->getLocale() == 'km' ? 'ចំនួន' : 'Qty' }} <span class="text-rose-500">*</span></th>
                                <th class="w-32">{{ app()->getLocale() == 'km' ? 'តម្លៃឯកតា ($)' : 'Unit Cost ($)' }} <span class="text-rose-500">*</span></th>
                                <th class="w-32 text-right">{{ app()->getLocale() == 'km' ? 'សរុបជួរ' : 'Line Total' }}</th>
                                <th class="w-28">{{ app()->getLocale() == 'km' ? 'លេខបាច់' : 'Batch #' }}</th>
                                <th class="w-32">{{ app()->getLocale() == 'km' ? 'ផុតកំណត់' : 'Expiry' }}</th>
                                <th class="w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/80">
                            <template x-for="(row, idx) in rows" :key="idx">
                                <tr class="hover:bg-white/60 transition">
                                    <td class="py-2.5 px-3">
                                        <select :name="'items['+idx+'][item_id]'" x-model="row.item_id" @change="onItemSelect(row)" required class="form-input text-xs">
                                            <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ...' : 'Select Item...' }}</option>
                                            @foreach($items as $it)
                                                <option value="{{ $it->id }}" data-cost="{{ $it->default_unit_cost }}" data-unit="{{ $it->unit_id }}">
                                                    [{{ $it->code }}] {{ localized_name($it, 'name') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <input type="number" step="0.01" min="0.01" :name="'items['+idx+'][quantity]'" x-model="row.qty" required class="form-input text-xs font-mono">
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <input type="number" step="0.01" min="0" :name="'items['+idx+'][unit_cost]'" x-model="row.unit_cost" required class="form-input text-xs font-mono">
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-900 font-mono text-xs" x-text="'$ ' + (row.qty * row.unit_cost).toFixed(2)"></td>
                                    <td class="py-2.5 px-3">
                                        <input type="text" :name="'items['+idx+'][batch_number]'" x-model="row.batch_number" placeholder="Batch" class="form-input text-xs font-mono">
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <input type="date" :name="'items['+idx+'][expiry_date]'" x-model="row.expiry_date" class="form-input text-xs">
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" @click="removeItem(idx)" x-show="rows.length > 1" class="btn-action btn-action-delete !w-7 !h-7" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="border-t border-slate-200/80 bg-slate-50/50 font-semibold text-xs">
                            <tr>
                                <td colspan="3" class="py-3 px-4 text-right font-bold text-slate-700">{{ app()->getLocale() == 'km' ? 'សរុបរួម:' : 'Grand Total:' }}</td>
                                <td class="py-3 px-4 text-right text-emerald-700 font-black text-sm font-mono" x-text="'$ ' + grandTotal.toFixed(2)"></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Submit Footer -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('stock-in.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-check2-circle mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'បញ្ជាក់ និងទទួលទំនិញចូល' : 'Confirm & Receive Stock' }}
            </button>
        </div>
    </form>
</div>

<script>
function stockInForm() {
    return {
        rows: [
            { item_id: '', qty: 1, unit_cost: 0.00, batch_number: '', expiry_date: '' }
        ],
        addItem() {
            this.rows.push({ item_id: '', qty: 1, unit_cost: 0.00, batch_number: '', expiry_date: '' });
        },
        removeItem(index) {
            this.rows.splice(index, 1);
        },
        onItemSelect(row) {
            const selectEl = event.target;
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.cost) {
                row.unit_cost = parseFloat(selectedOpt.dataset.cost) || 0.00;
            }
        },
        get grandTotal() {
            return this.rows.reduce((sum, r) => sum + ((parseFloat(r.qty) || 0) * (parseFloat(r.unit_cost) || 0)), 0);
        }
    }
}
</script>
@endsection
