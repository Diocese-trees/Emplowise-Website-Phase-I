<div class="team-stage">
    <div class="team-window" role="group" aria-label="{{ $team['window_label'] }}">
        <div class="team-window-bar">
            <span class="team-dots" aria-hidden="true"><i></i><i></i><i></i></span>
            <span class="team-window-url">
                <x-icon name="lock" :size="13" />
                {{ $team['window_title'] }}
            </span>
            <span class="team-window-meta">{{ $team['window_meta'] }}</span>
        </div>

        <div class="team-tabs" role="tablist" aria-label="{{ $team['tablist_label'] }}">
            @foreach ($team['profiles'] as $profile)
                <button type="button"
                        class="team-tab {{ $loop->first ? 'is-active' : '' }}"
                        id="team-tab-{{ $profile['id'] }}"
                        data-team-tab="{{ $profile['id'] }}"
                        role="tab"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        aria-controls="team-panel-{{ $profile['id'] }}"
                        tabindex="{{ $loop->first ? '0' : '-1' }}">
                    <span class="team-tab-num">{{ $profile['num'] }}</span>
                    <span class="team-tab-name">{{ $profile['name'] }}</span>
                    <span class="team-tab-role">{{ $profile['role'] }}</span>
                </button>
            @endforeach
        </div>

        <div class="team-panels">
            @foreach ($team['profiles'] as $profile)
                @php
                    $portrait = (string) ($profile['portrait'] ?? '');
                    $isPlaceholder = str_contains($portrait, 'placeholder-');
                    $words = preg_split('/\s+/', trim((string) $profile['name'])) ?: [];
                    $initials = '';
                    foreach (array_slice($words, 0, 2) as $word) {
                        $initials .= mb_strtoupper(mb_substr($word, 0, 1));
                    }
                @endphp
                <section class="team-panel {{ $loop->first ? 'is-active' : '' }}"
                         id="team-panel-{{ $profile['id'] }}"
                         data-team-panel="{{ $profile['id'] }}"
                         role="tabpanel"
                         aria-labelledby="team-tab-{{ $profile['id'] }}"
                         tabindex="0">
                    <figure class="team-photo">
                        @if ($isPlaceholder)
                            {{-- Headshot pending: show a neutral initials block instead of a stand-in portrait. --}}
                            <div class="team-avatar" role="img" aria-label="{{ $profile['name'] }}">
                                <span aria-hidden="true">{{ $initials }}</span>
                            </div>
                        @else
                            <picture>
                                <source type="image/webp"
                                        srcset="{{ asset_v($profile['portrait_webp_small']) }} 420w, {{ asset_v($profile['portrait_webp']) }} 840w"
                                        sizes="(max-width: 900px) 100vw, 420px">
                                <img src="{{ asset_v($profile['portrait']) }}"
                                     alt="{{ $profile['alt'] }}"
                                     width="800"
                                     height="1000"
                                     loading="lazy"
                                     decoding="async">
                            </picture>
                        @endif
                        <figcaption><span>{{ $profile['num'] }}</span> {{ $profile['role'] }}</figcaption>
                    </figure>

                    <div class="team-profile">
                        <p class="team-role">{{ $profile['role'] }}</p>
                        <h3>{{ $profile['name'] }}</h3>
                        <p class="team-focus">
                            <span>{{ $team['focus_label'] }}</span>
                            {{ $profile['focus'] }}
                        </p>
                        @if ($profile['bio_todo'])
                            {{-- {{ $profile['bio_todo'] }} --}}
                        @endif
                        <p class="team-bio">{{ $profile['bio'] }}</p>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</div>

<p class="team-note">{{ $team['panel_note'] }}</p>
