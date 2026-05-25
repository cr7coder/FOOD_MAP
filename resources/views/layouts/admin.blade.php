<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <script>
        // Force clean light theme for admin panel matching CabaFood Mockup
        document.documentElement.setAttribute('data-theme', 'light');
    </script>
    
    <title>@yield('title', 'Kênh Quản trị - Dong Anh Food Map')</title>
    
    <!-- Leaflet.js Map Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <!-- Dedicated Admin Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
</head>
<body>

    <div class="admin-layout-wrapper">
        
        <!-- ==========================================================================
             LEFT COLUMN: FIXED PURPLE SIDEBAR
             ========================================================================== -->
        <aside class="admin-sidebar-nav">
            <a href="/admin/dashboard" class="admin-sidebar-logo">
                <span>🏰</span> Đông Anh Food Map
            </a>
            
            <div class="admin-sidebar-section-title">Tổng quan hệ thống</div>
            <a href="/admin/dashboard" class="admin-menu-item {{ request()->is('admin/dashboard') && !isset($eatery) ? 'active' : '' }}">
                <span>📊</span> Dashboard Thống Kê
            </a>
            
            @if(isset($eatery) && $eatery)
            <div class="admin-sidebar-section-title">Đang điều phối</div>
            <a href="/admin/eateries/{{ $eatery->id }}/edit" class="admin-menu-item active">
                <span>⚙️</span> {{ Str::limit($eatery->name, 18, '...') }}
            </a>
            @endif

            <div class="admin-sidebar-section-title">Quản lý cơ sở</div>
            @if(session('user_role') === 'admin' || (session('user_role') === 'seller' && \App\Models\Eatery::where('user_id', session('user_id'))->count() === 0))
            <a href="/admin/eateries/create" class="admin-menu-item {{ request()->is('admin/eateries/create') ? 'active' : '' }}">
                <span>➕</span> Đăng ký địa điểm
            </a>
            @endif
            
            <div class="admin-sidebar-divider"></div>
            
            <div class="admin-sidebar-section-title">Cổng thông tin</div>
            <a href="/" class="admin-menu-item" target="_blank">
                <span>🗺️</span> Bản đồ Khách hàng
            </a>
        </aside>

        <!-- ==========================================================================
             RIGHT COLUMN: CONTENT WRAPPER
             ========================================================================== -->
        <div class="admin-main-wrapper">
            
            <!-- Top Navigation Bar -->
            <header class="admin-topbar">
                <div class="admin-breadcrumbs">
                    <a href="/admin/dashboard">Admin Panel</a>
                    <span class="separator">/</span>
                    <span>@yield('title', 'Bảng điều khiển')</span>
                </div>
                
                <div class="admin-topbar-actions">
                    <!-- Light/Dark Mode Toggle Mockup Icon -->
                    <button class="admin-icon-btn" title="Chuyển giao diện tối" onclick="alert('Tính năng giao diện tối đang được phát triển!')">
                        🌙
                    </button>
                    
                    <!-- Notification Bell Mockup Icon -->
                    <button class="admin-icon-btn" title="Thông báo hệ thống">
                        🔔
                        <span class="admin-icon-btn-badge"></span>
                    </button>
                    
                    <!-- User Dropdown Details -->
                    @if(session()->has('user_id'))
                        <div class="admin-user-profile">
                            <div class="admin-user-avatar">
                                {{ mb_substr(session('user_name'), 0, 1, 'UTF-8') }}
                            </div>
                            <div style="text-align: left;">
                                <span class="admin-user-name">{{ session('user_name') }}</span>
                                <span class="admin-user-role">{{ session('user_role') === 'admin' ? 'Administrator' : 'Chủ cửa hàng' }}</span>
                            </div>
                            
                            <form action="/auth/logout" method="POST" style="margin-left: 12px;">
                                @csrf
                                <button type="submit" class="btn-admin btn-admin-danger" style="padding: 6px 12px; font-size: 0.72rem; border-radius: 8px;">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </header>

            <!-- Main Yield Content -->
            <main class="admin-content-body">
                @yield('content')
            </main>

            <!-- Corporate Admin Footer -->
            <footer style="background-color: #ffffff; border-top: 1px solid var(--admin-border); padding: 18px 0; text-align: center; font-size: 0.8rem; color: var(--admin-text-muted);">
                &copy; 2026 Kênh Quản trị Bản đồ số Đông Anh (Dong Anh Food Map). Tất cả quyền được bảo lưu.
            </footer>
            
        </div>
        
    </div>

    <!-- Leaflet.js Map Library -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    @yield('scripts')
</body>
</html>
