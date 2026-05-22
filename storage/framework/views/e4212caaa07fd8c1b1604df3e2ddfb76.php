<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    
    <!-- SEO Meta Tags -->
    <title><?php echo $__env->yieldContent('title', 'Hành trình Ẩm thực Đông Anh - Dong Anh Food Map'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Khám phá hành trình ẩm thực, văn hóa và lịch sử Đông Anh với bản đồ số Cinematic thông minh.'); ?>">
    
    <!-- Leaflet.js Map Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <!-- Custom Theme Styling -->
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    
    <!-- Mobile Native Overrides (Only load for mobile) -->
    <link rel="stylesheet" media="screen and (max-width: 992px)" href="<?php echo e(asset('css/mobile-native.css')); ?>?v=<?php echo e(time()); ?>">
    
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body style="min-height: 100vh; display: flex; flex-direction: column; background: var(--bg-base); color: var(--text-main); font-family: var(--font-body); margin: 0; padding: 0;">

    <!-- Sticky Glass Navigation Header -->
    <header class="glass-nav">
        <div class="container nav-wrapper">
            <a href="/" class="logo">
                <span>🍜</span> Dong Anh Food Map
            </a>
            
            <div style="display: flex; align-items: center; gap: 16px;">
                <button id="themeToggleBtn" class="theme-btn mobile-hidden-theme" title="Chuyển giao diện sáng/tối" style="display: none;">
                    <!-- JS will fill icon -->
                </button>
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
            
            <div class="nav-collapse" id="navCollapse">
                <nav>
                <ul class="nav-menu">
                    <li><a href="/" class="nav-link <?php echo e(request()->is('/') && !request()->has('cat') ? 'active' : ''); ?>">Trang chủ</a></li>
                    <li><a href="/tim-kiem" class="nav-link <?php echo e(request()->is('tim-kiem*') ? 'active' : ''); ?>">Bản đồ & Tìm kiếm</a></li>
                    <li><a href="/food-tours" class="nav-link <?php echo e(request()->is('food-tours*') || (request()->is('food-tour*') && !request()->is('food-tour/tu-tay-lam-dac-san-co-loa*')) ? 'active' : ''); ?>">Food Tour</a></li>
                    <li><a href="/cooking-tours" class="nav-link <?php echo e(request()->is('cooking-tours*') || request()->is('food-tour/tu-tay-lam-dac-san-co-loa*') ? 'active' : ''); ?>">Trải nghiệm & Tự nấu</a></li>
                    <li><a href="/?cat=dac-san-dia-phuong" class="nav-link <?php echo e(request()->query('cat') === 'dac-san-dia-phuong' ? 'active' : ''); ?>">Tinh hoa bản địa</a></li>
                    <?php if(session('user_role') === 'admin'): ?>
                        <li><a href="/admin/dashboard" class="nav-link <?php echo e(request()->is('admin*') ? 'active' : ''); ?>">Trang quản trị</a></li>
                    <?php elseif(session('user_role') === 'seller'): ?>
                        <li><a href="/admin/dashboard" class="nav-link <?php echo e(request()->is('admin*') ? 'active' : ''); ?>">Quản lý quán</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <div class="user-actions">

                <?php if(session()->has('user_id')): ?>
                    <span style="font-size: 0.9rem; color: var(--text-muted);">
                        Chào, <strong style="color: var(--primary);"><?php echo e(session('user_name')); ?></strong>
                        <?php if(session('user_role') === 'admin'): ?>
                            (Admin)
                        <?php elseif(session('user_role') === 'seller'): ?>
                            (Chủ quán)
                        <?php endif; ?>
                    </span>
                    <form action="/auth/logout" method="POST" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-secondary" style="padding: 6px 14px; font-size: 0.85rem; border-radius: 8px;">Đăng xuất</button>
                    </form>
                <?php endif; ?>
                
                <button id="themeToggleBtnDesktop" class="theme-btn desktop-theme-btn" title="Chuyển giao diện sáng/tối">
                    <!-- JS will fill icon -->
                </button>
            </div>
            
            </div> <!-- End nav-collapse -->
        </div>
    </header>

    <!-- Main Content Area -->
    <main style="flex: 1; display: flex; flex-direction: column; width: 100%;">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Optional Footer Slot (Rendered only on list pages, omitted on fullscreen maps) -->
    <?php echo $__env->yieldContent('footer'); ?>

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

            // Theme Toggle Logic
            const themeBtns = document.querySelectorAll('.theme-btn');
            
            const updateToggleIcon = (theme) => {
                themeBtns.forEach(btn => {
                    btn.innerHTML = theme === 'light' ? '🌙' : '☀️';
                });
            };
            
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateToggleIcon(currentTheme);
            
            themeBtns.forEach(themeToggleBtn => {
                themeToggleBtn.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
                    
                    document.documentElement.setAttribute('data-theme', activeTheme);
                    localStorage.setItem('theme', activeTheme);
                    updateToggleIcon(activeTheme);
                    
                    // Dispatch custom event for Leaflet hot-swap theme
                    const event = new CustomEvent('theme-changed', { detail: { theme: activeTheme } });
                    document.dispatchEvent(event);
                });
            });
        });
    </script>
    
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\FOODDA\FOOD_MAP\resources\views/layouts/food-tour.blade.php ENDPATH**/ ?>