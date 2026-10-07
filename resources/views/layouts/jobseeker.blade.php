<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/css/responsive.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="main-sidebar" id="sidebar">
            <div class="sidebar-brand">
                <a href="{{ route('jobseeker.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                    <div class="brand-icon">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div class="brand-text">
                        <span class="brand-name">EMPLOYEASE</span>
                        <small class="brand-subtitle">Job Seeker</small>
                    </div>
                </a>
            </div>

            <div class="sidebar-user px-3 py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    @php $photo = auth()->user()->profile?->profile_photo_path; @endphp
                    @if($photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($photo))
                        <img src="{{ Storage::url($photo) }}" alt="" class="rounded-circle" style="width:38px;height:38px;object-fit:cover">
                    @else
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-weight:600">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-grow-1 min-w-0">
                        <p class="mb-0 fw-semibold text-truncate">{{ auth()->user()->name }}</p>
                        <small class="text-muted">Job Seeker</small>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('jobseeker.dashboard') }}" class="nav-link {{ request()->routeIs('jobseeker.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span>Jobs</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('jobseeker.jobs.index') }}" class="nav-link {{ request()->routeIs('jobseeker.jobs.index') || request()->routeIs('jobseeker.jobs.show') ? 'active' : '' }}">
                            <i class="bi bi-search-heart-fill"></i>
                            <span>Browse Jobs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('jobseeker.jobs.saved') }}" class="nav-link {{ request()->routeIs('jobseeker.jobs.saved') ? 'active' : '' }}">
                            <i class="bi bi-bookmark-fill"></i>
                            <span>Saved Jobs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('jobseeker.applications.index') }}" class="nav-link {{ request()->routeIs('jobseeker.applications.*') ? 'active' : '' }}">
                            <i class="bi bi-file-text-fill"></i>
                            <span>My Applications</span>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span>Account</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('jobseeker.profile.index') }}" class="nav-link {{ request()->routeIs('jobseeker.profile.*') ? 'active' : '' }}">
                            <i class="bi bi-person-fill"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('jobseeker.notifications.index') }}" class="nav-link {{ request()->routeIs('jobseeker.notifications.*') ? 'active' : '' }}">
                            <i class="bi bi-bell-fill"></i>
                            <span>Notifications</span>
                            @if($unreadCount > 0)
                                <span class="badge bg-brand-gold rounded-pill ms-auto">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer mt-auto p-3 border-top">
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary w-100 mb-2">
                    <i class="bi bi-house-door"></i> Home
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                        <i class="bi bi-box-arrow-left"></i> Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Navbar -->
            <nav class="main-header navbar navbar-expand navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-link text-dark sidebar-toggle me-2" id="sidebarToggle" type="button">
                        <i class="bi bi-list fs-5"></i>
                    </button>

                    <div class="d-flex align-items-center gap-3 ms-auto">
                        <!-- Notifications -->
                        <div class="dropdown">
                            <button class="btn btn-link position-relative notification-bell" type="button" data-bs-toggle="dropdown" id="notificationDropdown">
                                <i class="bi bi-bell fs-5"></i>
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill notification-badge" style="font-size:10px">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow notification-dropdown" aria-labelledby="notificationDropdown">
                                <div class="dropdown-header d-flex justify-content-between align-items-center">
                                    <strong>Notifications</strong>
                                    @if($unreadCount > 0)
                                        <button class="btn btn-sm btn-link text-decoration-none p-0 mark-all-read" type="button">
                                            Mark all read
                                        </button>
                                    @endif
                                </div>
                                <div class="notification-list" style="max-height:300px;overflow-y:auto">
                                    @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                        <a href="{{ $notification->action_url ?? '#' }}" class="dropdown-item notification-item {{ !$notification->is_read ? 'unread' : '' }}">
                                            <div class="d-flex gap-2">
                                                <div class="notification-icon">
                                                    <i class="bi {{ $notification->icon }} text-{{ $notification->color }}"></i>
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <p class="mb-0 fw-semibold small">{{ $notification->title }}</p>
                                                    <small class="text-muted">{{ Str::limit($notification->message, 50) }}</small>
                                                    <br>
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="text-center py-4 text-muted">
                                            <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                                            <small>No notifications yet</small>
                                        </div>
                                    @endforelse
                                </div>
                                @if(auth()->user()->notifications()->count() > 0)
                                    <div class="dropdown-footer text-center border-top">
                                        <a href="{{ route('jobseeker.notifications.index') }}" class="btn btn-sm btn-link text-decoration-none">View all notifications</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button class="btn btn-link text-dark position-relative" id="darkModeToggle" type="button">
                            <i class="bi bi-moon-fill" id="darkModeIcon"></i>
                        </button>

                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-link text-dark dropdown-toggle d-flex align-items-center gap-2 text-decoration-none" type="button" data-bs-toggle="dropdown">
                                @if($photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($photo))
                                    <img src="{{ Storage::url($photo) }}" alt="" class="rounded-circle" style="width:32px;height:32px;object-fit:cover">
                                @else
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:13px;font-weight:600">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('jobseeker.profile.index') }}">
                                        <i class="bi bi-person me-2"></i> My Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('jobseeker.applications.index') }}">
                                        <i class="bi bi-file-text me-2"></i> My Applications
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-left me-2"></i> Sign Out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="content-wrapper p-3 p-lg-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-circle-fill fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm" role="alert">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div>{{ session('info') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="main-footer bg-white border-top py-3 px-4 text-center text-muted small">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Municipal PESO - Tagudin, Ilocos Sur. All rights reserved.
            </footer>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.getElementById('sidebarToggle');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    if (window.innerWidth <= 768) {
                        sidebar.classList.toggle('show');
                        overlay.classList.toggle('show');
                    }
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }

            // Dark Mode Toggle
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            const html = document.documentElement;

            if (localStorage.getItem('theme') === 'dark') {
                html.setAttribute('data-bs-theme', 'dark');
                darkModeIcon?.classList.replace('bi-moon-fill', 'bi-sun-fill');
            }

            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    const currentTheme = html.getAttribute('data-bs-theme');
                    if (currentTheme === 'dark') {
                        html.setAttribute('data-bs-theme', 'light');
                        localStorage.setItem('theme', 'light');
                        darkModeIcon?.classList.replace('bi-sun-fill', 'bi-moon-fill');
                    } else {
                        html.setAttribute('data-bs-theme', 'dark');
                        localStorage.setItem('theme', 'dark');
                        darkModeIcon?.classList.replace('bi-moon-fill', 'bi-sun-fill');
                    }
                });
            }

            // Mark all notifications as read
            const markAllBtn = document.querySelector('.mark-all-read');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetch('{{ route("jobseeker.notifications.mark-all-read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            document.querySelectorAll('.notification-item.unread').forEach(el => {
                                el.classList.remove('unread');
                            });
                            document.querySelectorAll('.notification-badge').forEach(el => {
                                el.remove();
                            });
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
