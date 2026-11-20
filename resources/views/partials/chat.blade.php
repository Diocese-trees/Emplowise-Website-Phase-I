<div class="chat-root" id="chat-root" data-endpoint="{{ route('api.chat') }}">
    <section class="chat-panel" id="chat-panel" aria-label="Emplowise assistant" hidden>
        <header class="chat-head">
            <div class="chat-head-id">
                <div class="chat-avatar" aria-hidden="true">E</div>
                <div>
                    <p class="chat-title">Emplowise assistant</p>
                    <p class="chat-sub">Grounded answers only</p>
                </div>
            </div>
            <button type="button" class="chat-close" id="chat-close" aria-label="Close chat">&times;</button>
        </header>

        <div class="chat-log" id="chat-log" role="log" aria-live="polite">
            <div class="msg">hey. I am the Emplowise assistant. Ask about packages, what we run, or the free audit. Prices stay exact, guesses do not happen.</div>
        </div>

        <div class="chat-quick" id="chat-quick">
            <button type="button" class="quick-chip" data-q="What do the packages cost?">Pricing</button>
            <button type="button" class="quick-chip" data-q="What HR services do you run?">Services</button>
            <button type="button" class="quick-chip" data-q="How does the free HR audit work?">Free audit</button>
            <button type="button" class="quick-chip" data-q="What is in the student hub?">Student hub</button>
        </div>

        <form class="chat-form" id="chat-form" autocomplete="off">
            <label class="sr-only" for="chat-input">Your message</label>
            <input id="chat-input" name="message" type="text" maxlength="800" placeholder="Ask about payroll, pricing, policy..." required>
            <button class="chat-send" type="submit" id="chat-send">Send</button>
        </form>
        <p class="chat-note">Verified data only. Scope details open in the free audit.</p>
    </section>

    <button type="button" class="chat-launcher" id="chat-launcher" aria-expanded="false" aria-controls="chat-panel">
        <span class="dot" aria-hidden="true"></span>
        Ask Emplowise
    </button>
</div>
