<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <script>
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    
    <title><?php echo $__env->yieldContent('title', 'Kênh Quản trị - Dong Anh Food Map'); ?></title>
    
    <!-- Leaflet.js Map Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <!-- Custom Theme Styling -->
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
</head>
<body style="min-height: 100vh; display: flex; flex-direction: column; background: var(--bg-base); color: var(--text-main); font-family: var(--font-body); margin: 0; padding: 0;">

    <!-- Sticky Glass Navigation Header for Admin -->
    <header class="glass-nav" style="border-bottom: 1.5px solid rgba(255, 126, 41, 0.25); background: rgba(9, 9, 11, 0.8);">
        <div class="container nav-wrapper">
            <a href="/admin/dashboard" class="logo">
                <span>🛠️</span> Kênh Quản Trị
            </a>
            
            <nav>
                <ul class="nav-menu">
                    <li><a href="/admin/dashboard" class="nav-link <?php echo e(request()->is('admin/dashboard') ? 'active' : ''); ?>">📊 Bảng điều khiển</a></li>
                    <li><a href="/admin/eateries/create" class="nav-link <?php echo e(request()->is('admin/eateries/create') ? 'active' : ''); ?>">➕ Thêm địa điểm</a></li>
                    <li><a href="/" class="nav-link" style="border: 1px dashed var(--primary); padding: 6px 14px; border-radius: 8px;">🗺️ Xem Bản đồ chính</a></li>
                </ul>
            </nav>
            
            <div class="user-actions">

                <?php if(session()->has('user_id')): ?>
                    <span style="font-size: 0.9rem; color: var(--text-muted);">
                        Chào, <strong style="color: var(--primary);"><?php echo e(session('user_name')); ?></strong> 
                        (<span style="color: var(--accent);"><?php echo e(session('user_role') === 'admin' ? 'Admin' : 'Chủ quán'); ?></span>)
                    </span>
                    <form action="/auth/logout" method="POST" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-secondary" style="padding: 6px 14px; font-size: 0.85rem; border-radius: 8px;">Đăng xuất</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main style="flex: 1;">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Simple Admin Footer -->
    <footer style="background: #09090b; border-top: 1px solid var(--border-glow); padding: 25px 0; text-align: center; font-size: 0.8rem; color: rgba(255,255,255,0.3); margin-top: 60px;">
        <div class="container">
            &copy; 2026 Kênh Quản trị Bản đồ số Đông Anh (Dong Anh Food Map). Tất cả quyền được bảo lưu.
        </div>
    </footer>

    <!-- Leaflet.js Map Library -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            if (themeToggleBtn) {
                const updateToggleIcon = (theme) => {
                    themeToggleBtn.innerHTML = theme === 'light' ? '🌙' : '☀️';
                };
                
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                updateToggleIcon(currentTheme);
                
                themeToggleBtn.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
                    
                    document.documentElement.setAttribute('data-theme', activeTheme);
                    localStorage.setItem('theme', activeTheme);
                    updateToggleIcon(activeTheme);
                    
                    const event = new CustomEvent('theme-changed', { detail: { theme: activeTheme } });
                    document.dispatchEvent(event);
                });
            }
        });
    </script>
    
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\FOODDA\FOOD_MAP\resources\views/layouts/admin.blade.php ENDPATH**/ ?>