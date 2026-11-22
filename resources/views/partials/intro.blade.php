@php
    $mark = config('emplowise.brand.name');
    $cut = max(1, mb_strlen($mark) - 4);
@endphp

<div class="intro" id="intro" aria-hidden="true">
    <div class="intro-stage">
        <span class="intro-glow"></span>
        <span class="intro-word"><span class="intro-part intro-part--a">{{ mb_substr($mark, 0, $cut) }}</span><span class="intro-part intro-part--b">{{ mb_substr($mark, $cut) }}</span></span>
        <span class="intro-sub">{{ config('emplowise.brand.essence') }}</span>
    </div>
</div>
