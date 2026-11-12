<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Home') | POS Foundations</title>
    <link rel="stylesheet" href="/css/pos.css">
</head>
<body>
    <a class="skip" href="#main-content">Skip to content</a>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ route('home', [], false) }}"><span class="brand-icon" aria-hidden="true">P</span> POS <strong>Foundations</strong></a>
            <nav aria-label="Main navigation">
                <a href="{{ route('home', [], false) }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('about', [], false) }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                <a href="{{ route('customers.index', [], false) }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">Customers</a>
                <a href="{{ route('users.index', [], false) }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Users</a>
            </nav>
        </div>
    </header>
    <main id="main-content" class="container main-content">
        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="site-footer"><div class="container footer-inner"><span>&copy; {{ date('Y') }} POS Foundations</span><span>IT0049 &middot; TFA7</span></div></footer>
</body>
</html>
