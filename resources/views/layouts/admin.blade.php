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
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="main-sidebar" id="sidebar">
            <div class="sidebar-brand">
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                    <div class="brand-icon">
                        <img src="{{ asset('images/employease-mark.webp') }}" alt="EmployEase logo" width="38" height="38">
                    </div>
                    <div class="brand-text">
                        <span class="brand-name">EMPLOYEASE</span>
                        <small class="brand-subtitle">PESO Management</small>
                    </div>
                </a>
            </div>

            <div class="sidebar-user px-3 py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <p class="mb-0 fw-semibold text-truncate">{{ auth()->user()->name }}</p>
                        <small class="text-muted">Administrator</small>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span>Management</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.job-vacancies.index') }}" class="nav-link {{ request()->routeIs('admin.job-vacancies.*') && !request()->routeIs('admin.job-vacancies.archived') ? 'active' : '' }}">
                            <i class="bi bi-briefcase-fill"></i>
                            <span>Job Vacancies</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.job-vacancies.archived') }}" class="nav-link {{ request()->routeIs('admin.job-vacancies.archived') ? 'active' : '' }}">
                            <i class="bi bi-archive-fill"></i>
                            <span>Archived Jobs</span>
                            @php $archivedVacancies = \Illuminate\Support\Facades\Cache::remember('sidebar.archived.vacancies', 60, fn () => \App\Models\JobVacancy::archived()->count()); @endphp
                            @if($archivedVacancies > 0)
                                <span class="badge bg-secondary rounded-pill ms-auto">{{ $archivedVacancies }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.applicants.index') }}" class="nav-link {{ request()->routeIs('admin.applicants.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span>Applicants</span>
                            @php $pendingCount = \Illuminate\Support\Facades\Cache::remember('sidebar.pending.applications', 60, fn () => \App\Models\Application::where('status', 'pending')->count()); @endphp
                            @if($pendingCount > 0)
                                <span class="badge bg-brand-gold rounded-pill ms-auto">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.interviews.index') }}" class="nav-link {{ request()->routeIs('admin.interviews.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-event-fill"></i>
                            <span>Interviews</span>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span>Reports & Users</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-fill"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-person-badge-fill"></i>
                            <span>Job Seekers</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer mt-auto p-3 border-top">
                
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
                            <button class="btn btn-link position-relative notification-bell p-2" type="button" data-bs-toggle="dropdown" id="notificationDropdown" aria-expanded="false">
                                <i class="bi bi-bell fs-5"></i>
                                @php
                                    $unreadCount = 5; // This would come from your notification model
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill notification-badge bg-danger" style="font-size:10px;">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow notification-dropdown" aria-labelledby="notificationDropdown">
                                <div class="dropdown-header d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                    <h6 class="mb-0 fw-semibold">Notifications</h6>
                                    @if($unreadCount > 0)
                                        <button class="btn btn-sm btn-link text-decoration-none p-0 mark-all-read text-primary" type="button" style="font-size: 0.75rem;">
                                            Mark all read
                                        </button>
                                    @endif
                                </div>
                                <div class="notification-list" style="max-height:350px;overflow-y:auto;">
                                    {{-- Sample notifications - replace with actual data --}}
                                    <a href="#" class="dropdown-item notification-item unread py-2 px-3">
                                        <div class="d-flex gap-3">
                                            <div class="notification-icon flex-shrink-0">
                                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                    <i class="bi bi-calendar-check fs-6"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <p class="mb-1 fw-semibold small text-dark">Interview Scheduled</p>
                                                <p class="mb-1 text-muted small">An interview has been scheduled for "Elementary School Teacher (Contractual)"</p>
                                                <small class="text-muted" style="font-size: 0.7rem;">October 07, 2026 11:00 AM</small>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="dropdown-item notification-item py-2 px-3">
                                        <div class="d-flex gap-3">
                                            <div class="notification-icon flex-shrink-0">
                                                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                    <i class="bi bi-person-check fs-6"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <p class="mb-1 fw-semibold small text-dark">Application Status Updated</p>
                                                <p class="mb-1 text-muted small">Application status changed for "Elementary School Teacher"</p>
                                                <small class="text-muted" style="font-size: 0.7rem;">October 07, 2026 11:05 AM</small>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="dropdown-item notification-item py-2 px-3">
                                        <div class="d-flex gap-3">
                                            <div class="notification-icon flex-shrink-0">
                                                <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                    <i class="bi bi-file-text fs-6"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <p class="mb-1 fw-semibold small text-dark">Application Status Updated</p>
                                                <p class="mb-1 text-muted small">Application reviewed for "Elementary School Teacher"</p>
                                                <small class="text-muted" style="font-size: 0.7rem;">October 07, 2026 10:30 AM</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="dropdown-footer text-center border-top pt-2 mt-2">
                                    <a href="#" class="btn btn-sm btn-link text-decoration-none">View all notifications</a>
                                </div>
                            </div>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button class="btn btn-link text-dark position-relative p-2" id="darkModeToggle" type="button">
                            <i class="bi bi-moon-fill" id="darkModeIcon"></i>
                        </button>

                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-link text-dark dropdown-toggle d-flex align-items-center gap-2 text-decoration-none p-2" type="button" data-bs-toggle="dropdown">
                                <div class="avatar avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:13px">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-grid me-2"></i> Dashboard
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
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm session-alert" role="alert" data-toast-type="success" data-toast-message="{{ session('success') }}">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm session-alert" role="alert" data-toast-type="error" data-toast-message="{{ session('error') }}">
                        <i class="bi bi-exclamation-circle-fill fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm session-alert" role="alert" data-toast-type="info" data-toast-message="{{ session('info') }}">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div>{{ session('info') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @hasSection('breadcrumbs')
                    <div class="mb-2">
                        @yield('breadcrumbs')
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

    <!-- Toast notifications -->
    <div id="appToastStack" class="app-toast-stack" aria-live="polite"></div>

    @stack('scripts')

    <script>
        // Sidebar Toggle
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
        });
    </script>
</body>
</html>
