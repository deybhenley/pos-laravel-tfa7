@extends('layouts.app')
@section('title', 'User Accounts')
@section('content')
    <div class="page-heading heading-actions">
        <div><p class="eyebrow">YOUR STORE TEAM</p><h1>User Accounts</h1><p>{{ $users->count() }} user records</p></div>
        <a class="button button-primary" href="{{ route('users.create', [], false) }}">+ Create User</a>
    </div>
    <section class="panel table-panel">
        <div class="table-scroll" tabindex="0" role="region" aria-label="User directory table">
            <table>
                <caption>Store usernames, full names, and profile pictures</caption>
                <thead><tr><th scope="col">Avatar</th><th scope="col">ID</th><th scope="col">Username</th><th scope="col">Full name</th><th scope="col">Action</th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td><img class="avatar" width="48" height="48" src="{{ $user->avatar ? route('avatars.show', ['filename' => basename($user->avatar)], false) : asset('images/avatar-placeholder.svg') }}" alt="{{ $user->avatar ? 'Avatar for '.$user->full_name : 'No profile picture for '.$user->full_name }}"></td>
                            <td class="muted">{{ $user->id }}</td><td>{{ $user->username }}</td><td class="name-cell">{{ $user->full_name }}</td>
                            <td><a class="edit-link" href="{{ route('users.edit', $user, false) }}">Edit<span class="sr-only"> {{ $user->full_name }}</span></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No users yet. Create the first account above.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
