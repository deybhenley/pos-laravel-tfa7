@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <section class="hero">
        <p class="eyebrow">YOUR STORE, ORGANIZED</p>
        <h1>Keep your people<br>and their details in order.</h1>
        <p class="lead">Create customer accounts, update contact information, and keep your team directory easy to recognize.</p>
        <div class="actions">
            <a class="button button-primary" href="{{ route('customers.create', [], false) }}">Add a customer</a>
            <a class="button button-secondary" href="{{ route('users.index', [], false) }}">View your team</a>
        </div>
    </section>
    <section class="card-grid" aria-label="Account directories">
        <article class="card"><p class="eyebrow">CUSTOMER ACCOUNTS</p><p class="big-number">{{ $customerCount }}</p><h2>People you serve</h2><p>Browse names and contact details or update an existing account.</p><a class="text-link" href="{{ route('customers.index', [], false) }}">Open customer directory &rarr;</a></article>
        <article class="card"><p class="eyebrow">USER ACCOUNTS</p><p class="big-number">{{ $userCount }}</p><h2>Your store team</h2><p>Manage usernames, full names, and profile pictures.</p><a class="text-link" href="{{ route('users.index', [], false) }}">Open user directory &rarr;</a></article>
    </section>
@endsection
