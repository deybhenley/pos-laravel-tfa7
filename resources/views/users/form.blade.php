@extends('layouts.app')
@section('title', $user->exists ? 'Edit User' : 'Create User')
@section('content')
    <div class="page-heading"><p class="eyebrow">USER ACCOUNTS</p><h1>{{ $user->exists ? 'Edit User' : 'Create User' }}</h1><p>{{ $user->exists ? 'Update this account and its profile picture.' : 'Enter a unique username and the account holder’s full name.' }}</p></div>
    <section class="panel form-panel">
        @if ($errors->any())<p class="alert alert-error" role="alert">Please correct the highlighted fields.</p>@endif
        <form action="{{ $user->exists ? route('users.update', $user, false) : route('users.store', [], false) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @if ($user->exists) @method('PUT') @endif
            <div class="field">
                <label for="username">Username <span class="required">Required</span></label>
                <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" autocomplete="off" @error('username') aria-invalid="true" aria-describedby="username-error" @enderror>
                <x-field-error name="username" />
            </div>
            <div class="field">
                <label for="full_name">Full name <span class="required">Required</span></label>
                <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $user->full_name) }}" autocomplete="name" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
                <x-field-error name="full_name" />
            </div>
            @if ($user->exists)
                <div class="field avatar-field">
                    <label for="avatar">Profile picture <span class="optional">Optional</span></label>
                    <div class="avatar-preview"><img class="avatar avatar-large" width="88" height="88" src="{{ $user->avatar ? route('avatars.show', ['filename' => basename($user->avatar)], false) : asset('images/avatar-placeholder.svg') }}" alt="Current profile picture"><p class="help">JPEG, PNG, or WebP. Maximum 2 MB.<br>Prepared as a 256 × 256 square thumbnail.</p></div>
                    <input id="avatar" name="avatar" type="file" accept=".jpg,.jpeg,.png,.webp" aria-describedby="avatar-help @error('avatar') avatar-error @enderror" @error('avatar') aria-invalid="true" @enderror>
                    <p class="help" id="avatar-help">Leave this empty to keep the current picture. If a submission fails, select your file again.</p>
                    <x-field-error name="avatar" />
                </div>
            @endif
            <div class="actions"><button class="button button-primary" type="submit">{{ $user->exists ? 'Save Changes' : 'Create User' }}</button><a class="button button-secondary" href="{{ route('users.index', [], false) }}">Cancel</a></div>
        </form>
    </section>
@endsection
