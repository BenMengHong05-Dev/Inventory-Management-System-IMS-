@props(['variant' => 'primary', 'type' => 'button', 'icon' => null])
@php
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'dark' => 'btn-dark',
        'danger' => 'btn-danger',
    ];
    $classes = $variants[$variant] ?? $variants['primary'];
@endphp
<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        @if(str_contains($icon, 'bi-') || str_contains($icon, 'bi '))
            <i class="{{ $icon }} @if($slot->isNotEmpty()) mr-1.5 @endif"></i>
        @else
            <i data-lucide="{{ $icon }}" class="w-4 h-4 @if($slot->isNotEmpty()) mr-1.5 @endif"></i>
        @endif
    @endif
    {{ $slot }}
</button>
