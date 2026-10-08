<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <!-- Fixed Navigation Bar at the top of the viewport -->
        <nav class="navbar-fixed-top bg-white border-bottom border-gray-200 shadow-sm">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between" style="height: 60px;">
                    {{-- Logo --}}
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('jobseeker.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                        <span style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#0A2540,#1E3A5F);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;">
                            <i class="bi bi-briefcase-fill" aria-hidden="true"></i>
                        </span>
                        <span class="fw-bold text-dark ms-2" style="font-size:1rem;letter-spacing:.4px;color: var(--brand-navy);">{{ config('app.name', 'EMPLOYEASE') }}</span>
                    </a>

                    <!-- Right side: user menu -->
                    <div class="d-flex align-items-center gap-3">
                        @auth
                            <x-dropdown align="end" width="auto">
                                <x-slot name="trigger">
                                    <button class="btn btn-link p-0" style="line-height:1;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:32px;height:32px;border-radius:50%;background:#e9ecef;display:inline-flex;align-items:center;justify-content:center;color:#6c757d;font-weight:600;font-size:.8rem;">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                            <div class="d-none d-md-block text-truncate" style="max-width:140px;font-size:.85rem;">
                                                <div class="fw-medium text-dark mb-0" style="font-size:.9rem;">{{ Auth::user()->name }}</div>
                                            </div>
                                            <i class="bi bi-chevron-down text-muted" style="font-size:.7rem;"></i>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        <i class="bi bi-person-circle me-2"></i>{{ __('Profile') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}" class="dropdown-item-text">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content (pushed down to clear fixed navbar) -->
        <div class="main-content py-4" style="margin-top: 60px;">
            <div class="container">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow-sm border-bottom rounded-3 mb-4">
                        <div class="py-4 px-4">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
