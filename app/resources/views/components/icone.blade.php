@props(['name', 'size' => 18])
<svg {{ $attributes->merge(['class' => 'ui-icon']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
    @case('arrow-left')
    <path d="M19 12H5m6-6-6 6 6 6" /> @break
    @case('arrow-right')
    <path d="M5 12h14m-6-6 6 6-6 6" /> @break
    @case('arrow-up')
    <path d="M12 19V5m-6 6 6-6 6 6" /> @break
    @case('arrow-down')
    <path d="M12 5v14m-6-6 6 6 6-6" /> @break
    @case('plus')
    <path d="M12 5v14M5 12h14" /> @break
    @case('trash')
    <path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7" /> @break
    @case('copy')
    <rect x="8" y="8" width="12" height="13" rx="2"/><path d="M16 8V3H3v13h5" /> @break
    @case('download')
    <path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5" /> @break
    @case('edit')
    <path d="m15 5 4 4M4 20l4-1L20 7a2.8 2.8 0 0 0-4-4L4 15l-1 6 5-2" /> @break
    @endswitch
</svg>
