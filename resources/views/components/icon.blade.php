@props(['name', 'size' => 20])
<svg {{ $attributes->class('kl-i') }} width="{{ $size }}" height="{{ $size }}" aria-hidden="true" focusable="false"><use href="#i-{{ $name }}"/></svg>
