<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <script>
        // Force light theme
        document.documentElement.setAttribute('data-theme', 'light');
    </script>
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Bản đồ số Ẩm thực Đông Anh - Dong Anh Food Map')</title>
    <meta name="description" content="@yield('meta_description', 'Bản đồ số Ẩm thực Đông Anh - Số hóa toàn bộ nhà hàng, quán ăn, khách sạn và đặc sản tại Đông Anh. Chỉ đường nhanh, xem thực đơn đặc trưng, liên kết Google Maps.')">
    <meta name="keywords" content="ẩm thực đông anh, bản đồ ẩm thực, quán ngon đông anh, bún mạch tràng cổ loa, đặc sản đông anh, khách sạn đông anh, ăn gì đông anh">
    
    <!-- OpenGraph Social Tags -->
    <meta property="og:site_name" content="Dong Anh Food Map">
    <meta property="og:title" content="@yield('title', 'Bản đồ số Ẩm thực Đông Anh - Dong Anh Food Map')">
    <meta property="og:description" content="@yield('meta_description', 'Bản đồ số Ẩm thực Đông Anh - Số hóa nhà hàng, quán ăn, khách sạn và đặc sản.')">
    <meta property="og:image" content="@yield('og_image', 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=800&q=80')">
    <meta property="og:type" content="website">
    
    <!-- Leaflet.js Map Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <!-- Custom Theme Styling -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- Mobile Native Overrides (Only load for mobile) -->
    <link rel="stylesheet" media="screen and (max-width: 1200px)" href="{{ asset('css/mobile-native.css') }}?v={{ time() }}">
    
    <!-- Dynamic Schema.org JSON-LD Structured Data for Google Indexing -->
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Liquid Background Gradient Blobs -->
    <div class="liquid-bg-container">
        <div class="liquid-blob blob-1"></div>
        <div class="liquid-blob blob-2"></div>
        <div class="liquid-blob blob-3"></div>
    </div>

    <!-- Sticky Glass Navigation Header -->
    <header class="glass-nav">
        <div class="container nav-wrapper">
            <a href="/" class="logo">
                <span>🍜</span> Dong Anh Food Map
            </a>
            
            <div class="nav-collapse main-nav-container" id="navCollapse">
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
                    @else
                        <a href="/auth/login" class="btn-secondary" style="text-decoration: none; padding: 6px 14px; font-size: 0.85rem; border-radius: 8px;">Đăng nhập</a>
                        <a href="/auth/register" class="btn-primary" style="text-decoration: none; padding: 6px 14px; font-size: 0.85rem; border-radius: 8px;">Đăng ký</a>
                    @endif
                </div>
            </div> <!-- End nav-collapse -->

            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h3 class="logo" style="margin-bottom: 16px; font-size: 1.3rem;">🍜 Dong Anh Food Map</h3>
                    <p style="font-size: 0.85rem; line-height: 1.6; max-width: 480px;">
                        Bản đồ số Ẩm thực Đông Anh là giải pháp công nghệ số hóa toàn bộ quán ăn, nhà hàng, quán cafe, khách sạn, nhà nghỉ và quảng bá các món ăn đặc sản truyền thống của huyện Đông Anh, Hà Nội. Hỗ trợ chuyển đổi số và nâng tầm văn hóa du lịch địa phương.
                    </p>
                </div>
                <div>
                    <h4 style="color: var(--text-main); margin-bottom: 16px; font-size: 1rem;">Liên kết nhanh</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                        <li><a href="/" style="hover: color: var(--primary);">Trang chủ</a></li>
                        <li><a href="/tim-kiem" style="hover: color: var(--primary);">Bản đồ số</a></li>
                        <li><a href="/?cat=dac-san-dia-phuong" style="hover: color: var(--primary);">Đặc sản Cổ Loa & Đông Anh</a></li>
                        <li><a href="/auth/login" style="hover: color: var(--primary);">Đăng nhập quản trị viên</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="color: var(--text-main); margin-bottom: 16px; font-size: 1rem;">Liên hệ hỗ trợ</h4>
                    <p style="font-size: 0.85rem; line-height: 1.6;">
                        📍 TTHC xã Đông Anh,Thành phố Hà Nội
                        📞 Điện thoại: 024.3123.4567<br>
                        ✉️ Email: info@donganh.hanoi.gov.vn<br>
                        🌐 Website: donganh.hanoi.gov.vn
                    </p>
                </div>
            </div>
            <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px; text-align: center; font-size: 0.8rem; color: rgba(255,255,255,0.3);">
                &copy; 2026 Bản đồ số Ẩm thực Đông Anh (Dong Anh Food Map). Tất cả quyền được bảo lưu. Phát triển bởi Phòng Văn Xã Hội Xã Đông Anh
            </div>
        </div>
    </footer>

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
