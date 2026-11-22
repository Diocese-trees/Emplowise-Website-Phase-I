@props(['name', 'size' => 20, 'stroke' => 1.8])

@php
    // One icon system: 24-unit grid, currentColor, round joins. Brand marks
    // that read best as solid shapes opt into fill; everything else stays
    // on the same 1.8 stroke so rows and buttons look like one family.
    $filled = in_array($name, ['github'], true);
@endphp

@if ($filled)
    <svg {{ $attributes->class('icon') }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
@else
    <svg {{ $attributes->class('icon') }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
@endif

@switch($name)
    @case('arrow-right')
        <path d="M4 12h15M14 6l6 6-6 6"/>
        @break

    @case('arrow-up')
        <path d="M12 19V5M5 12l7-7 7 7"/>
        @break

    @case('external')
        <path d="M7 17 17 7M9 7h8v8"/>
        @break

    @case('lock')
        <rect x="4" y="10" width="16" height="11" rx="2"/>
        <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
        @break

    @case('card')
        <rect x="2" y="5" width="20" height="14" rx="2"/>
        <path d="M2 10h20M6 15h4"/>
        @break

    @case('chart-line')
        <path d="M3 3v18h18"/>
        <path d="M7 15l4-5 3 3 5-7"/>
        @break

    @case('file-check')
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <path d="M14 2v6h6M9 15l2 2 4-4"/>
        @break

    @case('copy')
        <rect x="9" y="9" width="12" height="12" rx="2"/>
        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
        @break

    @case('clock')
        <circle cx="12" cy="12" r="9"/>
        <path d="M12 7v5l3 2"/>
        @break

    @case('chip')
        <rect x="2.5" y="6" width="19" height="12" rx="2"/>
        <circle cx="12" cy="12" r="2.6"/>
        <path d="M6 10h.01M18 14h.01"/>
        @break

    @case('chart-bars')
        <path d="M4 20V4M4 20h16"/>
        <path d="M8 16v-4M12 16V7M16 16v-6M20 16v-2"/>
        @break

    @case('shield-check')
        <path d="M12 3l7 3v6c0 4.4-3 7.6-7 9-4-1.4-7-4.6-7-9V6z"/>
        <path d="M9.2 12.2l2 2 3.6-3.8"/>
        @break

    @case('check')
        <path d="M20 6 9 17l-5-5"/>
        @break

    @case('search')
        <circle cx="11" cy="11" r="7"/>
        <path d="m20 20-3.5-3.5"/>
        @break

    @case('mail')
        <rect x="2" y="4" width="20" height="16" rx="2"/>
        <path d="m2.5 6.5 8.3 6a2 2 0 0 0 2.4 0l8.3-6"/>
        @break

    @case('linkedin')
        <rect x="3" y="3" width="18" height="18" rx="4"/>
        <path d="M7.5 10.5V17M7.5 7.2v.01"/>
        <path d="M11.5 17v-4a2.5 2.5 0 0 1 5 0v4"/>
        <path d="M11.5 10.5V17"/>
        @break

    @case('facebook')
        <circle cx="12" cy="12" r="9"/>
        <path d="M14.8 20.6v-7h2.3l.4-2.7h-2.7V9.1c0-.8.3-1.2 1.3-1.2h1.5V5.5c-.3 0-1.1-.1-2-.1-2.2 0-3.5 1.3-3.5 3.6v1.9h-2.3v2.7h2.3v7"/>
        @break

    @case('instagram')
        <rect x="3" y="3" width="18" height="18" rx="5"/>
        <circle cx="12" cy="12" r="4"/>
        <circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none"/>
        @break

    @case('x')
        <path d="M4.5 4.5l15 15M19.5 4.5l-15 15"/>
        @break

    @case('youtube')
        <rect x="2.5" y="5.5" width="19" height="13" rx="4"/>
        <path d="m10.5 9.5 4.5 2.5-4.5 2.5z" fill="currentColor" stroke="none"/>
        @break

    @case('whatsapp')
        <path d="M20.5 11.6a8.5 8.5 0 0 1-12.6 7.4L3.5 20.5l1.5-4.3A8.5 8.5 0 1 1 20.5 11.6z"/>
        <path d="M9 8.4c.3-.3.6-.3.9 0l.9 1.6c.1.3.1.5-.1.7l-.5.6c.6 1.1 1.5 2 2.6 2.6l.6-.5c.2-.2.4-.2.7-.1l1.6.9c.3.2.4.6 0 .9-.6.7-1.5 1-2.4.8-2.9-.6-5.2-2.9-5.8-5.8-.2-.9 0-1.8.7-2.4z"/>
        @break

    @case('github')
        <path d="M12 .5C5.73.5.5 5.73.5 12c0 5.08 3.29 9.39 7.86 10.91.58.11.79-.25.79-.56v-2.17c-3.2.7-3.87-1.37-3.87-1.37-.53-1.34-1.3-1.7-1.3-1.7-1.06-.72.08-.71.08-.71 1.17.08 1.79 1.2 1.79 1.2 1.04 1.78 2.73 1.27 3.4.97.11-.75.41-1.27.74-1.56-2.55-.29-5.23-1.28-5.23-5.7 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.18 1.18a11 11 0 0 1 5.8 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.84 1.19 3.1 0 4.43-2.69 5.41-5.25 5.69.42.36.79 1.08.79 2.18v3.23c0 .31.21.68.8.56A11.5 11.5 0 0 0 23.5 12C23.5 5.73 18.27.5 12 .5z"/>
        @break
@endswitch
</svg>
