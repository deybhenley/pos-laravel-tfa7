@extends('layouts.app')
@section('title', 'Customer Accounts')
@section('content')
    <div class="page-heading heading-actions">
        <div><p class="eyebrow">PEOPLE &amp; RELATIONSHIPS</p><h1>Customer Accounts</h1><p>{{ $customers->count() }} customer records</p></div>
        <a class="button button-primary" href="{{ route('customers.create', [], false) }}">+ Create Customer</a>
    </div>
    <section class="panel table-panel">
        <div class="table-scroll" tabindex="0" role="region" aria-label="Customer directory table">
            <table>
                <caption>Customer names and contact information</caption>
                <thead><tr><th scope="col">ID</th><th scope="col">Full name</th><th scope="col">Email</th><th scope="col">Phone</th><th scope="col">Action</th></tr></thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr><td class="muted">{{ $customer->id }}</td><td class="name-cell">{{ $customer->full_name }}</td><td>{{ $customer->email }}</td><td class="nowrap">{{ $customer->phone ?? '—' }}</td><td><a class="edit-link" href="{{ route('customers.edit', $customer, false) }}">Edit<span class="sr-only"> {{ $customer->full_name }}</span></a></td></tr>
                    @empty
                        <tr><td colspan="5">No customers yet. Create the first account above.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
