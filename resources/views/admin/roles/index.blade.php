@extends('layouts.app')

@section('title', t('roles') . ' - IMS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ t('roles') }}</h1>
            <p class="text-xs text-gray-500">{{ app()->getLocale() == 'km' ? 'គ្រប់គ្រងតួនាទី និងការកំណត់សិទ្ធិប្រើប្រាស់ប្រព័ន្ធ (RBAC)' : 'Manage roles and system permissions (Role-Based Access Control)' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                <i class="bi bi-people mr-1.5"></i>
                {{ t('users') }}
            </a>
            <button onclick="document.getElementById('create_modal').classList.remove('hidden')" class="btn-primary">
                <i class="bi bi-plus-lg mr-1.5"></i>
                {{ t('add_new') }} {{ t('roles') }}
            </button>
        </div>
    </div>

    <!-- Table (Modern Clear Glass) -->
    <x-table>
        <x-slot name="head">
            <th>{{ t('roles') }}</th>
            <th>{{ t('description') }}</th>
            <th>{{ t('users') }}</th>
            <th>{{ t('permissions') ?? 'Permissions' }}</th>
            <th class="text-right">{{ t('actions') }}</th>
        </x-slot>

        @forelse($roles as $r)
            <tr>
                <td class="font-bold text-slate-900">
                    <a href="{{ route('admin.roles.edit', $r->id) }}" class="text-sky-600 hover:text-sky-700 transition">
                        {{ $r->name }}
                    </a>
                </td>
                <td class="text-xs text-slate-500 max-w-sm truncate">{{ $r->description ?: '-' }}</td>
                <td>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-700 border border-sky-500/20">
                        <i class="bi bi-people text-[11px]"></i>
                        <span>{{ $r->user_count ?? $r->users_count ?? 0 }}</span>
                    </span>
                </td>
                <td>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-700 border border-purple-500/20">
                        <i class="bi bi-key text-[11px]"></i>
                        <span>{{ $r->permission_count ?? $r->permissions_count ?? 0 }}</span>
                    </span>
                </td>
                <td class="text-right whitespace-nowrap space-x-1.5">
                    <a href="{{ route('admin.roles.edit', $r->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-sky-500/10 text-sky-700 hover:bg-sky-500/20 border border-sky-500/20 rounded-xl text-xs font-semibold transition" title="Configure Permissions">
                        <i class="bi bi-shield-check"></i>
                        <span>{{ t('permissions') ?? 'Permissions' }}</span>
                    </a>
                    @if($r->name != 'Super Admin')
                        <form method="POST" action="{{ route('admin.roles.destroy', $r->id) }}" onsubmit="return confirm('{{ t('confirm_delete') }}');" class="inline-block">
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
            <x-table-empty :colspan="5" icon="shield-lock" :title="app()->getLocale() == 'km' ? 'មិនទាន់មានតួនាទីទេ' : 'No Roles Defined'" :description="app()->getLocale() == 'km' ? 'បង្កើតតួនាទីថ្មីដើម្បីកំណត់សិទ្ធិប្រើប្រាស់' : 'Create roles to assign user permissions.'" />
        @endforelse
    </x-table>
</div>

<!-- Create Modal -->
<div id="create_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-white/60 w-full max-w-md p-6 space-y-4 m-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="bi bi-plus-circle text-sky-600"></i>
                {{ t('add_new') }} {{ t('roles') }}
            </h3>
            <button onclick="document.getElementById('create_modal').classList.add('hidden')" class="btn-action btn-action-delete !w-8 !h-8">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label required">{{ t('name') }}</label>
                <input type="text" name="name" class="form-input" placeholder="e.g. Warehouse Clerk, Purchasing Officer" required>
            </div>
            <div>
                <label class="form-label">{{ t('description') }}</label>
                <textarea name="description" rows="3" class="form-input" placeholder="{{ t('notes') }}..."></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('create_modal').classList.add('hidden')" class="btn-secondary">{{ t('cancel') }}</button>
                <button type="submit" class="btn-primary">{{ t('save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
