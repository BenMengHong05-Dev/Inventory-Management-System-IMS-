@props(['type' => 'text', 'name', 'id' => null, 'value' => null, 'required' => false, 'label' => null, 'wrapperClass' => 'mb-3', 'icon' => null])
@php
    $id = $id ?? $name;
@endphp
<div class="{{ $wrapperClass }}">
    <div class="relative flex items-center px-3 {{ $label ? 'py-1' : 'py-2' }} bg-white hover:bg-slate-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-slate-400 rounded-xl transition-all border border-slate-200 focus-within:border-slate-400">
        @if($icon)
            <div class="text-slate-400 mr-2 flex-shrink-0 flex items-center justify-center">
                <i class="{{ $icon }}"></i>
            </div>
        @endif
        <div class="flex-1 flex flex-col justify-center min-w-0 cursor-text">
            @if($label)
                <label for="{{ $id }}" class="block text-[10px] font-bold text-slate-500 cursor-text {{ $required ? 'after:content-[\'*\'] after:text-rose-500 after:ml-0.5' : '' }}">{{ $label }}</label>
            @endif
            <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" value="{{ old($name, $value) }}" {{ $required ? 'required' : '' }} {{ $attributes->merge(['class' => 'w-full bg-transparent border-none p-0 focus:ring-0 text-sm font-medium text-slate-800 outline-none ' . ($label ? 'mt-0.5' : '')]) }}>
        </div>
    </div>
    @error($name)
        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
    @enderror
</div>
