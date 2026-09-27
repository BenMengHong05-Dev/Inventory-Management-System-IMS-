@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'ផ្ទេរទំនិញរវាងឃ្លាំង' : 'New Stock Transfer Request')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="transferForm()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'ផ្ទេរទំនិញរវាងឃ្លាំង' : 'New Stock Transfer Request' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'ផ្ទេរទំនិញពីឃ្លាំងដើមទៅកាន់ឃ្លាំងគោលដៅ និងតាមដានស្ថានភាពដឹកជញ្ជូន' : 'Initiate stock relocation between warehouses with in-transit dispatch tracking' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stock-transfers.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ទៅបញ្ជី' : 'Back to List' }}
            </a>
        </div>
    </div>

    <!-- Form Card (Modern Clear Glass) -->
    <form action="{{ route('stock-transfers.store') }}" method="POST" class="glass-card p-6 md:p-8 space-y-8">
        @csrf

        <!-- Section 1: Transfer Header Details -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានលម្អិតនៃការផ្ទេរ' : 'Transfer Details' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'កំណត់ឃ្លាំងប្រភព ឃ្លាំងគោលដៅ និងកាលបរិច្ឆេទ' : 'Select origin and destination warehouses and scheduled transfer date' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'លេខប័ណ្ណផ្ទេរ #' : 'Transfer #' }}</label>
                    <input type="text" name="transfer_number" value="{{ old('transfer_number', $nextNumber) }}" required
                           class="form-input font-mono uppercase" placeholder="TRF-00001">
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ពីឃ្លាំង (ប្រភព)' : 'From Warehouse (Origin)' }}</label>
                    <select name="from_warehouse_id" required class="form-input">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('from_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ទៅកាន់ឃ្លាំង (គោលដៅ)' : 'To Warehouse (Destination)' }}</label>
                    <select name="to_warehouse_id" required class="form-input">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ (old('to_warehouse_id') ?? ($warehouses[1]->id ?? '')) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទផ្ទេរ' : 'Transfer Date' }}</label>
                    <input type="date" name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" required class="form-input">
                </div>

                <div class="sm:col-span-4">
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'មូលហេតុផ្ទេរ / កំណត់ចំណាំ' : 'Transfer Reason / Notes' }}</label>
                    <input type="text" name="note" value="{{ old('note') }}" placeholder="Branch replenishment, store transfer..." class="form-input">
                </div>
            </div>
        </div>

        <!-- Section 2: Items to Transfer -->
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'មុខទំនិញដែលត្រូវផ្ទេរ' : 'Items to Transfer' }}</h2>
                        <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ និងបញ្ជាក់បរិមាណ' : 'Specify catalog products and relocation quantities' }}</p>
                    </div>
                </div>

                <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 text-emerald-700 hover:bg-emerald-500/20 border border-emerald-500/20 rounded-xl text-xs font-semibold transition">
                    <i class="bi bi-plus-lg"></i>
                    <span>{{ app()->getLocale() == 'km' ? 'បន្ថែមជួរទំនិញ' : 'Add Item' }}</span>
                </button>
            </div>

            <!-- Table Card for Line Items -->
            <div class="glass-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="data-table w-full">
                        <thead>
                            <tr>
                                <th class="w-3/4">{{ t('messages.items') }} <span class="text-rose-500">*</span></th>
                                <th class="w-1/4">{{ app()->getLocale() == 'km' ? 'ចំនួនត្រូវផ្ទេរ' : 'Quantity to Transfer' }} <span class="text-rose-500">*</span></th>
                                <th class="w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/80">
                            <template x-for="(row, idx) in rows" :key="idx">
                                <tr class="hover:bg-white/60 transition">
                                    <td class="py-2.5 px-3">
                                        <select :name="'items['+idx+'][item_id]'" x-model="row.item_id" required class="form-input text-xs">
                                            <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ...' : 'Select Item...' }}</option>
                                            @foreach($items as $it)
                                                <option value="{{ $it->id }}">
                                                    [{{ $it->code }}] {{ localized_name($it, 'name') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <input type="number" step="0.01" min="0.01" :name="'items['+idx+'][quantity]'" x-model="row.qty" required class="form-input text-xs font-mono">
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" @click="removeItem(idx)" x-show="rows.length > 1" class="btn-action btn-action-delete !w-7 !h-7" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Submit Footer -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('stock-transfers.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-check2-circle mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'បង្កើតការផ្ទេរស្តុក' : 'Create Transfer' }}
            </button>
        </div>
    </form>
</div>

<script>
function transferForm() {
    return {
        rows: [
            { item_id: '', qty: 1 }
        ],
        addItem() {
            this.rows.push({ item_id: '', qty: 1 });
        },
        removeItem(index) {
            this.rows.splice(index, 1);
        }
    }
}
</script>
@endsection
