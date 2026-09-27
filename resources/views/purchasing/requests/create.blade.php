@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'បង្កើតសំណើទិញថ្មី' : 'New Purchase Request')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="prForm()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'បង្កើតសំណើទិញថ្មី' : 'New Purchase Request (PR)' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'ស្នើសុំការទិញសម្ភារ បរិក្ខារ ឬទំនិញសម្រាប់នាយកដ្ឋាន' : 'Submit an internal procurement requisition for management review' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('purchase-requests.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ទៅសំណើទិញ' : 'Back to Requests' }}
            </a>
        </div>
    </div>

    <!-- Form Card (Modern Clear Glass) -->
    <form action="{{ route('purchase-requests.store') }}" method="POST" class="glass-card p-6 md:p-8 space-y-8">
        @csrf

        <!-- Section 1: PR Header Details -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានលម្អិតនៃសំណើ' : 'Requisition Details' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'លេខកូដសំណើ នាយកដ្ឋាន កាលបរិច្ឆេទ និងកម្រិតអាទិភាព' : 'Request code, department, dates, and urgency level' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខសំណើទិញ #' : 'Request #' }}</label>
                    <input type="text" name="request_number" value="{{ old('request_number', $nextNumber) }}" required
                           class="form-input font-mono uppercase" placeholder="PR-00001">
                </div>

                <div>
                    <label class="form-label">{{ t('messages.department') }}</label>
                    <select name="department_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'នាយកដ្ឋានទូទៅ' : 'General Department' }}</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ localized_name($d, 'name') }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទស្នើ' : 'Request Date' }}</label>
                    <input type="date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" required class="form-input">
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទត្រូវការទំនិញ' : 'Required By Date' }}</label>
                    <input type="date" name="required_date" value="{{ old('required_date') }}" class="form-input">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កម្រិតអាទិភាព' : 'Priority Level' }}</label>
                    <select name="priority" required class="form-input">
                        <option value="LOW" {{ old('priority') === 'LOW' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ទាប (Low)' : 'Low' }}</option>
                        <option value="MEDIUM" {{ old('priority', 'MEDIUM') === 'MEDIUM' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'មធ្យម (Medium)' : 'Medium' }}</option>
                        <option value="HIGH" {{ old('priority') === 'HIGH' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ខ្ពស់ (High)' : 'High' }}</option>
                        <option value="URGENT" {{ old('priority') === 'URGENT' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បន្ទាន់បំផុត (Urgent)' : 'Urgent' }}</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'គោលបំណង / ការបញ្ជាក់ពីតម្រូវការ' : 'Purpose / Justification' }}</label>
                    <input type="text" name="purpose" value="{{ old('purpose') }}" placeholder="e.g. Office expansion, IT infrastructure replacement..." class="form-input">
                </div>
            </div>
        </div>

        <!-- Section 2: Requested Items (Dynamic Line Items) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        <i class="bi bi-cart-plus"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'មុខទំនិញដែលបានស្នើសុំ' : 'Requested Items' }}</h2>
                        <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ បរិមាណ និងតម្លៃប៉ាន់ស្មាន' : 'Item quantities and estimated pricing' }}</p>
                    </div>
                </div>

                <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 text-emerald-700 hover:bg-emerald-500/20 border border-emerald-500/20 rounded-xl text-xs font-semibold transition">
                    <i class="bi bi-plus-lg"></i>
                    <span>{{ app()->getLocale() == 'km' ? 'បន្ថែមទំនិញ' : 'Add Item' }}</span>
                </button>
            </div>

            <!-- Table Card for Line Items -->
            <div class="glass-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="data-table w-full">
                        <thead>
                            <tr>
                                <th class="w-1/2">{{ t('messages.items') }} <span class="text-rose-500">*</span></th>
                                <th class="w-28">{{ app()->getLocale() == 'km' ? 'ចំនួន' : 'Quantity' }} <span class="text-rose-500">*</span></th>
                                <th class="w-32">{{ app()->getLocale() == 'km' ? 'តម្លៃប៉ាន់ស្មាន ($)' : 'Est. Unit Price ($)' }}</th>
                                <th class="w-32 text-right">{{ app()->getLocale() == 'km' ? 'សរុបជួរ' : 'Line Total' }}</th>
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
                                        <input type="number" step="0.01" min="0" :name="'items['+idx+'][estimated_unit_price]'" x-model="row.price" class="form-input text-xs font-mono">
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-900 font-mono text-xs" x-text="'$ ' + (row.qty * row.price).toFixed(2)"></td>
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
                                <td colspan="3" class="py-3 px-4 text-right font-bold text-slate-700">{{ app()->getLocale() == 'km' ? 'តម្លៃប៉ាន់ស្មានសរុប:' : 'Estimated Total:' }}</td>
                                <td class="py-3 px-4 text-right text-emerald-700 font-black text-sm font-mono" x-text="'$ ' + grandTotal.toFixed(2)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Submit Footer -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('purchase-requests.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-send mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ដាក់ស្នើសំណើទិញ' : 'Submit Purchase Request' }}
            </button>
        </div>
    </form>
</div>

<script>
function prForm() {
    return {
        rows: [
            { item_id: '', qty: 1, price: 0.00 }
        ],
        addItem() {
            this.rows.push({ item_id: '', qty: 1, price: 0.00 });
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
            return this.rows.reduce((sum, r) => sum + ((parseFloat(r.qty) || 0) * (parseFloat(r.price) || 0)), 0);
        }
    }
}
</script>
@endsection
