@extends('layouts.app')
@section('title', $customer->exists ? 'Edit Customer' : 'Create Customer')
@section('content')
    <div class="page-heading"><p class="eyebrow">CUSTOMER ACCOUNTS</p><h1>{{ $customer->exists ? 'Edit Customer' : 'Create Customer' }}</h1><p>Enter the customer's name and contact information.</p></div>
    <section class="panel form-panel">
        @if ($errors->any())<p class="alert alert-error" role="alert">Please correct the highlighted fields.</p>@endif
        <form action="{{ $customer->exists ? route('customers.update', $customer, false) : route('customers.store', [], false) }}" method="POST" novalidate>
            @csrf
            @if ($customer->exists) @method('PUT') @endif
            <div class="field">
                <label for="full_name">Full name <span class="required">Required</span></label>
                <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $customer->full_name) }}" autocomplete="name" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
                <x-field-error name="full_name" />
            </div>
            <div class="field">
                <label for="email">Email address <span class="required">Required</span></label>
                <input id="email" name="email" type="email" value="{{ old('email', $customer->email) }}" autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                <x-field-error name="email" />
            </div>
            <div class="field">
                <label for="phone">Phone number <span class="optional">Optional</span></label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $customer->phone) }}" autocomplete="tel" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
                <x-field-error name="phone" />
            </div>
            <div class="actions"><button class="button button-primary" type="submit">{{ $customer->exists ? 'Save Changes' : 'Create Customer' }}</button><a class="button button-secondary" href="{{ route('customers.index', [], false) }}">Cancel</a></div>
        </form>
    </section>
@endsection
