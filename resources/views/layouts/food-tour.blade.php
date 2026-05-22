<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <script>
        document.documentElement.setAttribute('data-theme', 'light');
    </script>
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Hành trình Ẩm thực Đông Anh - Dong Anh Food Map')</title>
    <meta name="description" content="@yield('meta_description', 'Khám phá hành trình ẩm thực, văn hóa và lịch sử Đông Anh với bản đồ số Cinematic thông minh.')">
    
    <!-- Leaflet.js Map Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <!-- Custom Theme Styling -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- Mobile Native Overrides (Only load for mobile) -->
    <link rel="stylesheet" media="screen and (max-width: 992px)" href="{{ asset('css/mobile-native.css') }}?v={{ time() }}">
    
    @yield('styles')
</head>
<body style="min-height: 100vh; display: flex; flex-direction: column; background: var(--bg-base); color: var(--text-main); font-family: var(--font-body); margin: 0; padding: 0;">

    <!-- Sticky Glass Navigation Header -->
    <header class="glass-nav">
        <div class="container nav-wrapper">
            <a href="/" class="logo">
                <span>🍜</span> Dong Anh Food Map
            </a>
            
            <div style="display: flex; align-items: center; gap: 16px;">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
            
            <div class="nav-collapse" id="navCollapse">
                <nav>
                <ul class="nav-menu">
                    <li><a href="/" class="nav-link {{ request()->is('/') && !request()->has('cat') ? 'active' : '' }}">Trang chủ</a></li>
                    <li><a href="/tim-kiem" class="nav-link {{ request()->is('tim-kiem*') ? 'active' : '' }}">Bản đồ & Tìm kiếm</a></li>
                    <li><a href="/food-tours" class="nav-link {{ request()->is('food-tours*') || (request()->is('food-tour*') && !request()->is('food-tour/tu-tay-lam-dac-san-co-loa*')) ? 'active' : '' }}">Food Tour</a></li>
                    <li><a href="/cooking-tours" class="nav-link {{ request()->is('cooking-tours*') || request()->is('food-tour/tu-tay-lam-dac-san-co-loa*') ? 'active' : '' }}">Trải nghiệm & Tự nấu</a></li>
                    <li><a href="/?cat=dac-san-dia-phuong" class="nav-link {{ request()->query('cat') === 'dac-san-dia-phuong' ? 'active' : '' }}">Tinh hoa bản địa</a></li>
                    @if(session('user_role') === 'admin')
                        <li><a href="/admin/dashboard" class="nav-link {{ request()->is('admin*') ? 'active' : '' }}">Trang quản trị</a></li>
                    @elseif(session('user_role') === 'seller')
                        <li><a href="/admin/dashboard" class="nav-link {{ request()->is('admin*') ? 'active' : '' }}">Quản lý quán</a></li>
                    @endif
                </ul>
            </nav>
            
            <div class="user-actions">

                @if(session()->has('user_id'))
                    <span style="font-size: 0.9rem; color: var(--text-muted);">
                        Chào, <strong style="color: var(--primary);">{{ session('user_name') }}</strong>
                        @if(session('user_role') === 'admin')
                            (Admin)
                        @elseif(session('user_role') === 'seller')
                            (Chủ quán)
                        @endif
                    </span>
                    <form action="/auth/logout" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-secondary" style="padding: 6px 14px; font-size: 0.85rem; border-radius: 8px;">Đăng xuất</button>
                    </form>
                @endif
            </div>
            
            </div> <!-- End nav-collapse -->
        </div>
    </header>

    <!-- Main Content Area -->
    <main style="flex: 1; display: flex; flex-direction: column; width: 100%;">
        @yield('content')
    </main>

    <!-- Optional Footer Slot (Rendered only on list pages, omitted on fullscreen maps) -->
    @yield('footer')

    <!-- Leaflet.js Map Library -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Hamburger Menu Logic
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const navCollapse = document.getElementById('navCollapse');
            
            if (mobileMenuBtn && navCollapse) {
                mobileMenuBtn.addEventListener('click', function() {
                    this.classList.toggle('open');
                    navCollapse.classList.toggle('show');
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
