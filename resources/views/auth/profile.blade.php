@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">User Profile</h1>
            <p class="text-xs text-gray-500">Manage your personal account settings and password</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Details Card -->
        <div class="md:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                <i data-lucide="user" class="w-4 h-4 mr-2 text-emerald-600"></i>
                Account Details
            </h2>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2 border rounded-xl text-sm focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email Address</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full px-3 py-2 bg-gray-100 border rounded-xl text-sm text-gray-500 cursor-not-allowed">
                    <p class="text-[11px] text-gray-400 mt-0.5">Email address cannot be changed directly.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-3 py-2 border rounded-xl text-sm focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Profile Photo</label>
                    <input type="file" name="avatar" accept="image/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        Update Details
                    </button>
                </div>
            </form>
        </div>

        <!-- Role & Security Info Card -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h2 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                    <i data-lucide="shield" class="w-4 h-4 mr-2 text-emerald-600"></i>
                    Role & Permissions
                </h2>
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-gray-400 block">Assigned Role:</span>
                        <span class="font-bold text-gray-800 text-sm">{{ $user->role_name ?? 'Super Administrator' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Status:</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            {{ $user->status }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Last Login:</span>
                        <span class="text-gray-700">{{ $user->last_login_at ?? 'Current Session' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h2 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                    <i data-lucide="key" class="w-4 h-4 mr-2 text-emerald-600"></i>
                    Change Password
                </h2>
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" required
                               class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                               class="w-full px-3 py-2 border rounded-xl focus:border-emerald-500 focus:outline-none">
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition mt-2">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
