@extends('layouts.app')

@section('title', app()->getLocale() == 'km' ? 'បន្ថែមអ្នកផ្គត់ផ្គង់ថ្មី - IMS' : 'Add Supplier - IMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 font-mono">
                    {{ app()->getLocale() == 'km' ? 'ដៃគូផ្គត់ផ្គង់' : 'Supplier Management' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                {{ app()->getLocale() == 'km' ? 'បន្ថែមអ្នកផ្គត់ផ្គង់ថ្មី' : 'Add New Supplier' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ app()->getLocale() == 'km' ? 'ចុះឈ្មោះក្រុមហ៊ុនផ្គត់ផ្គង់ ឬដៃគូអាជីវកម្មក្រៅសម្រាប់បញ្ជាទិញទំនិញ' : 'Register a new vendor or external partner for procurement' }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('master.suppliers.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left mr-1.5"></i>
                {{ app()->getLocale() == 'km' ? 'ត្រឡប់ក្រោយ' : 'Back to Suppliers' }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('master.suppliers.store') }}" class="glass-card p-6 md:p-8 space-y-6">
        @csrf

        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200/70">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ app()->getLocale() == 'km' ? 'ព័ត៌មានក្រុមហ៊ុន និងទំនាក់ទំនង' : 'Company & Contact Info' }}</h2>
                    <p class="text-[11px] text-slate-400">{{ app()->getLocale() == 'km' ? 'ឈ្មោះក្រុមហ៊ុន កូដសម្គាល់ អ្នកតំណាង លេខទូរស័ព្ទ និងអាសយដ្ឋាន' : 'Company title, unique code, contact person, phone, and address' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ឈ្មោះអ្នកផ្គត់ផ្គង់ / ក្រុមហ៊ុន' : 'Supplier Name / Company' }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input font-medium" 
                           placeholder="{{ app()->getLocale() == 'km' ? 'ឧ. ក្រុមហ៊ុន អេស៊ីមេ ឯ.ក' : 'e.g. Acme Supplies Ltd' }}" required>
                </div>
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'កូដអ្នកផ្គត់ផ្គង់' : 'Supplier Code' }}</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="form-input font-mono uppercase" placeholder="SUP-001" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'បុគ្គលិកទំនាក់ទំនង' : 'Contact Person' }}</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name') }}" class="form-input font-medium" 
                           placeholder="{{ app()->getLocale() == 'km' ? 'ឈ្មោះអ្នកតំណាងលក់' : 'Account manager name' }}">
                </div>
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'អាសយដ្ឋានអ៊ីមែល' : 'Email Address' }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="supplier@example.com">
                </div>
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'លេខទូរស័ព្ទ' : 'Phone Number' }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-input font-mono" placeholder="+855 12 345 678">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'អាសយដ្ឋាន' : 'Address' }}</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="form-input font-medium" 
                           placeholder="{{ app()->getLocale() == 'km' ? 'ផ្ទះលេខ ផ្លូវ សង្កាត់ ខណ្ឌ' : 'Street, Building, Floor' }}">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="form-label">{{ app()->getLocale() == 'km' ? 'រាជធានី / ខេត្ត' : 'City' }}</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="form-input" placeholder="Phnom Penh">
                    </div>
                    <div>
                        <label class="form-label">{{ app()->getLocale() == 'km' ? 'ប្រទេស' : 'Country' }}</label>
                        <input type="text" name="country" value="{{ old('country', 'Cambodia') }}" class="form-input">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'លេខអត្តសញ្ញាណកម្មសារពើពន្ធ (VAT)' : 'Tax ID / VAT No' }}</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id') }}" class="form-input font-mono" placeholder="e.g. K001-90203040">
                </div>
                <div>
                    <label class="form-label">{{ app()->getLocale() == 'km' ? 'លក្ខខណ្ឌទូទាត់' : 'Payment Terms' }}</label>
                    <input type="text" name="payment_terms" value="{{ old('payment_terms', 'Net 30') }}" class="form-input" placeholder="e.g. Net 30, COD, Net 15">
                </div>
                <div>
                    <label class="form-label required">{{ app()->getLocale() == 'km' ? 'ស្ថានភាព' : 'Status' }}</label>
                    <select name="status" class="form-input font-medium" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'សកម្ម (Active)' : 'Active' }}</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'អសកម្ម (Inactive)' : 'Inactive' }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">{{ app()->getLocale() == 'km' ? 'កំណត់ចំណាំផ្សេងៗ' : 'Notes & Remarks' }}</label>
                <textarea name="notes" rows="3" class="form-input" 
                          placeholder="{{ app()->getLocale() == 'km' ? 'ព័ត៌មានគណនីធនាគារ ប្រភេទផលិតផលផ្គត់ផ្គង់ ឬការធានាដឹកជញ្ជូន...' : 'Bank details, supply categories, delivery guarantees...' }}">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200/70">
            <a href="{{ route('master.suppliers.index') }}" class="btn-secondary">
                {{ app()->getLocale() == 'km' ? 'បោះបង់' : 'Cancel' }}
            </a>
            <button type="submit" class="btn-primary !bg-emerald-600 hover:!bg-emerald-700 shadow-md shadow-emerald-500/20">
                <i class="bi bi-check-lg mr-1.5 text-sm"></i>
                {{ app()->getLocale() == 'km' ? 'រក្សាទុកអ្នកផ្គត់ផ្គង់' : 'Save Supplier' }}
            </button>
        </div>
    </form>
</div>
@endsection
