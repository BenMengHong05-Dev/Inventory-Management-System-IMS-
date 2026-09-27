@extends('layouts.app')

@section('title', t('messages.assets'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('messages.assets') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'តាមដានទ្រព្យសកម្មរូបវន្ត លេខស៊េរី ការធានា ការប្រគល់ និងស្ថានភាព' : 'Track individual physical assets, serial numbers, warranty, assignments, and conditions' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('asset-assignments.create') }}" class="btn-secondary">
                <i class="bi bi-person-check mr-1.5"></i>
                {{ t('messages.asset_assignments') }}
            </a>
            <a href="{{ route('assets.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('messages.create') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('assets.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search }}" placeholder="{{ t('messages.search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-36">
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.status') }}</option>
                    <option value="IN_STOCK" {{ $status === 'IN_STOCK' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ក្នុងស្តុក' : 'In Stock' }}</option>
                    <option value="ASSIGNED" {{ $status === 'ASSIGNED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'បានប្រគល់ជូន' : 'Assigned' }}</option>
                    <option value="IN_USE" {{ $status === 'IN_USE' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'កំពុងប្រើប្រាស់' : 'In Use' }}</option>
                    <option value="MAINTENANCE" {{ $status === 'MAINTENANCE' ? 'selected' : '' }}>{{ t('messages.maintenance') }}</option>
                    <option value="DISPOSED" {{ $status === 'DISPOSED' ? 'selected' : '' }}>{{ t('messages.disposal') }}</option>
                </x-select>
            </div>
            <div class="w-full sm:w-36">
                <x-select name="condition" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ app()->getLocale() == 'km' ? 'ស្ថានភាពរូបវន្ត' : 'Conditions' }}</option>
                    <option value="NEW" {{ $condition === 'NEW' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ថ្មី' : 'New' }}</option>
                    <option value="GOOD" {{ $condition === 'GOOD' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ល្អ' : 'Good' }}</option>
                    <option value="FAIR" {{ $condition === 'FAIR' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'មធ្យម' : 'Fair' }}</option>
                    <option value="DAMAGED" {{ $condition === 'DAMAGED' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ខូចខាត' : 'Damaged' }}</option>
                    <option value="CRITICAL" {{ $condition === 'CRITICAL' ? 'selected' : '' }}>{{ app()->getLocale() == 'km' ? 'ធ្ងន់ធ្ងរ' : 'Critical' }}</option>
                </x-select>
            </div>
            <div class="w-full sm:w-40">
                <x-select name="category_id" wrapperClass="!mb-0">
                    <option value="">{{ t('messages.all') }} {{ t('messages.categories') }}</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ $categoryId == $c->id ? 'selected' : '' }}>{{ localized_name($c, 'name') }}</option>
                    @endforeach
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('messages.filter') }}
            </x-button>
            <a href="{{ route('assets.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$assets">
        <x-slot name="head">
            <th>{{ t('messages.assets') }} & {{ t('messages.code') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'លេខស៊េរី' : 'Serial / Barcode' }}</th>
            <th>{{ t('messages.categories') }} & {{ t('messages.brands') }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'ស្ថានភាព' : 'Condition' }}</th>
            <th>{{ app()->getLocale() == 'km' ? 'អ្នកកាន់កាប់បច្ចុប្បន្ន' : 'Current Holder' }}</th>
            <th class="text-right">{{ t('messages.unit_cost') }}</th>
            <th>{{ t('messages.status') }}</th>
            <th class="text-right">{{ t('messages.actions') }}</th>
        </x-slot>

        @forelse($assets as $ast)
            <tr>
                <td>
                    <a href="{{ route('assets.show', $ast->id) }}" class="font-bold text-slate-900 hover:text-sky-600 block transition">
                        {{ $ast->asset_name }}
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $ast->asset_code }}</span>
                </td>
                <td class="font-mono text-slate-700 text-xs">
                    <span>{{ $ast->serial_number ?? '-' }}</span>
                </td>
                <td>
                    <span class="font-medium text-slate-800 block text-xs">{{ localized_name($ast, 'category_name') }}</span>
                    <span class="text-[11px] text-slate-400">{{ $ast->brand_name ?? '-' }} {{ $ast->model ?? '' }}</span>
                </td>
                <td>
                    @if($ast->condition === 'GOOD' || $ast->condition === 'NEW')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            {{ $ast->condition }}
                        </span>
                    @elseif($ast->condition === 'FAIR')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            {{ $ast->condition }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                            {{ $ast->condition }}
                        </span>
                    @endif
                </td>
                <td>
                    @if($ast->employee_name)
                        <span class="font-bold text-slate-800 block text-xs">{{ localized_name($ast, 'employee_name') }}</span>
                        <span class="text-[11px] text-slate-400">{{ localized_name($ast, 'department_name') }}</span>
                    @else
                        <span class="text-slate-400 italic text-xs">{{ app()->getLocale() == 'km' ? 'ក្នុងឃ្លាំង' : 'In Warehouse' }} ({{ $ast->warehouse_name ?? (app()->getLocale() == 'km' ? 'ឃ្លាំងកណ្តាល' : 'Depot') }})</span>
                    @endif
                </td>
                <td class="text-right font-bold text-slate-900">{{ format_currency($ast->purchase_cost) }}</td>
                <td>
                    @if($ast->status === 'IN_STOCK')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $ast->status }}
                        </span>
                    @elseif($ast->status === 'ASSIGNED')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                            {{ $ast->status }}
                        </span>
                    @elseif($ast->status === 'MAINTENANCE')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-700 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $ast->status }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $ast->status }}
                        </span>
                    @endif
                </td>
                <td class="text-right whitespace-nowrap space-x-1.5">
                    <a href="{{ route('assets.show', $ast->id) }}" class="btn-action btn-action-view" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('assets.edit', $ast->id) }}" class="btn-action btn-action-edit" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="8" icon="laptop" :title="app()->getLocale() == 'km' ? 'មិនមានកំណត់ត្រាទ្រព្យសកម្មនៅឡើយទេ' : 'No Assets Recorded Yet'" :description="app()->getLocale() == 'km' ? 'ចាប់ផ្តើមកត់ត្រា និងគ្រប់គ្រងទ្រព្យសម្បត្តិរូបវន្តរបស់ស្ថាប័ន' : 'Start tracking and managing your organization\'s physical assets and equipment.'" :actionUrl="route('assets.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
