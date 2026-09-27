@extends('layouts.app')

@section('title', t('users') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('users') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'គ្រប់គ្រងគណនីអ្នកប្រើប្រាស់ ការកំណត់សិទ្ធិ និងតួនាទី' : 'Manage system user accounts, credentials, and access roles' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary">
                <i class="bi bi-shield-lock mr-1.5"></i>
                {{ t('roles') }}
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">
                <i class="bi bi-person-plus mr-1.5"></i>
                {{ t('add_new') }} {{ t('users') }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <!-- Search on Left -->
        <div class="w-full lg:w-72">
            <x-input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ t('search') }}..." wrapperClass="!mb-0" icon="bi bi-search" />
        </div>

        <!-- Filter Controls on Right -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 justify-end w-full lg:w-auto">
            <div class="w-full sm:w-48">
                <x-select name="role_id" wrapperClass="!mb-0">
                    <option value="">{{ t('all') }} {{ t('roles') }}</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ ($roleId ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
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
            <a href="{{ route('admin.users.index') }}" class="btn-secondary !p-2 !w-9 !h-9 flex items-center justify-center rounded-full" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <!-- Table (Modern Clear Glass) -->
    <x-table :pagination="$users">
        <x-slot name="head">
            <th>{{ t('users') }}</th>
            <th>{{ t('roles') }}</th>
            <th>{{ t('phone') ?? 'Phone' }}</th>
            <th>{{ t('status') }}</th>
            <th>{{ t('last_login') ?? 'Last Login' }}</th>
            <th class="text-right">{{ t('actions') }}</th>
        </x-slot>

        @forelse($users as $u)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden border border-slate-200/80 shadow-sm">
                            @if(!empty($u->avatar))
                                <img src="{{ asset('storage/' . $u->avatar) }}" class="w-full h-full object-cover" alt="Avatar">
                            @else
                                {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 text-xs">{{ $u->name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                        <i class="bi bi-shield-check text-[10px]"></i>
                        {{ $u->role_name ?: 'No Role' }}
                    </span>
                </td>
                <td class="text-xs text-slate-600 font-mono">{{ $u->phone ?: '-' }}</td>
                <td>
                    @if(strtoupper($u->status ?? '') === 'ACTIVE')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ t('active') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ t('inactive') }}
                        </span>
                    @endif
                </td>
                <td class="text-xs text-slate-500 whitespace-nowrap">
                    {{ !empty($u->last_login_at) ? date('Y-m-d H:i', strtotime($u->last_login_at)) : '-' }}
                </td>
                <td class="text-right whitespace-nowrap space-x-1.5">
                    <a href="{{ route('admin.users.edit', $u->id) }}" class="btn-action btn-action-edit" title="{{ t('edit') }}">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @if($u->id != session('user_id'))
                        <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" onsubmit="return confirm('{{ t('confirm_delete') }}');" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-action-delete" title="{{ t('delete') }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <x-table-empty :colspan="6" icon="people" :title="app()->getLocale() == 'km' ? 'មិនមានគណនីអ្នកប្រើប្រាស់ទេ' : 'No User Accounts Found'" :description="app()->getLocale() == 'km' ? 'បង្កើតអ្នកប្រើប្រាស់ថ្មីសម្រាប់ប្រព័ន្ធ' : 'Create new users and assign role permissions.'" :actionUrl="route('admin.users.create')" :actionLabel="t('messages.create')" />
        @endforelse
    </x-table>
</div>
@endsection
