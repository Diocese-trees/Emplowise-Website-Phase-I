@php
    $h1Text = $seo['h1'];
    $h1Accent = $seo['h1_accent'] ?? null;
@endphp
<h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
