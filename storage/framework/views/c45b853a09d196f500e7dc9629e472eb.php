<?php $__env->startSection('title', 'Bảng Điều Khiển Quản Trị'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $categories = $categories ?? \App\Models\Category::all();
    $communes = $communes ?? \App\Models\Commune::all();
?>

<!-- GOOGLE DRIVE STORAGE STATUS BANNER -->
<?php if(empty(env('GOOGLE_DRIVE_CLIENT_ID')) || empty(env('GOOGLE_DRIVE_CLIENT_SECRET')) || empty(env('GOOGLE_DRIVE_REFRESH_TOKEN'))): ?>
    <div class="admin-alert admin-alert-warning">
        <span style="font-size: 1.25rem;">☁️</span>
        <div>
            <strong style="color: #92400e;">Chế độ Giả Lập lưu trữ Google Drive đang hoạt động!</strong><br>
            Tất cả ảnh/video đăng tải sẽ được lưu tạm tại thư mục cục bộ (`public/uploads`) để bảo vệ dữ liệu. Hãy cấu hình các biến môi trường sau trong file `.env` để kích hoạt ổ đĩa Google Drive thực tế (0MB server space):<br>
            <code style="background: rgba(0,0,0,0.05); padding: 2px 6px; border-radius: 4px; color: #92400e; font-family: monospace; display: inline-block; margin-top: 4px; font-size: 0.8rem; font-weight: bold;">GOOGLE_DRIVE_CLIENT_ID</code>, 
            <code style="background: rgba(0,0,0,0.05); padding: 2px 6px; border-radius: 4px; color: #92400e; font-family: monospace; display: inline-block; margin-top: 4px; font-size: 0.8rem; font-weight: bold;">GOOGLE_DRIVE_CLIENT_SECRET</code>, 
            <code style="background: rgba(0,0,0,0.05); padding: 2px 6px; border-radius: 4px; color: #92400e; font-family: monospace; display: inline-block; margin-top: 4px; font-size: 0.8rem; font-weight: bold;">GOOGLE_DRIVE_REFRESH_TOKEN</code>
        </div>
    </div>
<?php else: ?>
    <div class="admin-alert admin-alert-success">
        <span style="font-size: 1.25rem;">⚡</span>
        <div>
            <strong>Đã kết nối lưu trữ đám mây Google Drive thành công!</strong> All uploaded images and videos are streamed dynamically with 0MB server space cost.
        </div>
    </div>
<?php endif; ?>

<!-- SUCCESS ALERT BANNER -->
<?php if(session('success')): ?>
    <div class="admin-alert admin-alert-success">
        <span>🎉</span>
        <div>
            <strong>Thành công!</strong> <?php echo e(session('success')); ?>

        </div>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="admin-alert admin-alert-warning" style="background-color: #fee2e2; border-color: #fecaca; color: #b91c1c;">
        <span>⚠️</span>
        <div>
            <strong>Lỗi!</strong> <?php echo e(session('error')); ?>

        </div>
    </div>
<?php endif; ?>

<!-- Welcome Header Card -->
<div class="admin-welcome-banner">
    <div>
        <h1>Chào mừng tới Kênh Quản trị 🏰</h1>
        <p>Tìm kiếm địa điểm cơ sở và click nút "Quản lý" để điều khiển thực đơn, tọa độ bản đồ, video review và chứng chỉ an toàn vệ sinh thực phẩm.</p>
    </div>
    <div style="font-size: 2.5rem; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.12));">⚙️</div>
</div>

<!-- Stats Grid Widgets -->
<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div>
            <div class="admin-stat-val"><?php echo e($stats['total_eateries']); ?></div>
            <div class="admin-stat-lbl">TỔNG ĐỊA ĐIỂM</div>
        </div>
        <span class="admin-stat-icon">📍</span>
    </div>

    <div class="admin-stat-card">
        <div>
            <div class="admin-stat-val"><?php echo e($stats['total_categories']); ?></div>
            <div class="admin-stat-lbl">DANH MỤC DỊCH VỤ</div>
        </div>
        <span class="admin-stat-icon">🍔</span>
    </div>

    <div class="admin-stat-card">
        <div>
            <div class="admin-stat-val"><?php echo e($stats['total_communes']); ?></div>
            <div class="admin-stat-lbl">XÃ / THỊ TRẤN SỐ HÓA</div>
        </div>
        <span class="admin-stat-icon">🌾</span>
    </div>

    <div class="admin-stat-card">
        <div>
            <div class="admin-stat-val"><?php echo e($stats['total_reviews']); ?></div>
            <div class="admin-stat-lbl">TỔNG SỐ ĐÁNH GIÁ</div>
        </div>
        <span class="admin-stat-icon">💬</span>
    </div>
</div>

<!-- Main Workspace Card: Search & Eateries List -->
<div class="admin-card">
    <div class="admin-card-header" style="flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <h2 class="admin-card-title" style="margin-bottom: 0;">
            <span>📋</span> Cơ Sở Dịch Vụ Trên Bản Đồ Số
        </h2>
        <?php if(session('user_role') === 'admin' || (session('user_role') === 'seller' && $eateries->count() === 0)): ?>
        <a href="/admin/eateries/create" class="btn-admin btn-admin-primary" style="font-size: 0.8rem; padding: 8px 16px;">
            <span>➕</span> Đăng ký địa điểm mới
        </a>
        <?php endif; ?>
    </div>

    <!-- Premium CabaFood Style Search & Filters Bar -->
    <div style="display: flex; gap: 12px; margin-bottom: 24px; align-items: center; flex-wrap: wrap; background-color: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid var(--admin-border);">
        <div style="flex: 2; min-width: 260px; position: relative;">
            <input type="text" id="eaterySearchInput" class="admin-form-input" placeholder="🔍 Tìm theo tên cơ sở, địa chỉ hoặc số điện thoại..." style="padding-left: 14px;">
        </div>
        <div style="flex: 1; min-width: 160px;">
            <select id="categoryFilter" class="admin-form-input">
                <option value="">Tất cả danh mục</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->name); ?>"><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 160px;">
            <select id="communeFilter" class="admin-form-input">
                <option value="">Tất cả khu vực xã</option>
                <?php $__currentLoopData = $communes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $com): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($com->name); ?>"><?php echo e($com->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="button" onclick="resetFilters()" class="btn-admin btn-admin-secondary" style="padding: 10px 16px;">
            🔄 Reset
        </button>
    </div>

    <!-- Eateries Table List -->
    <?php if($eateries->count() > 0): ?>
        <div class="admin-table-container">
            <table class="admin-data-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Ảnh</th>
                        <th>Tên địa điểm</th>
                        <th>Phân loại</th>
                        <th>Xã / Khu vực</th>
                        <th>Điện thoại</th>
                        <th>Đánh giá</th>
                        <th style="text-align: center; width: 220px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $eateries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="eatery-table-row" 
                            data-name="<?php echo e($eat->name); ?>" 
                            data-address="<?php echo e($eat->address); ?>" 
                            data-phone="<?php echo e($eat->phone ?: ''); ?>"
                            data-category="<?php echo e($eat->category->name); ?>" 
                            data-commune="<?php echo e($eat->commune->name); ?>">
                            <td>
                                <img src="<?php echo e($eat->image_path ?: 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=300&q=80'); ?>" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid var(--admin-border);">
                            </td>
                            <td>
                                <strong style="color: var(--admin-text-main); font-size: 0.92rem; display: block;"><?php echo e($eat->name); ?></strong>
                                <span style="font-size: 0.76rem; color: var(--admin-text-muted); display: block; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">📍 <?php echo e($eat->address); ?></span>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-primary"><?php echo e($eat->category->icon); ?> <?php echo e($eat->category->name); ?></span>
                            </td>
                            <td>
                                <span style="font-size: 0.88rem; font-weight: 600; color: var(--admin-text-main);">Xã <?php echo e($eat->commune->name); ?></span>
                            </td>
                            <td>
                                <span style="font-size: 0.88rem; font-weight: 700; color: var(--admin-primary);"><?php echo e($eat->phone ?: 'Chưa có'); ?></span>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; display: flex; flex-direction: column;">
                                    <span style="color: var(--admin-warning); font-weight: 800;">★ <?php echo e(number_format($eat->average_rating, 1)); ?></span>
                                    <span style="font-size: 0.72rem; color: var(--admin-text-muted);">(<?php echo e($eat->reviews->count()); ?> reviews)</span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    <a href="/dia-diem/dac-san/<?php echo e($eat->slug); ?>" target="_blank" class="btn-admin btn-admin-secondary" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 6px;" title="Xem trang bản đồ khách hàng">
                                        Xem Map
                                    </a>
                                    
                                    <!-- Dynamic Purple CabaFood-style Quản Lý Button -->
                                    <a href="/admin/eateries/<?php echo e($eat->id); ?>/edit" class="btn-admin btn-admin-primary" style="padding: 6px 14px; font-size: 0.78rem; border-radius: 6px; background-color: var(--admin-primary);">
                                        ⚙️ Quản lý
                                    </a>
                                    
                                    <?php if(session('user_role') === 'admin'): ?>
                                    <form action="/admin/eateries/<?php echo e($eat->id); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa điểm này khỏi Bản đồ số Đông Anh không?')" style="display: inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-admin btn-admin-danger" style="padding: 6px 8px; font-size: 0.75rem; border-radius: 6px;">
                                            Xóa
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px 0; color: var(--admin-text-muted);">
            <p style="font-size: 1rem; margin-bottom: 12px;">📭 Chưa có địa điểm nào được ghim lên bản đồ số.</p>
            <?php if(session('user_role') === 'seller'): ?>
            <p style="font-size: 0.88rem; color: var(--admin-text-muted); max-width: 480px; margin: 0 auto 16px; line-height: 1.6;">Tài khoản của bạn chưa liên kết với địa điểm kinh doanh nào. Hãy đăng ký thông tin quán của bạn để bắt đầu quản lý thực đơn và video review!</p>
            <a href="/admin/eateries/create" class="btn-admin btn-admin-primary">➕ Đăng ký quán kinh doanh của tôi</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("eaterySearchInput");
        const categoryFilter = document.getElementById("categoryFilter");
        const communeFilter = document.getElementById("communeFilter");
        const tableRows = document.querySelectorAll(".eatery-table-row");

        function filterTable() {
            const query = searchInput.value.toLowerCase().trim();
            const selectedCat = categoryFilter.value.toLowerCase();
            const selectedCom = communeFilter.value.toLowerCase();

            tableRows.forEach(row => {
                const name = row.getAttribute("data-name").toLowerCase();
                const address = row.getAttribute("data-address").toLowerCase();
                const phone = row.getAttribute("data-phone").toLowerCase();
                const category = row.getAttribute("data-category").toLowerCase();
                const commune = row.getAttribute("data-commune").toLowerCase();

                const matchesQuery = name.includes(query) || address.includes(query) || phone.includes(query);
                const matchesCategory = selectedCat === "" || category.includes(selectedCat);
                const matchesCommune = selectedCom === "" || commune.includes(selectedCom);

                if (matchesQuery && matchesCategory && matchesCommune) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        if(searchInput) searchInput.addEventListener("input", filterTable);
        if(categoryFilter) categoryFilter.addEventListener("change", filterTable);
        if(communeFilter) communeFilter.addEventListener("change", filterTable);

        window.resetFilters = function() {
            if(searchInput) searchInput.value = "";
            if(categoryFilter) categoryFilter.value = "";
            if(communeFilter) communeFilter.value = "";
            filterTable();
        };
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FOODDA\FOOD_MAP\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>