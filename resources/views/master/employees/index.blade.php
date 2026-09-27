@extends('layouts.app')

@section('title', t('employees') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('employees') }}</h1>
            <p class="text-xs text-gray-500">{{ t('manage') }} {{ t('employees') }} &amp; {{ t('asset_custodians') ?? 'Custodians' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('master.employees.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i> {{ t('add_new') }} {{ t('employees') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar (No white card background, no shadow, search on left, filters on right) -->
    <form method="GET" action="{{ route('master.employees.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ t('search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-48">
                <x-select name="department_id" wrapperClass="!mb-0">
                    <option value="">{{ t('all') }} {{ t('departments') }}</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ ($deptId ?? '') == $d->id ? 'selected' : '' }}>
                            {{ localized_name($d, 'name') }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full sm:w-36">
                <x-select name="status" wrapperClass="!mb-0">
                    <option value="">{{ t('all') }} {{ t('status') }}</option>
                    <option value="ACTIVE" {{ ($status ?? '') === 'ACTIVE' ? 'selected' : '' }}>{{ t('active') }}</option>
                    <option value="INACTIVE" {{ ($status ?? '') === 'INACTIVE' ? 'selected' : '' }}>{{ t('inactive') }}</option>
                </x-select>
            </div>
            <x-button type="submit" variant="dark" icon="bi bi-funnel">
                {{ t('filter') }}
            </x-button>
            @if(!empty($search) || !empty($deptId) || !empty($status))
                <a href="{{ route('master.employees.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full shrink-0" title="Clear">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Employees Table -->
    <x-table :pagination="$employees">
        <x-slot name="head">
            <th>{{ t('code') }} &amp; {{ t('name') }}</th>
            <th>{{ t('departments') }}</th>
            <th>{{ t('position') ?? 'Position' }}</th>
            <th>{{ t('contact') ?? 'Contact' }}</th>
            <th>{{ t('assets') }}</th>
            <th>{{ t('status') }}</th>
            <th class="text-right">{{ t('actions') }}</th>
        </x-slot>

        @forelse($employees as $e)
        <tr>
            <td>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/90 border border-slate-200/70 flex items-center justify-center font-bold text-slate-700 text-xs shrink-0 overflow-hidden shadow-sm">
                        @if($e->photo)
                            <img src="{{ asset('storage/' . $e->photo) }}" class="w-full h-full object-cover" alt="Avatar">
                        @else
                            {{ mb_substr($e->name_en ?? $e->name_km ?? 'E', 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 hover:text-sky-600 transition">
                            {{ localized_name($e, 'name') }}
                        </div>
                        <div class="text-[11px] font-mono text-sky-600 font-semibold">{{ $e->code }}</div>
                    </div>
                </div>
            </td>
            <td class="text-xs font-medium text-slate-700">
                @if($e->department_name || $e->department_name_km)
                    {{ localized_name((object)['name_en' => $e->department_name, 'name_km' => $e->department_name_km], 'name') }}
                @else
                    -
                @endif
            </td>
            <td class="text-xs text-slate-600 font-medium">{{ $e->position ?: '-' }}</td>
            <td class="text-xs text-slate-600 space-y-0.5">
                @if($e->phone)
                    <div class="flex items-center gap-1.5"><i class="bi bi-telephone text-slate-400"></i> {{ $e->phone }}</div>
                @endif
                @if($e->email)
                    <div class="flex items-center gap-1.5"><i class="bi bi-envelope text-slate-400"></i> {{ $e->email }}</div>
                @endif
                @if(!$e->phone && !$e->email)
                    <span class="text-slate-400">-</span>
                @endif
            </td>
            <td>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ ($e->active_assets_count ?? 0) > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/70' : 'bg-slate-100 text-slate-500' }}">
                    <i class="bi bi-box-seam mr-1"></i> {{ $e->active_assets_count ?? 0 }}
                </span>
            </td>
            <td>
                @if(strtoupper($e->status ?? '') === 'ACTIVE')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ t('active') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        {{ t('inactive') }}
                    </span>
                @endif
            </td>
            <td class="text-right space-x-1.5 whitespace-nowrap">
                <a href="{{ route('master.employees.show', $e->id) }}" class="btn-action btn-action-view" title="{{ t('view') }}">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('master.employees.edit', $e->id) }}" class="btn-action btn-action-edit" title="{{ t('edit') }}">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('master.employees.destroy', $e->id) }}" onsubmit="return confirm('{{ t('confirm_delete') }}');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-action-delete" title="{{ t('delete') }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
            <x-table-empty :colspan="7" icon="bi bi-people" :title="t('no_data_available')" />
        @endforelse
    </x-table>
</div>
@endsection
