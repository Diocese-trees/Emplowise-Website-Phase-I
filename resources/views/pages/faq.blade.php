@extends('layouts.app')

@section('title', 'FAQ | Emplowise')
@section('meta_description', 'Answers about Emplowise services, pricing, the free HR audit, and the free student hub. Confirmed data only, with unresolved details confirmed in writing after your Free HR Audit.')

@section('content')
    <section class="page-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <span>FAQ</span>
            </nav>
            <p class="kicker">{{ $total }} questions, four categories</p>
            <h1>The questions clients and students actually ask.</h1>
            <p class="lede">
                Search it, filter it, or open the assistant. Every answer uses confirmed business data. Anything
                unresolved is confirmed in writing after your Free HR Audit.
            </p>
        </div>
    </section>

    <section class="section section--tight">
        <div class="shell shell--narrow">
            <div class="faq-toolbar">
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                    <label class="sr-only" for="faq-search">Search frequently asked questions</label>
                    <input id="faq-search" type="search" placeholder="Search pricing, audit, ATS, payroll..." autocomplete="off">
                </div>
                <div class="filter-row" id="faq-filters">
                    <button type="button" class="filter-btn is-active" data-faq-filter="all">All</button>
                    @foreach ($groups as $group)
                        <button type="button" class="filter-btn" data-faq-filter="{{ $group['category'] }}">{{ $group['category'] }}</button>
                    @endforeach
                </div>
            </div>

            <div id="faq-list">
                @foreach ($groups as $groupIndex => $group)
                    <section class="faq-group" data-faq-group data-cat="{{ $group['category'] }}">
                        <div class="faq-group-head">
                            <h2>{{ $group['category'] }}</h2>
                            <span>{{ count($group['items']) }} answers</span>
                        </div>

                        <div class="accordion">
                            @foreach ($group['items'] as $itemIndex => $item)
                                @php($panelId = 'faq-panel-'.$groupIndex.'-'.$itemIndex)
                                <div class="acc-item" data-cat="{{ $group['category'] }}">
                                    <button type="button"
                                            class="acc-trigger"
                                            aria-expanded="false"
                                            aria-controls="{{ $panelId }}">
                                        <span class="acc-q">{{ $item['q'] }}</span>
                                        <span class="acc-icon" aria-hidden="true"></span>
                                    </button>
                                    <div class="acc-panel" id="{{ $panelId }}">
                                        <p class="acc-a">{{ $item['a'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <p class="faq-empty" id="faq-empty">No answer matches that search. Try one broader word, or ask the assistant.</p>

            <div class="card card--flat s3">
                <div class="panel-head s4">
                    <div>
                        <p class="kicker s5">Still stuck</p>
                        <h2 class="s6">Ask the Emplowise assistant</h2>
                    </div>
                    <span class="badge">Grounded answers</span>
                </div>
                <p class="small s7">
                    The assistant answers from the same verified data as this page. It does not guess prices,
                    client names, or legal outcomes.
                </p>
                <button type="button" class="btn btn--primary btn--sm" data-open-chat>Open the assistant</button>
            </div>

            <p class="small muted s1">{{ $note }}</p>
        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>Ready for a real answer about your HR stack?</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">Book the free audit</a>
                <a href="{{ route('student-hub') }}" class="btn btn--outline-light">Try the student tools</a>
            </div>
        </div>
    </section>
@endsection
