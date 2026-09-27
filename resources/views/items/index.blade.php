@extends('layouts.app')

@section('title', t('messages.items'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.items') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'គ្រប់គ្រងទំនិញ ស្តុក បាកូដ និងតម្លៃឯកតា' : 'Manage product SKUs, stock levels, barcodes, and pricing' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('items.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.create') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form action="{{ route('items.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search }}" placeholder="{{ t('messages.search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-44">
                <x-select name="category_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ localized_name($cat, 'name') }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-44">
                <x-select name="brand_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.brands') }}</option>
                    @foreach($brands as $brd)
                        <option value="{{ $brd->id }}" {{ $brandId == $brd->id ? 'selected' : '' }}>{{ $brd->name }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-40">
                <x-select name="item_type" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.type') }}</option>
                    <option value="CONSUMABLE" {{ $itemType === 'CONSUMABLE' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ប្រើប្រាស់អស់' : 'Consumable' }}</option>
                    <option value="NON_CONSUMABLE" {{ $itemType === 'NON_CONSUMABLE' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'មិនប្រើប្រាស់អស់' : 'Non-Consumable' }}</option>
                    <option value="ASSET" {{ $itemType === 'ASSET' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ទ្រព្យសកម្ម' : 'Fixed Asset' }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            <a href="{{ route('items.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table Card (Modern Clear Glass) -->
    <x-table :pagination="$items">
        <x-slot name="head">
            <th>{{ t('messages.items') }} & SKU</th>
            <th>{{ t('messages.categories') }} / {{ t('messages.brands') }}</th>
            <th>{{ t('messages.type') }}</th>
            <th>{{ t('messages.stock') }}</th>
            <th>{{ t('messages.unit_cost') }}</th>
            <th>{{ t('messages.total_value') }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($items as $item)
            <tr>
                <td>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-white/90 border border-slate-200/70 flex items-center justify-center flex-shrink-0 overflow-hidden text-slate-400">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="package" class="w-5 h-5 text-slate-400"></i>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('items.show', $item->id) }}" class="font-bold text-slate-900 hover:text-sky-600 transition block">
                                {{ localized_name($item, 'name') }}
                            </a>
                            <span class="text-[11px] text-slate-400 font-mono">Code: {{ $item->code }} @if($item->barcode) • Barcode: {{ $item->barcode }} @endif</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="font-semibold text-slate-800 text-xs block">{{ localized_name($item, 'category_name') }}</span>
                    <span class="text-[11px] text-slate-400">{{ $item->brand_name ?? '-' }}</span>
                </td>
                <td>
                    @if($item->item_type === 'CONSUMABLE')
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200/60">
                            {{ app()->getLocale() == 'km' ? 'ប្រើប្រាស់អស់' : 'Consumable' }}
                        </span>
                    @elseif($item->item_type === 'ASSET')
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200/60">
                            {{ app()->getLocale() == 'km' ? 'ទ្រព្យសកម្ម' : 'Asset' }}
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                            {{ app()->getLocale() == 'km' ? 'មិនប្រើប្រាស់អស់' : 'Non-Consumable' }}
                        </span>
                    @endif
                </td>
                <td>
                    <div class="flex items-baseline gap-1">
                        <span class="font-bold text-sm {{ $item->total_stock <= $item->reorder_level ? 'text-amber-600' : 'text-slate-900' }}">
                            {{ number_format($item->total_stock, 0) }}
                        </span>
                        <span class="text-[11px] text-slate-400">/ {{ localized_name($item, 'unit_name') }}</span>
                    </div>
                    @if($item->total_stock <= $item->reorder_level)
                        <span class="inline-flex items-center gap-1 text-[10px] text-amber-600 font-semibold mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ app()->getLocale() == 'km' ? 'ជិតអស់ពីស្តុក' : 'Low Stock' }}
                        </span>
                    @endif
                </td>
                <td class="font-medium text-slate-600">{{ format_currency($item->default_unit_cost) }}</td>
                <td class="font-bold text-slate-900">{{ format_currency($item->total_value) }}</td>
                <td>
                    @if(strtoupper($item->status) === 'ACTIVE')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ app()->getLocale() == 'km' ? 'សកម្ម' : 'Active' }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            {{ app()->getLocale() == 'km' ? 'អសកម្ម' : 'Inactive' }}
                        </span>
                    @endif
                </td>
                <td class="text-right space-x-1.5 whitespace-nowrap">
                    <a href="{{ route('items.show', $item->id) }}" class="btn-action btn-action-view" title="{{ t('messages.view') ?? 'View' }}">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('items.edit', $item->id) }}" class="btn-action btn-action-edit" title="{{ t('messages.edit') ?? 'Edit' }}">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ app()->getLocale() == 'km' ? 'តើអ្នកប្រាកដជាចង់លុបទំនិញនេះ?' : 'Are you sure you want to delete this item?' }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-action-delete" title="{{ t('messages.delete') ?? 'Delete' }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="8" icon="package-x" :title="app()->getLocale() == 'km' ? 'មិនមានទិន្នន័យទំនិញនៅឡើយទេ' : 'No Catalog Items Found'" :description="app()->getLocale() == 'km' ? 'អ្នកអាចចាប់ផ្តើមបង្កើតទំនិញថ្មីដំបូងរបស់អ្នកដោយចុចប៊ូតុងខាងក្រោម' : 'Get started by creating your first product item in the inventory catalog.'" :actionUrl="route('items.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
