<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Presensi Karyawan')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --biru-utama: #2563eb;
            --biru-gelap: #1d4ed8;
            --biru-muda: #eff6ff;
        }
        body {
            background-color: #f4f6fb;
            font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
        }
        .app-shell {
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            background: white;
            box-shadow: 0 0 24px rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
        }
        .app-header {
            background: var(--biru-utama);
            color: white;
            padding: 16px 20px;
            border-radius: 0 0 18px 18px;
        }
        .btn-biru {
            background: var(--biru-utama);
            border-color: var(--biru-utama);
            color: white;
        }
        .btn-biru:hover {
            background: var(--biru-gelap);
            border-color: var(--biru-gelap);
            color: white;
        }
        .card-absen {
            border-radius: 16px;
            border: 1px solid #e5e9f2;
        }
        .menu-grid-item {
            text-align: center;
            text-decoration: none;
            color: #333;
            font-size: 12px;
        }
        .menu-grid-item .icon-circle {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 6px;
            font-size: 20px;
            color: white;
        }
        .bottom-nav {
            border-top: 1px solid #e5e9f2;
            background: white;
            padding: 8px 0;
        }
        .bottom-nav a {
            text-decoration: none;
            color: #9aa2b1;
            font-size: 11px;
            text-align: center;
            display: block;
        }
        .bottom-nav a.active {
            color: var(--biru-utama);
        }
        .bottom-nav i {
            display: block;
            font-size: 20px;
            margin-bottom: 2px;
        }
        #reader {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="app-shell">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
