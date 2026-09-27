@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'កត់ត្រាការប្រគល់ទំនិញត្រឡប់' : 'Process Item Return')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() == 'km' ? 'កត់ត្រាការប្រគល់ទំនិញត្រឡប់' : 'Process Item Return' }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'កត់ត្រាទំនិញប្រគល់ត្រឡប់ទៅកាន់អ្នកផ្គត់ផ្គង់ ឬការប្រគល់ត្រឡប់ចូលស្តុកវិញពីបុគ្គលិក' : 'Record goods returned to a supplier or returned back by an employee' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('inventory.returns.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ទៅបញ្ជី' : 'Back to Returns' }}
            </a>
        </div>
    </div>

    <!-- Form Card (Modern Clear Glass) -->
    <form method="POST" action="{{ route('inventory.returns.store') }}" class="glass-card p-6 md:p-8 space-y-8">
        @csrf

        <!-- Section 1: Return Header Details -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-arrow-return-left"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មាននៃការប្រគល់ត្រឡប់' : 'Return Scope & Warehouse' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'កំណត់ប្រភេទនៃការប្រគល់ កាលបរិច្ឆេទ និងឃ្លាំងទទួល' : 'Return flow type, transaction date, and target warehouse' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ប្រភេទនៃការប្រគល់' : 'Return Type' }}</label>
                    <select name="return_type" id="return_type" class="form-input" required onchange="togglePartyFields()">
                        <option value="to_supplier" {{ old('return_type') == 'to_supplier' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ត្រឡប់ទៅអ្នកផ្គត់ផ្គង់ (ចេញ)' : 'Return to Supplier (Outflow)' }}</option>
                        <option value="from_employee" {{ old('return_type') == 'from_employee' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ត្រឡប់ពីបុគ្គលិក (ចូល)' : 'Return from Employee (Inflow)' }}</option>
                    </select>
                </div>

                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កាលបរិច្ឆេទ' : 'Return Date' }}</label>
                    <input type="date" name="return_date" value="{{ old('return_date', date('Y-m-d')) }}" class="form-input" required>
                </div>

                <div>
                    <label class="form-label required">{{ t('messages.warehouse') }}</label>
                    <select name="warehouse_id" class="form-input" required>
                        <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសឃ្លាំង...' : '-- Select Warehouse --' }}</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="supplier_group">
                    <label class="form-label required">{{ t('messages.suppliers') }}</label>
                    <select name="supplier_id" id="supplier_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសអ្នកផ្គត់ផ្គង់...' : '-- Choose Supplier --' }}</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="employee_group" style="display: none;">
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'បុគ្គលិក' : 'Employee' }}</label>
                    <select name="employee_id" id="employee_id" class="form-input">
                        <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសបុគ្គលិក...' : '-- Choose Employee --' }}</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->name }} ({{ $e->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'មូលហេតុនៃការប្រគល់ត្រឡប់' : 'Return Reason' }}</label>
                    <input type="text" name="reason" value="{{ old('reason') }}" class="form-input" placeholder="e.g. Defective batch, Wrong specification, Project completed">
                </div>
            </div>
        </div>

        <!-- Section 2: Items Table -->
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'មុខទំនិញដែលត្រូវប្រគល់' : 'Return Items' }}</h2>
                        <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ បរិមាណ និងមូលហេតុ' : 'Specify products and quantities' }}</p>
                    </div>
                </div>

                <button type="button" onclick="addRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 text-emerald-700 hover:bg-emerald-500/20 border border-emerald-500/20 rounded-xl text-xs font-semibold transition">
                    <i class="bi bi-plus-lg"></i>
                    <span>{{ app()->getLocale() == 'km' ? 'បន្ថែមជួរទំនិញ' : 'Add Item Line' }}</span>
                </button>
            </div>

            <!-- Table Card for Line Items -->
            <div class="glass-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="data-table w-full" id="items_table">
                        <thead>
                            <tr>
                                <th class="w-1/2">{{ t('messages.items') }} <span class="text-rose-500">*</span></th>
                                <th class="w-1/4">{{ app()->getLocale() == 'km' ? 'ចំនួន' : 'Quantity' }} <span class="text-rose-500">*</span></th>
                                <th class="w-1/4">{{ app()->getLocale() == 'km' ? 'មូលហេតុជួរ' : 'Line Reason' }}</th>
                                <th class="w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="items_body" class="divide-y divide-slate-100/80">
                            <tr class="hover:bg-white/60 transition">
                                <td class="py-2.5 px-3">
                                    <select name="items[0][item_id]" class="form-input text-xs" required>
                                        <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ...' : '-- Select Item --' }}</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}">{{ $item->sku }} - {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.01" min="0.01" name="items[0][quantity]" class="form-input text-xs font-mono" placeholder="Qty" required>
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="text" name="items[0][reason]" class="form-input text-xs" placeholder="Optional notes">
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" onclick="removeRow(this)" class="btn-action btn-action-delete !w-7 !h-7" title="Remove">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <label class="form-label">{{ app()->getLocale() == 'km' ? 'កំណត់ចំណាំទូទៅបន្ថែម' : 'Additional General Notes' }}</label>
            <textarea name="notes" rows="2" class="form-input" placeholder="Any shipping notes, carrier tracking or inspection logs...">{{ old('notes') }}</textarea>
        </div>

        <!-- Submit Footer -->
        <div class="pt-5 border-t border-slate-200/70 flex items-center justify-end space-x-3">
            <a href="{{ route('inventory.returns.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-check2-circle mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'កត់ត្រាការប្រគល់ត្រឡប់' : 'Submit Return' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let rowCount = 1;
function togglePartyFields() {
    const type = document.getElementById('return_type').value;
    const sGroup = document.getElementById('supplier_group');
    const eGroup = document.getElementById('employee_group');
    if (type === 'to_supplier') {
        sGroup.style.display = 'block';
        eGroup.style.display = 'none';
    } else {
        sGroup.style.display = 'none';
        eGroup.style.display = 'block';
    }
}
togglePartyFields();

function addRow() {
    const tbody = document.getElementById('items_body');
    const row = document.createElement('tr');
    row.className = "hover:bg-white/60 transition";
    row.innerHTML = `
        <td class="py-2.5 px-3">
            <select name="items[${rowCount}][item_id]" class="form-input text-xs" required>
                <option value="">{{ app()->getLocale() == 'km' ? 'ជ្រើសរើសទំនិញ...' : '-- Select Item --' }}</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->sku }} - {{ $item->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="py-2.5 px-3">
            <input type="number" step="0.01" min="0.01" name="items[${rowCount}][quantity]" class="form-input text-xs font-mono" placeholder="Qty" required>
        </td>
        <td class="py-2.5 px-3">
            <input type="text" name="items[${rowCount}][reason]" class="form-input text-xs" placeholder="Optional notes">
        </td>
        <td class="py-2.5 px-3 text-center">
            <button type="button" onclick="removeRow(this)" class="btn-action btn-action-delete !w-7 !h-7" title="Remove">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    rowCount++;
}

function removeRow(btn) {
    const tbody = document.getElementById('items_body');
    if (tbody.children.length > 1) {
        btn.closest('tr').remove();
    } else {
        alert('At least one item line is required.');
    }
}
</script>
@endpush
@endsection
