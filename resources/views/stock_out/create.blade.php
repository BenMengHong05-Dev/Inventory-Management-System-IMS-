@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'បញ្ចេញទំនិញពីស្តុក' : 'New Stock Out')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="stockOutForm()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'បញ្ចេញទំនិញពីស្តុក' : 'New Stock Out (Issue Items)' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កត់ត្រាការបញ្ចេញទំនិញ ឬសម្ភារប្រើប្រាស់ជូននាយកដ្ឋាន និងបុគ្គលិកដោយកាត់ស្តុកស្វ័យប្រវត្តិ' : 'Issue items or consumables from warehouse with automated stock deduction' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stock-out.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ទៅបញ្ជី' : 'Back to List' }}
            </a>
        </div>
    </div>

    <!-- Form Card (Modern Clear Glass) -->
    <form action="{{ route('stock-out.store') }}" method="POST" class="glass-card p-6 md:p-8 space-y-8">
        @csrf

        <!-- Section 1: Stock Out Details -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មាននៃការបញ្ចេញ' : 'Requisition & Destination' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសឃ្លាំងប្រភព នាយកដ្ឋានទទួល និងបុគ្គលិកស្នើសុំ' : 'Source warehouse, receiving department, and recipient employee' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខប័ណ្ណបញ្ចេញ #' : 'Stock Out #' }}</label>
                    <input type="text" name="stock_out_number" value="{{ old('stock_out_number', $nextNumber) }}" required
                           class="form-input font-mono uppercase" placeholder="OUT-00001">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឃ្លាំងប្រភព' : 'Source Warehouse' }}</label>
                    <select name="warehouse_id" required class="form-input">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ t('messages.department') }}</label>
                    <select name="department_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'ទូទៅ' : 'General Requisition' }}</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ localized_name($d, 'name') }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'បុគ្គលិកទទួល' : 'Recipient Employee' }}</label>
                    <select name="employee_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសបុគ្គលិក' : 'Select Employee' }}</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>{{ localized_name($e, 'name') }} ({{ $e->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទចេញ' : 'Issue Date' }}</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="form-input">
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'គោលបំណង / មូលហេតុ' : 'Purpose / Reason' }}</label>
                    <input type="text" name="purpose" value="{{ old('purpose') }}" placeholder="e.g. Monthly office supplies, Project deployment..." class="form-input">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កំណត់ចំណាំ' : 'Notes' }}</label>
                    <input type="text" name="note" value="{{ old('note') }}" placeholder="Additional notes..." class="form-input">
                </div>
            </div>
        </div>

        <!-- Section 2: Items to Issue (Dynamic Line Items) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        <i class="bi bi-list-check"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'មុខទំនិញបញ្ចេញ' : 'Items to Issue' }}</h2>
                        <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ និងបញ្ជាក់ចំនួនដែលត្រូវកាត់ចេញពីស្តុក' : 'Select catalog products and quantities to deduct from inventory' }}</p>
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
                                <th class="w-1/2">{{ t('messages.items') }} <span class="text-rose-500">*</span></th>
                                <th class="w-32">{{ app()->getLocale() == 'km' ? 'ចំនួន' : 'Quantity' }} <span class="text-rose-500">*</span></th>
                                <th class="w-32">{{ app()->getLocale() == 'km' ? 'តម្លៃដើម ($)' : 'Unit Cost ($)' }}</th>
                                <th class="w-32 text-right">{{ app()->getLocale() == 'km' ? 'តម្លៃសរុប' : 'Line Value' }}</th>
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
                                        <input type="number" step="0.01" min="0" :name="'items['+idx+'][unit_cost]'" x-model="row.unit_cost" class="form-input text-xs font-mono">
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-900 font-mono text-xs" x-text="'$ ' + (row.qty * row.unit_cost).toFixed(2)"></td>
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
                                <td colspan="3" class="py-3 px-4 text-right font-bold text-slate-700">{{ app()->getLocale() == 'km' ? 'តម្លៃសរុបដែលបានចេញ:' : 'Total Issued Value:' }}</td>
                                <td class="py-3 px-4 text-right text-slate-900 font-black text-sm font-mono" x-text="'$ ' + grandTotal.toFixed(2)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Submit Footer -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('stock-out.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-check2-circle mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'បញ្ជាក់ និងបញ្ចេញទំនិញ' : 'Confirm & Issue Stock' }}
            </button>
        </div>
    </form>
</div>

<script>
function stockOutForm() {
    return {
        rows: [
            { item_id: '', qty: 1, unit_cost: 0.00 }
        ],
        addItem() {
            this.rows.push({ item_id: '', qty: 1, unit_cost: 0.00 });
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
