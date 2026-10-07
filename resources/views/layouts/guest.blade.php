<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EMPLOYEASE') }} - PESO Tagudin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/responsive.css', 'resources/js/app.js'])

    <style>
        /* ===== GUEST LAYOUT — clean, light header ===== */
        .guest-header {
            background: #fff;
            border-bottom: 1px solid rgba(10, 37, 64, .07);
            box-shadow: 0 1px 0 rgba(10, 37, 64, .03);
            padding: .875rem 0;
        }

        .guest-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .guest-brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0A2540, #1E3A5F);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            box-shadow: 0 4px 10px rgba(10, 37, 64, .2);
            flex-shrink: 0;
        }

        .guest-brand-wordmark {
            color: var(--brand-navy);
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: .4px;
            line-height: 1;
        }

        .guest-brand-badge {
            background: rgba(227, 160, 8, .12);
            border: 1px solid rgba(201, 151, 0, .28);
            color: var(--brand-gold-ink);
            font-size: .6rem;
            font-weight: 600;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: .3rem .55rem;
            border-radius: 999px;
            white-space: nowrap;
        }

        @media (max-width: 480px) {
            .guest-header {
                padding: .75rem 0;
            }

            .guest-brand-badge {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Top Brand Header -->
    <header class="guest-header">
        <div class="container">
            <a class="guest-brand" href="{{ url('/') }}">
                <span class="guest-brand-mark"><i class="bi bi-briefcase-fill"></i></span>
                <span class="guest-brand-wordmark">{{ config('app.name', 'EMPLOYEASE') }}</span>
                <span class="guest-brand-badge">PESO Tagudin</span>
            </a>
        </div>
    </header>

    {{ $slot }}
</body>
</html>
