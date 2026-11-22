@extends('layouts.app')

@section('title', 'Page not found | Emplowise')
@section('meta_description', 'That page does not exist. Head back to Emplowise home, book a free HR audit, or contact the team.')

@section('content')
    <section class="section">
        <div class="shell">
            <p class="kicker">404</p>
            <h1>Page not found</h1>
            <p class="lede">That page does not exist, or it has moved. Everything else is one click away.</p>
            <div class="hero-actions">
                <a href="{{ route('home') }}" class="btn btn--primary">Back to home</a>
                <a href="{{ route('free-hr-audit') }}">Book a free HR audit</a>
                <a href="{{ route('contact') }}">Contact us</a>
            </div>
        </div>
    </section>
@endsection
