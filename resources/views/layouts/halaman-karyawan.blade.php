@extends('layouts.app')

@section('content')
<style>
    html, body { margin: 0; padding: 0; width: 100%; overflow-x: hidden; }
    body { background-color: #f1f5f9; font-family: 'Inter', 'Segoe UI', sans-serif; }
    .app-shell { max-width: none; margin: 0; box-shadow: none; padding-bottom: 0; }

    .mobile-shell { width: 100%; background: #fff; min-height: 100vh; padding-bottom: 90px; }
    .mobile-shell .app-header {
        background: #fff; padding: 1.1rem 1rem; border-bottom: 1px solid #e2e8f0;
        position: sticky; top: 0; z-index: 10; display: flex; align-items: center; gap: 12px;
    }
    .mobile-shell .app-header .back-btn {
        width: 38px; height: 38px; border-radius: 12px; background: #f1f5f9;
        display: flex; align-items: center; justify-content: center; color: #475569; text-decoration: none; flex-shrink: 0;
    }
    .mobile-shell .app-header h6 { margin: 0; font-weight: 800; color: #1e293b; }

    .desktop-shell { display: none; }
    @media (min-width: 992px) {
        .desktop-shell { display: flex; min-height: 100vh; background: #f8fafc; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .desktop-main { flex-grow: 1; padding: 1.75rem 2rem; max-width: 980px; }
        .page-header-row { margin-bottom: 1.5rem; }
        .page-header-row h4 { font-weight: 800; color: #1e293b; margin-bottom: 2px; }
        .page-header-row p { color: #94a3b8; font-size: .85rem; margin: 0; }
    }
</style>

{{-- HP --}}
<div class="mobile-shell d-lg-none">
    <div class="app-header">
        <a href="{{ route('dashboard.karyawan') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <h6>@yield('judul')</h6>
    </div>
    <div class="p-3">
        @yield('konten')
    </div>
    @include('partials.bottom-nav-karyawan')
</div>

{{-- Desktop --}}
<div class="desktop-shell">
    @include('partials.sidebar-karyawan')

    <main class="desktop-main">
        <div class="page-header-row">
            <h4>@yield('judul')</h4>
            <p>@yield('subjudul')</p>
        </div>
        @yield('konten')
    </main>
</div>
@endsection