@php($chat = config('emplowise.copy.chat'))
<div class="chat-root" id="chat-root" data-endpoint="{{ route('api.chat') }}">
    <section class="chat-panel" id="chat-panel" aria-label="{{ $chat['aria_label'] }}" hidden>
        <header class="chat-head">
            <div class="chat-head-id">
                <div class="chat-avatar" aria-hidden="true">E</div>
                <div>
                    <p class="chat-title">{{ $chat['title'] }}</p>
                    <p class="chat-sub">{{ $chat['sub'] }}</p>
                </div>
            </div>
            <div class="chat-head-actions">
                <button type="button" class="chat-reset" id="chat-reset">{{ $chat['reset'] }}</button>
                <button type="button" class="chat-close" id="chat-close" aria-label="{{ $chat['close'] }}">&times;</button>
            </div>
        </header>

        <div class="chat-log" id="chat-log" role="log" aria-live="polite">
            <div class="msg">{{ $chat['intro'] }}</div>
        </div>

        <div class="chat-suggestions" id="chat-suggestions" aria-label="Suggested replies"></div>

        <div class="chat-quick" id="chat-quick">
            @foreach ($chat['quick'] as $chip)
                <button type="button" class="quick-chip" data-q="{{ $chip['query'] }}">{{ $chip['label'] }}</button>
            @endforeach
        </div>

        <form class="chat-form" id="chat-form" autocomplete="off">
            <label class="sr-only" for="chat-input">{{ $chat['input_label'] }}</label>
            <input id="chat-input" name="message" type="text" maxlength="800" placeholder="Ask about payroll, pricing, policy..." required>
            <button class="chat-send" type="submit" id="chat-send">{{ $chat['send'] }}</button>
        </form>
        <p class="chat-note">{{ $chat['note'] }}</p>
    </section>

    <button type="button" class="chat-launcher" id="chat-launcher" aria-expanded="false" aria-controls="chat-panel">
        <span class="dot" aria-hidden="true"></span>
        {{ $chat['launcher'] }}
    </button>
</div>
