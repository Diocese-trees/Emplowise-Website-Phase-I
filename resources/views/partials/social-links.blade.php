@php
    $socials = config('emplowise.copy.contact.socials');
    $pendingTemplate = config('emplowise.copy.contact.social_pending');
    $variant = $variant ?? 'card';
    $iconSize = $variant === 'footer' ? 18 : 20;
@endphp

<div class="social-row social-row--{{ $variant }}">
    @foreach ($socials as $social)
        @php($pending = ! empty($social['pending']))
        <a class="social-link"
           href="{{ $pending ? '#' : $social['href'] }}"
           aria-label="{{ $social['label'] }}"
           title="{{ $social['label'] }}{{ $pending ? ' — profile pending' : '' }}"
           @if ($pending)
               data-social-pending="{{ str_replace(':label', $social['label'], $pendingTemplate) }}"
           @else
               target="_blank" rel="noopener noreferrer"
           @endif
        >
            <x-icon :name="$social['icon']" :size="$iconSize" />
        </a>
    @endforeach
</div>
