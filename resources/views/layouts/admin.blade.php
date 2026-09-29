<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản lý – Radiant Hotel')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

    <div class="d-flex" id="wrapper">
        {{-- SIDEBAR --}}
        <div class="sidebar bg-dark text-white" id="sidebar">
            <div class="sidebar-header p-3 border-bottom border-secondary">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Radiant Hotel</h5>
                <small class="text-white-50">Hệ thống quản lý</small>
            </div>
            <nav class="nav flex-column p-2">
                <a class="nav-link sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>Tổng quan
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('admin.room-map') ? 'active' : '' }}"
                   href="{{ route('admin.room-map') }}">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Sơ đồ phòng
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}"
                   href="{{ route('admin.bookings.index') }}">
                    <i class="bi bi-calendar-check me-2"></i>Đặt phòng
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('admin.rooms*') ? 'active' : '' }}"
                   href="{{ route('admin.rooms.index') }}">
                    <i class="bi bi-door-open me-2"></i>Quản lý phòng
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('admin.room-types*') ? 'active' : '' }}"
                   href="{{ route('admin.room-types.index') }}">
                    <i class="bi bi-tags me-2"></i>Hạng / Loại phòng
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('admin.services*') ? 'active' : '' }}"
                   href="{{ route('admin.services.index') }}">
                    <i class="bi bi-bell me-2"></i>Dịch vụ phụ
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('admin.invoices*') ? 'active' : '' }}"
                   href="{{ route('admin.invoices.index') }}">
                    <i class="bi bi-receipt me-2"></i>Hoá đơn
                </a>
                <a class="nav-link sidebar-link" href="{{ route('home') }}">
                    <i class="bi bi-globe me-2"></i>Xem trang chủ
                </a>
                <hr class="border-secondary">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-link sidebar-link text-danger border-0 bg-transparent w-100 text-start" type="submit">
                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                    </button>
                </form>
            </nav>
        </div>

        {{-- NỘI DUNG CHÍNH --}}
        <div class="flex-grow-1">
            {{-- TOP BAR --}}
            <nav class="navbar navbar-light bg-white shadow-sm px-4">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="toggleSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-text ms-auto">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ auth()->user()->name ?? 'Admin' }}
                    <span class="badge bg-primary ms-1">{{ auth()->user()->role->display_name ?? '' }}</span>
                </span>
            </nav>

            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar trên mobile
        document.getElementById('toggleSidebar')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
