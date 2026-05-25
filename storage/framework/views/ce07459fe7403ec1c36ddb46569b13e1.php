<?php $__env->startSection('title', ($eatery ? '⚙️ Quản lý: ' . $eatery->name : 'Thêm địa điểm mới')); ?>

<?php $__env->startSection('content'); ?>

<!-- Welcome Workspace Banner -->
<div class="admin-welcome-banner" style="margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 1.45rem;">🏢 <?php echo e($eatery ? $eatery->name : 'Thêm cơ sở mới'); ?></h1>
        <p><?php echo e($eatery ? 'Không gian làm việc & điều phối hồ sơ pháp lý cơ sở' : 'Khai báo hồ sơ ban đầu cho cơ sở kinh doanh mới'); ?></p>
    </div>
    <div style="font-size: 2rem;">⚙️</div>
</div>

<!-- Errors Alert Banner -->
<?php if($errors->any()): ?>
    <div class="admin-alert admin-alert-warning" style="background-color: #fee2e2; border-color: #fecaca; color: #b91c1c;">
        <div>
            <strong style="display: block; margin-bottom: 6px;">⚠️ Vui lòng hoàn thiện các trường thông tin hợp lệ:</strong>
            <ul style="padding-left: 20px; font-size: 0.85rem;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- Dynamic Animated Success Status Toast -->
<?php if(session('success')): ?>
    <div id="admin-success-toast" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.9); z-index: 9999; background: #0f172a; color: #ffffff; border-radius: 16px; padding: 24px 32px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.35); display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 14px; border: 1px solid rgba(255,255,255,0.1); opacity: 0; pointer-events: none; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); min-width: 320px; max-width: 90%;">
        <div style="background: #10b981; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);">✓</div>
        <div>
            <strong style="display: block; font-size: 1.05rem; color: #ffffff; margin-bottom: 4px;">Thao tác thành công!</strong>
            <span style="font-size: 0.85rem; color: #94a3b8; line-height: 1.4; display: block;"><?php echo e(session('success')); ?></span>
        </div>
        <button type="button" onclick="const t = document.getElementById('admin-success-toast'); t.style.opacity='0'; t.style.transform='translate(-50%, -50%) scale(0.9)'; t.style.pointerEvents='none';" class="btn-admin btn-admin-accent" style="padding: 8px 24px; font-size: 0.78rem; border-radius: 8px; margin-top: 6px; font-weight: bold; width: 100%;">Đồng ý</button>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toast = document.getElementById('admin-success-toast');
            if (toast) {
                setTimeout(() => {
                    toast.style.transform = 'translate(-50%, -50%) scale(1)';
                    toast.style.opacity = '1';
                    toast.style.pointerEvents = 'auto';
                }, 150);
                
                setTimeout(() => {
                    if (toast.style.opacity !== '0') {
                        toast.style.transform = 'translate(-50%, -50%) scale(0.9)';
                        toast.style.opacity = '0';
                        toast.style.pointerEvents = 'none';
                    }
                }, 4000); // Automatically fade out after 4s
            }
        });
    </script>
<?php endif; ?>

<!-- ==========================================================================
     SUB-TAB WORKSPACE SWITCHER
     ========================================================================== -->
<div class="admin-sub-tabs">
    <button type="button" class="admin-sub-tab-btn active" onclick="switchSubTab(event, 'tab-info')">
        1. Thông tin & Bản đồ
    </button>
    <?php if($eatery): ?>
    <button type="button" class="admin-sub-tab-btn" onclick="switchSubTab(event, 'tab-dishes')">
        2. Thực đơn món ngon (<?php echo e($eatery->dishes->count()); ?>)
    </button>
    <button type="button" class="admin-sub-tab-btn" onclick="switchSubTab(event, 'tab-videos')">
        3. Video Review của quán (<?php echo e($eatery->reviewVideos->count()); ?>)
    </button>
    <button type="button" class="admin-sub-tab-btn" onclick="switchSubTab(event, 'tab-attp')">
        4. Giấy VSATTP & Nhật ký
    </button>
    <button type="button" class="admin-sub-tab-btn" onclick="switchSubTab(event, 'tab-contracts')">
        5. Hợp đồng & Hóa đơn
    </button>
    <button type="button" class="admin-sub-tab-btn" onclick="switchSubTab(event, 'tab-reviews')">
        6. Đánh giá từ khách hàng (<?php echo e($eatery->reviews->count()); ?>)
    </button>
    <?php endif; ?>
</div>

<!-- ==========================================================================
     TAB 1: BASIC INFO & MAP COORDINATES PICKER
     ========================================================================== -->
<div id="tab-info" class="admin-tab-section" style="display: block;">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <span>📍</span> Hồ sơ & Định vị cơ sở GPS
            </h2>
        </div>
        
        <form action="<?php echo e($eatery ? '/admin/eateries/' . $eatery->id : '/admin/eateries'); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php if($eatery): ?>
                <?php echo method_field('PUT'); ?>
            <?php endif; ?>

            <div class="admin-split-layout">
                
                <!-- Left Details Column -->
                <div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Tên cơ sở / quán ăn / khách sạn <span style="color: var(--admin-danger);">*</span></label>
                        <input type="text" name="name" class="admin-form-input" required placeholder="Ví dụ: Bún chả Hùng Thái Cổ Loa" value="<?php echo e(old('name', $eatery ? $eatery->name : '')); ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Phân loại danh mục <span style="color: var(--admin-danger);">*</span></label>
                            <select name="category_id" class="admin-form-input" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id', $eatery ? $eatery->category_id : '') == $cat->id ? 'selected' : ''); ?>>
                                        <?php echo e($cat->icon); ?> <?php echo e($cat->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Địa bàn xã / Thị trấn <span style="color: var(--admin-danger);">*</span></label>
                            <select name="commune_id" class="admin-form-input" required>
                                <option value="">-- Chọn Xã --</option>
                                <?php $__currentLoopData = $communes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $com): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($com->id); ?>" <?php echo e(old('commune_id', $eatery ? $eatery->commune_id : '') == $com->id ? 'selected' : ''); ?>>
                                        📍 <?php echo e($com->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Địa chỉ chi tiết <span style="color: var(--admin-danger);">*</span></label>
                        <input type="text" name="address" class="admin-form-input" required placeholder="Ví dụ: Thôn Mạch Tràng, Xã Cổ Loa, Đông Anh" value="<?php echo e(old('address', $eatery ? $eatery->address : '')); ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Số điện thoại liên hệ</label>
                            <input type="text" name="phone" class="admin-form-input" placeholder="Ví dụ: 0987654321" value="<?php echo e(old('phone', $eatery ? $eatery->phone : '')); ?>">
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Giờ mở cửa</label>
                            <input type="text" name="opening_hours" class="admin-form-input" placeholder="Ví dụ: 06:00 - 22:00" value="<?php echo e(old('opening_hours', $eatery ? $eatery->opening_hours : '')); ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Mức giá tham khảo</label>
                            <input type="text" name="price_range" class="admin-form-input" placeholder="Ví dụ: 30.000đ - 80.000đ" value="<?php echo e(old('price_range', $eatery ? $eatery->price_range : '')); ?>">
                        </div>
                        <?php if(session('user_role') === 'admin'): ?>
                        <div class="admin-form-group" style="display: flex; align-items: center; padding-top: 32px;">
                            <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.88rem; font-weight: 700; color: var(--admin-text-main);">
                                <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured', $eatery ? $eatery->is_featured : false) ? 'checked' : ''); ?> style="width: 17px; height: 17px; accent-color: var(--admin-primary); cursor: pointer;">
                                ⭐ Đánh dấu địa điểm nổi bật
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Ảnh đại diện cơ sở (Upload)</label>
                        <input type="file" name="image" class="admin-form-input" accept="image/*" style="padding: 6px 12px;">
                        <?php if($eatery && $eatery->image_path): ?>
                            <span style="font-size: 0.75rem; color: var(--admin-text-muted); display: block; margin-top: 5px;">Ảnh hiện tại: <code><?php echo e($eatery->image_path); ?></code></span>
                        <?php endif; ?>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Hoặc Đường dẫn ảnh (URL)</label>
                        <input type="url" name="image_url" class="admin-form-input" placeholder="https://example.com/eatery.jpg" value="<?php echo e(old('image_url', $eatery && !Str::startsWith($eatery->image_path, '/uploads') ? $eatery->image_path : '')); ?>">
                    </div>
                </div>

                <!-- Right Map Picker Column -->
                <div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" style="display: flex; justify-content: space-between;">
                            <span>🔗 Tự động lấy tọa độ qua link Google Maps</span>
                        </label>
                        <div style="display: flex; gap: 8px;">
                            <input type="url" id="gmapsUrlInput" class="admin-form-input" placeholder="Dán liên kết Google Maps của quán vào đây..." style="flex: 1;">
                            <button type="button" id="btnExtractCoords" class="btn-admin btn-admin-accent">
                                ⚡ Giải mã GPS
                            </button>
                        </div>
                        <span id="gmapsHelperText" style="font-size: 0.76rem; display: block; margin-top: 6px; font-weight: 500;"></span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Vĩ độ (Latitude) <span style="color: var(--admin-danger);">*</span></label>
                            <input type="number" step="any" name="latitude" id="latInput" class="admin-form-input" required placeholder="Ví dụ: 21.1182" value="<?php echo e(old('latitude', $eatery ? $eatery->latitude : '')); ?>">
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Kinh độ (Longitude) <span style="color: var(--admin-danger);">*</span></label>
                            <input type="number" step="any" name="longitude" id="lngInput" class="admin-form-input" required placeholder="Ví dụ: 105.8394" value="<?php echo e(old('longitude', $eatery ? $eatery->longitude : '')); ?>">
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label class="admin-form-label" style="margin-bottom: 0;">🎯 Click chọn trực tiếp trên Bản đồ</label>
                            <?php if($eatery): ?>
                                <button type="button" id="btnMapLockToggle" class="btn-admin" style="padding: 4px 10px; font-size: 0.72rem; border-radius: 6px; background-color: var(--admin-danger-light); color: var(--admin-danger); border: 1.5px solid rgba(239, 68, 68, 0.15); display: inline-flex; align-items: center; gap: 4px;" onclick="toggleMapLock()">
                                    🔒 Đã khóa click (Bấm để sửa vị trí)
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="admin-map-picker" id="pickerMap"></div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Mô tả giới thiệu ngắn</label>
                        <textarea name="description" class="admin-form-input" rows="3" placeholder="Nhập các nét độc đáo, món đặc sản, cách tìm quán..." style="resize: vertical;"><?php echo e(old('description', $eatery ? $eatery->description : '')); ?></textarea>
                    </div>
                </div>

            </div>

            <!-- Submit details button -->
            <div style="border-top: 1px solid var(--admin-border); padding-top: 20px; margin-top: 10px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="/admin/dashboard" class="btn-admin btn-admin-secondary">Hủy bỏ</a>
                <button type="submit" class="btn-admin btn-admin-primary" style="padding: 10px 28px;">
                    <?php echo e($eatery ? '💾 Lưu thay đổi hồ sơ' : '🚀 Lưu lại & Đăng ký cơ sở'); ?>

                </button>
            </div>
        </form>
    </div>
</div>

<?php if($eatery): ?>
<!-- ==========================================================================
     TAB 2: DIGNATURE DISHES MANAGER (THỰC ĐƠN)
     ========================================================================== -->
<div id="tab-dishes" class="admin-tab-section" style="display: none;">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <span>🍔</span> Biên Tập Thực Đơn Cơ Sở
            </h2>
        </div>

        <div class="admin-split-layout">
            
            <!-- Left: Current Dishes -->
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 16px; color: var(--admin-text-main);">
                    📋 Danh sách món hiện tại (<?php echo e($eatery->dishes->count()); ?>)
                </h3>

                <?php if($eatery->dishes->count() > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <?php $__currentLoopData = $eatery->dishes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dish): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="admin-dish-item" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px; border: 1.5px solid var(--admin-border); border-radius: 12px; background-color: #ffffff;">
                                <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0;">
                                    <img src="<?php echo e($dish->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=80&q=80'); ?>" class="admin-dish-img" style="width: 56px; height: 56px; border-radius: 10px; object-fit: cover;">
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                            <h4 class="admin-dish-title" style="margin: 0; font-weight: 700; font-size: 0.92rem; color: var(--admin-text-main);"><?php echo e($dish->name); ?></h4>
                                            <?php if($dish->is_signature): ?>
                                                <span class="admin-badge admin-badge-success" style="font-size: 0.65rem; padding: 2px 6px;">★ Đặc trưng</span>
                                            <?php endif; ?>
                                        </div>
                                        <span style="font-size: 0.76rem; color: var(--admin-text-muted); display: block; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($dish->description ?: 'Chưa có mô tả ngắn'); ?></span>
                                        <span class="admin-dish-price" style="font-size: 0.85rem; font-weight: 800; color: var(--admin-success); display: block; margin-top: 1px;"><?php echo e(number_format($dish->price, 0, ',', '.')); ?>đ</span>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <!-- Toggle Signature Star Button -->
                                    <form action="/admin/dishes/<?php echo e($dish->id); ?>/toggle-signature" method="POST" style="display: inline; margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" style="background: transparent; border: none; padding: 4px; font-size: 1.25rem; cursor: pointer; color: <?php echo e($dish->is_signature ? '#eab308' : '#cbd5e1'); ?>; transition: transform 0.2s;" title="<?php echo e($dish->is_signature ? 'Gỡ sao nổi bật' : 'Đặt làm món nổi bật'); ?>" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                            ★
                                        </button>
                                    </form>

                                    <!-- View Details Button -->
                                    <button type="button" class="btn-admin btn-admin-secondary" style="padding: 6px 10px; font-size: 0.72rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;" onclick="openViewDishModal('<?php echo e(addslashes($dish->name)); ?>', '<?php echo e(number_format($dish->price, 0, ',', '.')); ?>đ', '<?php echo e(addslashes($dish->description)); ?>', '<?php echo e($dish->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80'); ?>', '<?php echo e($dish->is_signature ? 1 : 0); ?>')">
                                        👁️ Xem
                                    </button>

                                    <!-- Edit Button -->
                                    <button type="button" class="btn-admin btn-admin-accent" style="padding: 6px 10px; font-size: 0.72rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;" onclick="openEditDishModal('<?php echo e($dish->id); ?>', '<?php echo e(addslashes($dish->name)); ?>', '<?php echo e((int)$dish->price); ?>', '<?php echo e(addslashes($dish->description)); ?>', '<?php echo e($dish->image_path); ?>', '<?php echo e($dish->is_signature ? 1 : 0); ?>')">
                                        ✏️ Sửa
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="/admin/dishes/<?php echo e($dish->id); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa món này khỏi thực đơn?')" style="display: inline; margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-admin btn-admin-danger" style="padding: 6px 10px; font-size: 0.72rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                            🗑️ Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 0; border: 1.5px dashed var(--admin-border); border-radius: 12px; color: var(--admin-text-muted);">
                        <p style="font-size: 0.88rem; margin-bottom: 4px;">Thực đơn hiện tại đang trống.</p>
                        <p style="font-size: 0.78rem;">Sử dụng form bên phải để khai báo món ngon đầu tiên!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Add Dish Form -->
            <div>
                <div style="padding: 20px; border: 1.5px solid var(--admin-border); border-radius: 12px; background-color: #f8fafc;">
                    <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 16px; color: var(--admin-primary); display: flex; align-items: center; gap: 6px;">
                        <span>✨</span> Thêm món ngon mới
                    </h3>

                    <form action="/admin/dishes" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="eatery_id" value="<?php echo e($eatery->id); ?>">

                        <div class="admin-form-group">
                            <label class="admin-form-label">Tên món ăn <span style="color: var(--admin-danger);">*</span></label>
                            <input type="text" name="dish_name" class="admin-form-input" required placeholder="Ví dụ: Bún chả chày Mạch Tràng">
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">Giá bán thực tế (VNĐ) <span style="color: var(--admin-danger);">*</span></label>
                            <input type="number" name="dish_price" class="admin-form-input" required placeholder="Ví dụ: 35000">
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">Mô tả tóm tắt</label>
                            <textarea name="dish_description" class="admin-form-input" rows="2" placeholder="Ví dụ: Bún sợi to chuẩn truyền thống kèm chả băm nướng..." style="resize: vertical;"></textarea>
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">Ảnh món ăn (Upload)</label>
                            <input type="file" name="dish_image" class="admin-form-input" accept="image/*" style="padding: 5px 12px; font-size: 0.8rem;">
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">Hoặc URL ảnh món ăn</label>
                            <input type="url" name="dish_image_url" class="admin-form-input" placeholder="https://example.com/dish.jpg">
                        </div>

                        <div class="admin-form-group" style="display: flex; align-items: center; margin-top: 10px; margin-bottom: 15px;">
                            <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.84rem; font-weight: bold; color: var(--admin-text-main);">
                                <input type="checkbox" name="is_signature" value="1" style="width: 15px; height: 15px; accent-color: var(--admin-primary); cursor: pointer;">
                                ★ Đặt làm món đặc trưng nổi bật
                            </label>
                        </div>

                        <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; padding: 10px 0;">
                            🚀 Thêm món vào thực đơn
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 3: SPECIFIC EATERY VIDEO REVIEW MANAGEMENT
     ========================================================================== -->
<div id="tab-videos" class="admin-tab-section" style="display: none;">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <span>🎥</span> Quản Lý Video Review Của Cơ Sở
            </h2>
        </div>

        <div class="admin-split-layout">
            
            <!-- Left Column: Add Video Review Form -->
            <div>
                <div style="padding: 20px; border: 1.5px solid var(--admin-border); border-radius: 12px; background-color: #f8fafc;">
                    <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 16px; color: var(--admin-accent); display: flex; align-items: center; gap: 6px;">
                        <span>🎬</span> Nhúng Video Review Mới
                    </h3>
                    
                    <form action="<?php echo e(route('admin.video.store')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="eatery_id" value="<?php echo e($eatery->id); ?>">

                        <div class="admin-form-group">
                            <label class="admin-form-label">Tiêu đề video ngắn *</label>
                            <input type="text" name="title" required placeholder="Ví dụ: Review ăn sập bún mạch tràng..." class="admin-form-input">
                        </div>

                        <!-- Upload Type tabs inside Form -->
                        <div style="background-color: #e2e8f0; padding: 4px; border-radius: 8px; display: flex; gap: 4px; margin-bottom: 16px;">
                            <button type="button" id="uploadTabBtn-embed" onclick="toggleUploadMode('embed')" class="btn-admin btn-admin-primary" style="flex: 1; font-size: 0.75rem; padding: 6px 0; border-radius: 6px;">
                                🔗 Nhúng Link (0MB)
                            </button>
                            <button type="button" id="uploadTabBtn-file" onclick="toggleUploadMode('file')" class="btn-admin btn-admin-secondary" style="flex: 1; font-size: 0.75rem; padding: 6px 0; border-radius: 6px; background: transparent; border-color: transparent;">
                                📤 Tải Tệp từ máy
                            </button>
                        </div>

                        <!-- Embed URL (Default) -->
                        <div id="uploadContainer-embed" class="admin-form-group" style="display: block;">
                            <label class="admin-form-label">Đường dẫn video (TikTok / YouTube Shorts) *</label>
                            <input type="url" id="videoUrlInput" name="video_url" required placeholder="https://www.tiktok.com/@.../video/..." class="admin-form-input">
                            <span style="font-size: 0.75rem; color: var(--admin-text-muted); display: block; margin-top: 5px; line-height: 1.4;">
                                💡 Khuyên dùng: Dán liên kết TikTok hoặc Shorts để tự động hiển thị mượt mà trên bản đồ và không tốn bộ nhớ lưu trữ!
                            </span>
                        </div>

                        <!-- Local File Upload -->
                        <div id="uploadContainer-file" class="admin-form-group" style="display: none;">
                            <label class="admin-form-label">Chọn File Video ngắn từ thiết bị *</label>
                            <input type="file" id="videoFileInput" name="video_file" accept="video/mp4,video/quicktime" class="admin-form-input" style="padding: 6px 12px;">
                            <span style="font-size: 0.75rem; color: var(--admin-text-muted); display: block; margin-top: 5px; line-height: 1.4;">
                                ⚠️ Yêu cầu: Video định dạng MP4 dung lượng nhỏ hơn 20MB.
                            </span>
                        </div>

                        <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; padding: 10px 0; margin-top: 6px;">
                            🚀 Lưu video review
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Current Eatery Videos -->
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 16px; color: var(--admin-text-main);">
                    📋 Danh sách video hiện tại (<?php echo e($eatery->reviewVideos->count()); ?>)
                </h3>

                <?php if($eatery->reviewVideos->count() > 0): ?>
                    <div class="admin-table-container">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>Video & Tiêu đề</th>
                                    <th>Nguồn</th>
                                    <th>Trạng thái</th>
                                    <th style="text-align: center;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $eatery->reviewVideos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div style="position: relative; width: 38px; height: 50px; border-radius: 6px; overflow: hidden; background: #000; flex-shrink: 0; border: 1px solid var(--admin-border); cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'" onclick="openWatchVideoModal('<?php echo e(addslashes($vid->title)); ?>', '<?php echo e($vid->video_url); ?>', '<?php echo e($vid->video_type); ?>')" title="Bấm để xem video">
                                                    <img src="<?php echo e($vid->thumbnail_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=100&q=80'); ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.85;">
                                                    <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 10px;">▶️</span>
                                                </div>
                                                <div style="display: flex; flex-direction: column; min-width: 0;">
                                                    <span style="font-weight: 700; font-size: 0.82rem; color: var(--admin-text-main); line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; cursor: pointer;" onclick="openWatchVideoModal('<?php echo e(addslashes($vid->title)); ?>', '<?php echo e($vid->video_url); ?>', '<?php echo e($vid->video_type); ?>')" title="Bấm để xem video">
                                                        <?php echo e($vid->title); ?>

                                                    </span>
                                                    <span style="font-size: 0.7rem; color: var(--admin-text-muted); margin-top: 1px;">
                                                        Bởi: <?php echo e($vid->user->name); ?>

                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($vid->video_type === 'tiktok'): ?>
                                                <span class="admin-badge" style="background-color: #0f172a; color: #38bdf8;">TikTok</span>
                                            <?php elseif($vid->video_type === 'youtube_shorts'): ?>
                                                <span class="admin-badge admin-badge-danger">Shorts</span>
                                            <?php else: ?>
                                                <span class="admin-badge admin-badge-primary">File</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($vid->status === 'approved'): ?>
                                                <span class="admin-badge admin-badge-success">Đã Duyệt</span>
                                            <?php elseif($vid->status === 'pending'): ?>
                                                <span class="admin-badge admin-badge-warning">Chờ Duyệt</span>
                                            <?php else: ?>
                                                <span class="admin-badge admin-badge-danger">Bác bỏ</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="display: inline-flex; gap: 4px; align-items: center;">
                                                <?php if(session('user_role') === 'admin' && $vid->status === 'pending'): ?>
                                                    <form action="<?php echo e(route('admin.video.approve', $vid->id)); ?>" method="POST" style="display: inline;">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn-admin btn-admin-primary" style="padding: 4px 8px; font-size: 0.7rem; border-radius: 4px;">
                                                            Duyệt
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <!-- Watch video action -->
                                                <button type="button" class="btn-admin btn-admin-secondary" onclick="openWatchVideoModal('<?php echo e(addslashes($vid->title)); ?>', '<?php echo e($vid->video_url); ?>', '<?php echo e($vid->video_type); ?>')" style="padding: 4px 8px; font-size: 0.7rem; border-radius: 4px; display: inline-flex; align-items: center; gap: 2px;">
                                                    👁️ Xem
                                                </button>

                                                <button type="button" class="btn-admin btn-admin-accent" onclick="openEditVideoModal('<?php echo e($vid->id); ?>', '<?php echo e(addslashes($vid->title)); ?>', '<?php echo e($vid->eatery_id); ?>', '<?php echo e($vid->video_url); ?>', '<?php echo e($vid->video_type); ?>')" style="padding: 4px 8px; font-size: 0.7rem; border-radius: 4px;">
                                                    Sửa
                                                </button>
                                                
                                                <form action="<?php echo e(route('admin.video.destroy', $vid->id)); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa video review này không?')" style="display: inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn-admin btn-admin-danger" style="padding: 4px 8px; font-size: 0.7rem; border-radius: 4px;">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 0; border: 1.5px dashed var(--admin-border); border-radius: 12px; color: var(--admin-text-muted);">
                        <p style="font-size: 0.88rem; margin-bottom: 4px;">Quán ăn này chưa liên kết video review nào.</p>
                        <p style="font-size: 0.78rem;">Nhúng link TikTok hoặc đăng tải video mới để hiển thị mượt mà lên bản đồ!</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 4: TRUST HUB - VSATTP CERTIFICATE & DAILY INSPECTION LOGS
     ========================================================================== -->
<div id="tab-attp" class="admin-tab-section" style="display: none;">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <span>🛡️</span> Hồ Sơ Vệ Sinh An Toàn Thực Phẩm
            </h2>
        </div>

        <div class="admin-split-layout">
            
            <!-- VSATTP Certificate Form -->
            <div style="padding: 20px; border: 1.5px solid var(--admin-border); border-radius: 12px; background-color: #ffffff;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 16px; color: var(--admin-primary); display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                    <span style="display: flex; align-items: center; gap: 6px;"><span>📜</span> Giấy Chứng Nhận An Toàn VSATTP</span>
                    <?php if($eatery->foodSafetyCertificate): ?>
                        <button type="button" class="btn-admin btn-admin-accent" style="padding: 4px 8px; font-size: 0.68rem; border-radius: 6px; font-weight: 700;" onclick="openViewCertModal('<?php echo e(addslashes($eatery->foodSafetyCertificate->certificate_number)); ?>', '<?php echo e(addslashes($eatery->foodSafetyCertificate->issued_by)); ?>', '<?php echo e($eatery->foodSafetyCertificate->issued_at->format('d/m/Y')); ?>', '<?php echo e($eatery->foodSafetyCertificate->expired_at->format('d/m/Y')); ?>', '<?php echo e($eatery->foodSafetyCertificate->image_path); ?>')">👁️ Xem Giấy Phép</button>
                    <?php endif; ?>
                </h3>
                
                <form action="/admin/trust/certificate" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="eatery_id" value="<?php echo e($eatery->id); ?>">

                    <div class="admin-form-group">
                        <label class="admin-form-label">Số chứng nhận <span style="color: var(--admin-danger);">*</span></label>
                        <input type="text" name="certificate_number" class="admin-form-input" required placeholder="Ví dụ: 124/2024/ATTP-HN" value="<?php echo e($eatery->foodSafetyCertificate ? $eatery->foodSafetyCertificate->certificate_number : ''); ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Cơ quan cấp chứng nhận <span style="color: var(--admin-danger);">*</span></label>
                        <input type="text" name="issued_by" class="admin-form-input" required placeholder="Chi Cục An Toàn Thực Phẩm Sở Y Tế HN..." value="<?php echo e($eatery->foodSafetyCertificate ? $eatery->foodSafetyCertificate->issued_by : ''); ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Ngày cấp <span style="color: var(--admin-danger);">*</span></label>
                            <input type="date" name="issued_at" class="admin-form-input" required value="<?php echo e($eatery->foodSafetyCertificate ? $eatery->foodSafetyCertificate->issued_at->format('Y-m-d') : ''); ?>">
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Ngày hết hạn <span style="color: var(--admin-danger);">*</span></label>
                            <input type="date" name="expired_at" class="admin-form-input" required value="<?php echo e($eatery->foodSafetyCertificate ? $eatery->foodSafetyCertificate->expired_at->format('Y-m-d') : ''); ?>">
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Ảnh chụp chứng nhận (Upload)</label>
                        <input type="file" name="image" class="admin-form-input" accept="image/*" style="padding: 5px 12px; font-size: 0.8rem;">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Hoặc Đường dẫn ảnh (URL)</label>
                        <input type="url" name="image_url" class="admin-form-input" placeholder="https://example.com/cert.jpg" value="<?php echo e($eatery->foodSafetyCertificate ? $eatery->foodSafetyCertificate->image_path : ''); ?>">
                    </div>

                    <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; padding: 10px 0;">
                        💾 Lưu thông tin chứng nhận VSATTP
                    </button>
                </form>
            </div>

            <!-- Daily Inspection Logs -->
            <div style="padding: 20px; border: 1.5px solid var(--admin-border); border-radius: 12px; background-color: #ffffff;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 16px; color: var(--admin-accent); display: flex; align-items: center; gap: 6px;">
                    <span>📅</span> Đóng Dấu Nhật Ký Hàng Ngày
                </h3>

                <form action="/admin/trust/logs" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="eatery_id" value="<?php echo e($eatery->id); ?>">
                    <input type="hidden" name="log_date" value="<?php echo e(date('Y-m-d')); ?>">

                    <div class="admin-form-group">
                        <label class="admin-form-label">Ngày kiểm tra</label>
                        <input type="text" class="admin-form-input" disabled value="<?php echo e(date('d/m/Y')); ?> (Hôm nay)" style="opacity: 0.85;">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Nguồn gốc nguyên liệu nhập vào sạch *</label>
                        <input type="text" name="ingredients_origin" required class="admin-form-input" placeholder="Ví dụ: Thịt lợn sạch Liêm Hiệp, Rau HTX Vân Nội...">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Nhiệt độ bảo quản & Tình trạng đạt *</label>
                        <input type="text" name="storage_condition" required class="admin-form-input" placeholder="Ví dụ: Tủ đông -18°C và Tủ mát 4°C bảo quản đạt chuẩn...">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Người kiểm tra thực hiện *</label>
                        <input type="text" name="checker_name" required class="admin-form-input" placeholder="Họ và tên..." value="<?php echo e(session('user_name') ?: ''); ?>">
                    </div>

                    <button type="submit" class="btn-admin btn-admin-accent" style="width: 100%; padding: 10px 0;">
                        ✔ Xác Nhận & Đóng Dấu Nhật Ký
                    </button>
                </form>

                <!-- 3 days inspection logs history -->
                <div style="margin-top: 20px;">
                    <h4 style="font-size: 0.84rem; font-weight: 700; margin-bottom: 10px; color: var(--admin-text-main);">
                        Nhật ký đóng dấu 3 ngày gần đây:
                    </h4>
                    <?php if($eatery->dailyFoodLogs->count() > 0): ?>
                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            <?php $__currentLoopData = $eatery->dailyFoodLogs->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="padding: 10px; border: 1.5px solid var(--admin-border); border-radius: 8px; display: flex; justify-content: space-between; align-items: center; background-color: #f8fafc; font-size: 0.8rem;">
                                    <div style="cursor: pointer; flex: 1;" onclick="openViewLogModal('<?php echo e(\Carbon\Carbon::parse($log->log_date)->format('d/m/Y')); ?>', '<?php echo e(addslashes($log->ingredients_origin)); ?>', '<?php echo e(addslashes($log->storage_condition)); ?>', '<?php echo e(addslashes($log->checker_name)); ?>')">
                                        <span style="font-weight: 800; color: var(--admin-accent);">📅 <?php echo e($log->log_date->format('d/m/Y')); ?></span> - 
                                        <span style="color: var(--admin-text-main);"><?php echo e(Str::limit($log->ingredients_origin, 28)); ?></span>
                                    </div>
                                    <div style="display: flex; gap: 6px; align-items: center;">
                                        <button type="button" class="btn-admin btn-admin-accent" style="padding: 4px 8px; font-size: 0.68rem; border-radius: 6px; font-weight: 700;" onclick="openViewLogModal('<?php echo e(\Carbon\Carbon::parse($log->log_date)->format('d/m/Y')); ?>', '<?php echo e(addslashes($log->ingredients_origin)); ?>', '<?php echo e(addslashes($log->storage_condition)); ?>', '<?php echo e(addslashes($log->checker_name)); ?>')">👁️ Xem</button>
                                        <form action="/admin/trust/logs/<?php echo e($log->id); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhật ký ngày này?')" style="display: inline; margin: 0;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" style="background: none; border: none; color: var(--admin-danger); cursor: pointer; font-size: 0.9rem;" title="Xóa nhật ký">🗑️</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p style="font-size: 0.78rem; color: var(--admin-text-muted); font-style: italic;">Chưa ghi nhận nhật ký nào gần đây.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 5: TRUST HUB - SUPPLY CONTRACTS & INVOICES
     ========================================================================== -->
<div id="tab-contracts" class="admin-tab-section" style="display: none;">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <span>🧾</span> Hồ Sơ Hợp Đồng & Hóa Đơn Sạch
            </h2>
        </div>

        <div class="admin-split-layout">
            
            <!-- Supply Clean Contracts -->
            <div style="padding: 20px; border: 1.5px solid var(--admin-border); border-radius: 12px; background-color: #ffffff;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 16px; color: var(--admin-accent); display: flex; align-items: center; gap: 6px;">
                    <span>📜</span> Hợp Đồng Cung Cấp Nguyên Liệu Sạch
                </h3>

                <!-- Upload New Contract -->
                <div style="padding: 14px; border: 1.5px solid var(--admin-border); border-radius: 8px; background-color: #f8fafc; margin-bottom: 16px;">
                    <h4 style="font-size: 0.82rem; font-weight: 700; margin-bottom: 12px; color: var(--admin-text-main);">Khai báo hợp đồng mới:</h4>
                    <form action="/admin/trust/contracts" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="eatery_id" value="<?php echo e($eatery->id); ?>">
                        
                        <div class="admin-form-group" style="margin-bottom: 10px;">
                            <input type="text" name="supplier_name" required class="admin-form-input" placeholder="Tên đối tác cung cấp (HTX Vân Nội...)" style="padding: 6px 12px; font-size: 0.82rem;">
                        </div>

                        <div class="admin-form-group" style="margin-bottom: 10px;">
                            <input type="text" name="items_supplied" required class="admin-form-input" placeholder="Nguyên liệu (Rau quả hữu cơ...)" style="padding: 6px 12px; font-size: 0.82rem;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                            <input type="date" name="signed_at" required class="admin-form-input" placeholder="Ngày ký" style="padding: 6px 12px; font-size: 0.82rem;">
                            <input type="date" name="expired_at" required class="admin-form-input" placeholder="Hết hạn" style="padding: 6px 12px; font-size: 0.82rem;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                            <input type="file" name="image" class="admin-form-input" accept="image/*" style="padding: 3px; font-size: 0.72rem;">
                            <input type="url" name="image_url" class="admin-form-input" placeholder="Hoặc dán URL ảnh" style="padding: 6px 12px; font-size: 0.82rem;">
                        </div>

                        <button type="submit" class="btn-admin btn-admin-accent" style="width: 100%; padding: 6px 0; font-size: 0.8rem;">
                            ➕ Thêm Hợp Đồng Sạch
                        </button>
                    </form>
                </div>

                <!-- Contracts List -->
                <?php if($eatery->foodSupplyContracts->count() > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <?php $__currentLoopData = $eatery->foodSupplyContracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="padding: 10px; border: 1.5px solid var(--admin-border); border-radius: 8px; display: flex; gap: 12px; align-items: center; background-color: #ffffff;">
                                <img src="<?php echo e($contract->image_path); ?>" style="width: 36px; height: 46px; object-fit: cover; border-radius: 4px; border: 1px solid var(--admin-border); cursor: pointer;" onclick="openViewContractModal('<?php echo e(addslashes($contract->supplier_name)); ?>', '<?php echo e(addslashes($contract->items_supplied)); ?>', '<?php echo e(\Carbon\Carbon::parse($contract->signed_at)->format('d/m/Y')); ?>', '<?php echo e(\Carbon\Carbon::parse($contract->expired_at)->format('d/m/Y')); ?>', '<?php echo e($contract->image_path); ?>')">
                                <div style="flex: 1; min-width: 0; cursor: pointer;" onclick="openViewContractModal('<?php echo e(addslashes($contract->supplier_name)); ?>', '<?php echo e(addslashes($contract->items_supplied)); ?>', '<?php echo e(\Carbon\Carbon::parse($contract->signed_at)->format('d/m/Y')); ?>', '<?php echo e(\Carbon\Carbon::parse($contract->expired_at)->format('d/m/Y')); ?>', '<?php echo e($contract->image_path); ?>')">
                                    <h5 style="margin: 0; font-weight: 700; font-size: 0.82rem; color: var(--admin-text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($contract->supplier_name); ?></h5>
                                    <span style="font-size: 0.7rem; color: var(--admin-accent); display: block; margin-top: 1px;">🌾 <?php echo e($contract->items_supplied); ?></span>
                                </div>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    <button type="button" class="btn-admin btn-admin-accent" style="padding: 4px 8px; font-size: 0.68rem; border-radius: 6px; font-weight: 700;" onclick="openViewContractModal('<?php echo e(addslashes($contract->supplier_name)); ?>', '<?php echo e(addslashes($contract->items_supplied)); ?>', '<?php echo e(\Carbon\Carbon::parse($contract->signed_at)->format('d/m/Y')); ?>', '<?php echo e(\Carbon\Carbon::parse($contract->expired_at)->format('d/m/Y')); ?>', '<?php echo e($contract->image_path); ?>')">👁️ Xem</button>
                                    <form action="/admin/trust/contracts/<?php echo e($contract->id); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hợp đồng này?')" style="display: inline; margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" style="background: none; border: none; color: var(--admin-danger); cursor: pointer; font-size: 0.9rem; padding: 2px;" title="Xóa hợp đồng">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p style="font-size: 0.78rem; color: var(--admin-text-muted); font-style: italic;">Chưa ghim hợp đồng sạch nào.</p>
                <?php endif; ?>
            </div>

            <!-- Purchase Clean Invoices -->
            <div style="padding: 20px; border: 1.5px solid var(--admin-border); border-radius: 12px; background-color: #ffffff;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 16px; color: var(--admin-primary); display: flex; align-items: center; gap: 6px;">
                    <span>🧾</span> Hóa Đơn Mua Hàng Hàng Ngày
                </h3>

                <!-- Upload New Invoice -->
                <div style="padding: 14px; border: 1.5px solid var(--admin-border); border-radius: 8px; background-color: #f8fafc; margin-bottom: 16px;">
                    <h4 style="font-size: 0.82rem; font-weight: 700; margin-bottom: 12px; color: var(--admin-text-main);">Khai báo hóa đơn mới:</h4>
                    <form action="/admin/trust/invoices" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="eatery_id" value="<?php echo e($eatery->id); ?>">
                        
                        <div class="admin-form-group" style="margin-bottom: 10px;">
                            <input type="text" name="supplier_name" required class="admin-form-input" placeholder="Tên đơn vị bán (Chợ rau Đông Anh...)" style="padding: 6px 12px; font-size: 0.82rem;">
                        </div>

                        <div class="admin-form-group" style="margin-bottom: 10px;">
                            <input type="text" name="items_summary" required class="admin-form-input" placeholder="Mặt hàng mua (40kg sườn lợn...)" style="padding: 6px 12px; font-size: 0.82rem;">
                        </div>

                        <div class="admin-form-group" style="margin-bottom: 10px;">
                            <input type="date" name="invoice_date" required class="admin-form-input" placeholder="Ngày mua hàng" style="padding: 6px 12px; font-size: 0.82rem;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                            <input type="file" name="image" class="admin-form-input" accept="image/*" style="padding: 3px; font-size: 0.72rem;">
                            <input type="url" name="image_url" class="admin-form-input" placeholder="Hoặc dán URL ảnh" style="padding: 6px 12px; font-size: 0.82rem;">
                        </div>

                        <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; padding: 6px 0; font-size: 0.8rem;">
                            ➕ Thêm Hóa Đơn
                        </button>
                    </form>
                </div>

                <!-- Invoices List -->
                <?php if($eatery->purchaseInvoices->count() > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <?php $__currentLoopData = $eatery->purchaseInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="padding: 10px; border: 1.5px solid var(--admin-border); border-radius: 8px; display: flex; gap: 12px; align-items: center; background-color: #ffffff;">
                                <img src="<?php echo e($invoice->image_path); ?>" style="width: 36px; height: 46px; object-fit: cover; border-radius: 4px; border: 1px solid var(--admin-border); filter: blur(0.5px); cursor: pointer;" onclick="openViewInvoiceModal('<?php echo e(addslashes($invoice->supplier_name)); ?>', '<?php echo e(addslashes($invoice->items_summary)); ?>', '<?php echo e(\Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y')); ?>', '<?php echo e($invoice->image_path); ?>')">
                                <div style="flex: 1; min-width: 0; cursor: pointer;" onclick="openViewInvoiceModal('<?php echo e(addslashes($invoice->supplier_name)); ?>', '<?php echo e(addslashes($invoice->items_summary)); ?>', '<?php echo e(\Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y')); ?>', '<?php echo e($invoice->image_path); ?>')">
                                    <h5 style="margin: 0; font-weight: 700; font-size: 0.82rem; color: var(--admin-text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($invoice->supplier_name); ?></h5>
                                    <span style="font-size: 0.7rem; color: var(--admin-text-muted); display: block; margin-top: 1px;">🧾 <?php echo e($invoice->items_summary); ?></span>
                                </div>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    <button type="button" class="btn-admin btn-admin-primary" style="padding: 4px 8px; font-size: 0.68rem; border-radius: 6px; font-weight: 700;" onclick="openViewInvoiceModal('<?php echo e(addslashes($invoice->supplier_name)); ?>', '<?php echo e(addslashes($invoice->items_summary)); ?>', '<?php echo e(\Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y')); ?>', '<?php echo e($invoice->image_path); ?>')">👁️ Xem</button>
                                    <form action="/admin/trust/invoices/<?php echo e($invoice->id); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hóa đơn này?')" style="display: inline; margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" style="background: none; border: none; color: var(--admin-danger); cursor: pointer; font-size: 0.9rem; padding: 2px;" title="Xóa hóa đơn">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p style="font-size: 0.78rem; color: var(--admin-text-muted); font-style: italic;">Chưa có hóa đơn nào được cập nhật.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 6: CUSTOMER REVIEWS & FEEDBACK
     ========================================================================== -->
<div id="tab-reviews" class="admin-tab-section" style="display: none;">
    <div class="admin-card">
        <div class="admin-card-header" style="display: flex; align-items: center; justify-content: space-between;">
            <h2 class="admin-card-title">
                <span>💬</span> Đánh Giá & Phản Hồi Từ Khách Hàng
            </h2>
            <span class="admin-badge admin-badge-primary" style="font-size: 0.8rem; font-weight: 700; padding: 6px 14px;">
                ★ <?php echo e(number_format($eatery->reviews->avg('rating') ?: 5.0, 1)); ?> / 5.0 (<?php echo e($eatery->reviews->count()); ?> lượt đánh giá)
            </span>
        </div>

        <div style="padding: 8px 0;">
            <?php if($eatery->reviews->count() > 0): ?>
                <div class="admin-table-container">
                    <table class="admin-data-table">
                        <thead>
                            <tr>
                                <th style="width: 150px;">Khách hàng</th>
                                <th style="width: 130px;">Đánh giá</th>
                                <th>Nội dung nhận xét</th>
                                <th style="width: 130px;">Thời gian</th>
                                <?php if(session('user_role') === 'admin'): ?>
                                <th style="text-align: center; width: 100px;">Thao tác</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $eatery->reviews->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong style="color: var(--admin-text-main); font-size: 0.88rem;"><?php echo e($rev->user_name); ?></strong>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <span style="color: var(--admin-warning); font-weight: 800; font-size: 0.95rem; letter-spacing: 1px;">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <?php echo e($i <= $rev->rating ? '★' : '☆'); ?>

                                            <?php endfor; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <p style="font-size: 0.86rem; color: var(--admin-text-main); line-height: 1.5; margin: 0; white-space: pre-line;"><?php echo e($rev->comment); ?></p>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.76rem; color: var(--admin-text-muted);">
                                            <?php echo e(\Carbon\Carbon::parse($rev->created_at)->format('H:i d/m/Y')); ?>

                                        </span>
                                    </td>
                                    <?php if(session('user_role') === 'admin'): ?>
                                    <td style="text-align: center;">
                                        <form action="/admin/reviews/<?php echo e($rev->id); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này khỏi hệ thống?')" style="display: inline; margin: 0;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn-admin btn-admin-danger" style="padding: 4px 8px; font-size: 0.72rem; border-radius: 4px; font-weight: 700; cursor: pointer;">
                                                🗑️ Xóa
                                            </button>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 50px 0; border: 1.5px dashed var(--admin-border); border-radius: 12px; color: var(--admin-text-muted);">
                    <p style="font-size: 1rem; margin-bottom: 6px;">💬 Quán ăn này chưa có đánh giá nào từ khách hàng.</p>
                    <p style="font-size: 0.82rem;">Hãy khuyến khích khách hàng ghé thăm và gửi đánh giá để tăng độ uy tín cho cơ sở!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ==========================================================================
     MODAL XEM CHI TIẾT MÓN ĂN (SaaS Luxury Layout)
     ========================================================================== -->
<div id="viewDishModal" class="admin-reels-overlay" style="display: none;">
    <div class="admin-card" style="width: 100%; max-width: 500px; padding: 0; position: relative; border-radius: 20px; background-color: #ffffff; box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15); overflow: hidden; border: none;">
        <!-- Beautiful floating close icon -->
        <button type="button" style="position: absolute; top: 16px; right: 16px; background: rgba(15, 23, 42, 0.6); border: none; color: #ffffff; font-size: 1rem; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; backdrop-filter: blur(4px); transition: all 0.2s;" onclick="closeViewDishModal()" onmouseover="this.style.background='rgba(15, 23, 42, 0.8)'" onmouseout="this.style.background='rgba(15, 23, 42, 0.6)'">✕</button>
        
        <!-- Premium Image Banner with Gradient Overlay -->
        <div style="position: relative; height: 260px; overflow: hidden; background-color: #f1f5f9;">
            <img id="viewDishImg" src="" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,0.6) 100%); z-index: 5;"></div>
            
            <!-- Absolute floating signature badge -->
            <span id="viewDishBadge" class="admin-badge" style="position: absolute; top: 16px; left: 16px; font-size: 0.72rem; font-weight: 800; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); display: none; z-index: 10; border: 1px solid rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px;">★ MÓN ĐẶC TRƯNG NỔI BẬT</span>
            
            <!-- Floating dish name in banner -->
            <div style="position: absolute; bottom: 20px; left: 24px; right: 24px; z-index: 10;">
                <h3 id="viewDishName" style="font-size: 1.35rem; font-weight: 800; color: #ffffff; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.4); line-height: 1.3; letter-spacing: -0.01em;"></h3>
            </div>
        </div>

        <!-- Structured Details Panel -->
        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr; gap: 18px;">
                
                <!-- Price Section (Giá bán) -->
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span style="font-size: 0.7rem; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px;">💵 Giá bán niêm yết</span>
                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 500;">Giá thực tế tại cửa hàng</span>
                    </div>
                    <span id="viewDishPrice" style="font-size: 1.45rem; font-weight: 800; color: #10b981; letter-spacing: -0.02em;"></span>
                </div>
                
                <!-- Description Section (Mô tả hương vị) -->
                <div>
                    <span style="font-size: 0.72rem; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 8px; padding-left: 2px;">📝 Mô tả hương vị & Công thức</span>
                    <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; min-height: 80px; max-height: 160px; overflow-y: auto;">
                        <p id="viewDishDesc" style="font-size: 0.88rem; color: #334155; line-height: 1.6; margin: 0; white-space: pre-line;"></p>
                    </div>
                </div>
                
            </div>

        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL XEM CHI TIẾT HỢP ĐỒNG SẠCH (SaaS Luxury Layout)
     ========================================================================== -->
<div id="viewContractModal" class="admin-reels-overlay" style="display: none;">
    <div class="admin-card" style="width: 100%; max-width: 500px; padding: 0; position: relative; border-radius: 20px; background-color: #ffffff; box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15); overflow: hidden; border: none;">
        <!-- Beautiful floating close icon -->
        <button type="button" style="position: absolute; top: 16px; right: 16px; background: rgba(15, 23, 42, 0.6); border: none; color: #ffffff; font-size: 1rem; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; backdrop-filter: blur(4px); transition: all 0.2s;" onclick="closeViewContractModal()" onmouseover="this.style.background='rgba(15, 23, 42, 0.8)'" onmouseout="this.style.background='rgba(15, 23, 42, 0.6)'">✕</button>
        
        <!-- Premium Image Banner with Gradient Overlay -->
        <div style="position: relative; height: 260px; overflow: hidden; background-color: #f1f5f9;">
            <img id="viewContractImg" src="" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,0.6) 100%); z-index: 5;"></div>
            
            <div style="position: absolute; bottom: 20px; left: 24px; right: 24px; z-index: 10;">
                <span class="admin-badge" style="font-size: 0.68rem; font-weight: 800; background: #4f46e5; color: #ffffff; margin-bottom: 6px; padding: 4px 10px; border-radius: 12px; display: inline-block;">📜 HỢP ĐỒNG CUNG CẤP</span>
                <h3 id="viewContractSupplier" style="font-size: 1.3rem; font-weight: 800; color: #ffffff; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.4); line-height: 1.3;"></h3>
            </div>
        </div>

        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                <!-- Items Supplied -->
                <div>
                    <span style="font-size: 0.72rem; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">🌾 Danh mục nguyên liệu sạch</span>
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 0.88rem; font-weight: 600; color: var(--admin-text-main);">
                        <span id="viewContractItems"></span>
                    </div>
                </div>

                <!-- Sign & Expiry Dates -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px;">
                        <span style="font-size: 0.68rem; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; display: block; margin-bottom: 2px;">📅 Ngày ký kết</span>
                        <span id="viewContractSigned" style="font-size: 0.85rem; font-weight: 700; color: var(--admin-text-main);"></span>
                    </div>
                    <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 10px 14px;">
                        <span style="font-size: 0.68rem; font-weight: 800; color: #b45309; text-transform: uppercase; display: block; margin-bottom: 2px;">⏳ Ngày hết hạn</span>
                        <span id="viewContractExpired" style="font-size: 0.85rem; font-weight: 700; color: #b45309;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL XEM CHI TIẾT HÓA ĐƠN MUA HÀNG (SaaS Luxury Layout)
     ========================================================================== -->
<div id="viewInvoiceModal" class="admin-reels-overlay" style="display: none;">
    <div class="admin-card" style="width: 100%; max-width: 500px; padding: 0; position: relative; border-radius: 20px; background-color: #ffffff; box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15); overflow: hidden; border: none;">
        <!-- Beautiful floating close icon -->
        <button type="button" style="position: absolute; top: 16px; right: 16px; background: rgba(15, 23, 42, 0.6); border: none; color: #ffffff; font-size: 1rem; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; backdrop-filter: blur(4px); transition: all 0.2s;" onclick="closeViewInvoiceModal()" onmouseover="this.style.background='rgba(15, 23, 42, 0.8)'" onmouseout="this.style.background='rgba(15, 23, 42, 0.6)'">✕</button>
        
        <!-- Premium Image Banner with Gradient Overlay -->
        <div style="position: relative; height: 260px; overflow: hidden; background-color: #f1f5f9;">
            <img id="viewInvoiceImg" src="" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,0.6) 100%); z-index: 5;"></div>
            
            <div style="position: absolute; bottom: 20px; left: 24px; right: 24px; z-index: 10;">
                <span class="admin-badge" style="font-size: 0.68rem; font-weight: 800; background: #10b981; color: #ffffff; margin-bottom: 6px; padding: 4px 10px; border-radius: 12px; display: inline-block;">🧾 HÓA ĐƠN MUA HÀNG</span>
                <h3 id="viewInvoiceSupplier" style="font-size: 1.3rem; font-weight: 800; color: #ffffff; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.4); line-height: 1.3;"></h3>
            </div>
        </div>

        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                <!-- Summary -->
                <div>
                    <span style="font-size: 0.72rem; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">🛒 Chi tiết thực phẩm nhập mua</span>
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 0.88rem; font-weight: 600; color: var(--admin-text-main);">
                        <span id="viewInvoiceSummary"></span>
                    </div>
                </div>

                <!-- Date -->
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px;">
                    <span style="font-size: 0.68rem; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; display: block; margin-bottom: 2px;">📅 Ngày giao dịch / xuất phiếu</span>
                    <span id="viewInvoiceDate" style="font-size: 0.88rem; font-weight: 700; color: var(--admin-text-main);"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL XEM CHI TIẾT GIẤY PHÉP VSATTP (SaaS Luxury Layout)
     ========================================================================== -->
<div id="viewCertModal" class="admin-reels-overlay" style="display: none;">
    <div class="admin-card" style="width: 100%; max-width: 500px; padding: 0; position: relative; border-radius: 20px; background-color: #ffffff; box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15); overflow: hidden; border: none;">
        <!-- Beautiful floating close icon -->
        <button type="button" style="position: absolute; top: 16px; right: 16px; background: rgba(15, 23, 42, 0.6); border: none; color: #ffffff; font-size: 1rem; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; backdrop-filter: blur(4px); transition: all 0.2s;" onclick="closeViewCertModal()" onmouseover="this.style.background='rgba(15, 23, 42, 0.8)'" onmouseout="this.style.background='rgba(15, 23, 42, 0.6)'">✕</button>
        
        <!-- Premium Image Banner with Gradient Overlay -->
        <div style="position: relative; height: 260px; overflow: hidden; background-color: #f1f5f9;">
            <img id="viewCertImg" src="" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,0.6) 100%); z-index: 5;"></div>
            
            <div style="position: absolute; bottom: 20px; left: 24px; right: 24px; z-index: 10;">
                <span class="admin-badge" style="font-size: 0.68rem; font-weight: 800; background: #10b981; color: #ffffff; margin-bottom: 6px; padding: 4px 10px; border-radius: 12px; display: inline-block;">🛡️ CHỨNG NHẬN VSATTP</span>
                <h3 id="viewCertNumber" style="font-size: 1.3rem; font-weight: 800; color: #ffffff; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.4); line-height: 1.3;"></h3>
            </div>
        </div>

        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                <!-- Issued By -->
                <div>
                    <span style="font-size: 0.72rem; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">🏛️ Cơ quan cấp chứng nhận</span>
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 0.88rem; font-weight: 600; color: var(--admin-text-main);">
                        <span id="viewCertIssuedBy"></span>
                    </div>
                </div>

                <!-- Sign & Expiry Dates -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px;">
                        <span style="font-size: 0.68rem; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; display: block; margin-bottom: 2px;">📅 Ngày cấp giấy</span>
                        <span id="viewCertIssuedAt" style="font-size: 0.85rem; font-weight: 700; color: var(--admin-text-main);"></span>
                    </div>
                    <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 10px 14px;">
                        <span style="font-size: 0.68rem; font-weight: 800; color: #b45309; text-transform: uppercase; display: block; margin-bottom: 2px;">⏳ Ngày hết hạn</span>
                        <span id="viewCertExpiredAt" style="font-size: 0.85rem; font-weight: 700; color: #b45309;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL XEM CHI TIẾT NHẬT KÝ HÀNG NGÀY (SaaS Luxury Layout)
     ========================================================================== -->
<div id="viewLogModal" class="admin-reels-overlay" style="display: none;">
    <div class="admin-card" style="width: 100%; max-width: 500px; padding: 0; position: relative; border-radius: 20px; background-color: #ffffff; box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15); overflow: hidden; border: none;">
        <!-- Beautiful floating close icon -->
        <button type="button" style="position: absolute; top: 16px; right: 16px; background: rgba(15, 23, 42, 0.6); border: none; color: #ffffff; font-size: 1rem; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; backdrop-filter: blur(4px); transition: all 0.2s;" onclick="closeViewLogModal()" onmouseover="this.style.background='rgba(15, 23, 42, 0.8)'" onmouseout="this.style.background='rgba(15, 23, 42, 0.6)'">✕</button>
        
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 32px 24px; color: #ffffff; position: relative;">
            <span class="admin-badge" style="font-size: 0.68rem; font-weight: 800; background: #4f46e5; color: #ffffff; margin-bottom: 8px; padding: 4px 10px; border-radius: 12px; display: inline-block;">📅 NHẬT KÝ AN TOÀN THỰC PHẨM</span>
            <h3 id="viewLogDate" style="font-size: 1.45rem; font-weight: 800; color: #ffffff; margin: 0;"></h3>
        </div>

        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr; gap: 18px;">
                <!-- Ingredients Origin -->
                <div>
                    <span style="font-size: 0.72rem; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">🌾 Nguồn gốc nguyên liệu nhập sạch</span>
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; font-size: 0.88rem; font-weight: 600; color: var(--admin-text-main); line-height: 1.5;">
                        <span id="viewLogOrigin"></span>
                    </div>
                </div>

                <!-- Storage Condition -->
                <div>
                    <span style="font-size: 0.72rem; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">❄️ Nhiệt độ bảo quản & Tình trạng đạt</span>
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; font-size: 0.88rem; font-weight: 600; color: var(--admin-text-main); line-height: 1.5;">
                        <span id="viewLogStorage"></span>
                    </div>
                </div>

                <!-- Checker -->
                <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span style="font-size: 0.68rem; font-weight: 800; color: #166534; text-transform: uppercase; display: block; margin-bottom: 2px;">👤 Người kiểm tra thực hiện</span>
                        <span id="viewLogChecker" style="font-size: 0.9rem; font-weight: 700; color: #166534;"></span>
                    </div>
                    <span style="font-size: 1.25rem;">✅</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL CHỈNH SỬA MÓN ĂN
     ========================================================================== -->
<div id="editDishModal" class="admin-reels-overlay" style="display: none;">
    <div class="admin-card" style="width: 100%; max-width: 480px; padding: 24px; position: relative; border-radius: 16px; background-color: #ffffff; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
        <button type="button" style="position: absolute; top: 16px; right: 16px; background: transparent; border: none; color: var(--admin-text-muted); font-size: 1.25rem; cursor: pointer; z-index: 10;" onclick="closeEditDishModal()">✕</button>
        
        <h3 class="admin-card-title" style="margin-bottom: 18px; font-size: 1.1rem; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">
            <span>✏️</span> Cập Nhật Thông Tin Món Ăn
        </h3>
        
        <form id="editDishForm" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Tên món ăn <span style="color: var(--admin-danger);">*</span></label>
                <input type="text" id="editDishNameInput" name="dish_name" required placeholder="Ví dụ: Bún chả chày..." class="admin-form-input">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">Giá bán thực tế (VNĐ) <span style="color: var(--admin-danger);">*</span></label>
                <input type="number" id="editDishPriceInput" name="dish_price" required placeholder="Ví dụ: 35000" class="admin-form-input">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">Mô tả tóm tắt</label>
                <textarea id="editDishDescInput" name="dish_description" rows="2" class="admin-form-input" placeholder="Ví dụ: Mô tả hương vị món ăn..."></textarea>
            </div>

            <div class="admin-form-group" style="margin-bottom: 12px;">
                <label class="admin-form-label">Chọn File Ảnh mới</label>
                <input type="file" name="dish_image" accept="image/*" class="admin-form-input" style="padding: 6px 12px;">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">Hoặc dán URL ảnh mới</label>
                <input type="url" id="editDishImageUrlInput" name="dish_image_url" placeholder="https://example.com/image.jpg" class="admin-form-input">
            </div>

            <div class="admin-form-group" style="display: flex; align-items: center; margin-top: 10px; margin-bottom: 18px;">
                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.84rem; font-weight: bold; color: var(--admin-text-main);">
                    <input type="checkbox" id="editDishSignatureInput" name="is_signature" value="1" style="width: 15px; height: 15px; accent-color: var(--admin-primary); cursor: pointer;">
                    ★ Đặt làm món đặc trưng nổi bật
                </label>
            </div>

            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn-admin btn-admin-secondary" style="padding: 10px 20px; font-size: 0.82rem; border-radius: 8px;" onclick="closeEditDishModal()">Hủy bỏ</button>
                <button type="submit" class="btn-admin btn-admin-primary" style="padding: 10px 24px; font-size: 0.82rem; border-radius: 8px;">💾 Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================================================
     MODAL SỬA VIDEO REVIEW TIỆN ÍCH DÀNH CHO CƠ SỞ
     ========================================================================== -->
<div id="editVideoModal" class="admin-reels-overlay" style="display: none;">
    <div class="admin-card" style="width: 100%; max-width: 460px; padding: 24px; position: relative; border-radius: 12px; background-color: #ffffff; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <button type="button" style="position: absolute; top: 16px; right: 16px; background: transparent; border: none; color: var(--admin-text-muted); font-size: 1.15rem; cursor: pointer;" onclick="closeEditVideoModal()">✕</button>
        
        <h3 class="admin-card-title" style="margin-bottom: 18px; font-size: 1.1rem; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">
            <span>✏️</span> Cập Nhật Video Review
        </h3>
        
        <form id="editVideoForm" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Tiêu đề video *</label>
                <input type="text" id="editVideoTitle" name="title" required placeholder="Ví dụ: Ăn sập chợ Đông Anh..." class="admin-form-input">
            </div>

            <!-- Pre-filled eatery input hidden -->
            <?php if($eatery): ?>
                <input type="hidden" id="editVideoEateryId" name="eatery_id" value="<?php echo e($eatery->id); ?>">
            <?php endif; ?>

            <!-- Edit Upload Type Tabs -->
            <div style="background-color: #e2e8f0; padding: 4px; border-radius: 8px; display: flex; gap: 4px; border: 1px solid var(--admin-border); margin-bottom: 16px;">
                <button type="button" id="editTabBtn-embed" onclick="toggleEditUploadMode('embed')" class="btn-admin btn-admin-accent" style="flex: 1; font-size: 0.78rem; padding: 6px 0; border-radius: 6px;">
                    🔗 Nhúng Link
                </button>
                <button type="button" id="editTabBtn-file" onclick="toggleEditUploadMode('file')" class="btn-admin btn-admin-secondary" style="flex: 1; font-size: 0.78rem; padding: 6px 0; border-radius: 6px; background: transparent; border-color: transparent;">
                    📤 Tải Video mới
                </button>
            </div>

            <!-- Edit Embed Section -->
            <div id="editContainer-embed" class="admin-form-group" style="display: block;">
                <label class="admin-form-label">Đường dẫn Video (TikTok / Shorts) *</label>
                <input type="url" id="editVideoUrlInput" name="video_url" placeholder="https://www.tiktok.com/..." class="admin-form-input">
            </div>

            <!-- Edit File Upload Section -->
            <div id="editContainer-file" class="admin-form-group" style="display: none;">
                <label class="admin-form-label">Chọn File Video mới</label>
                <input type="file" id="editVideoFileInput" name="video_file" accept="video/mp4" class="admin-form-input" style="padding: 6px 12px;">
                <span style="font-size: 0.72rem; color: var(--admin-text-muted); display: block; margin-top: 5px; line-height: 1.4;">
                    ⚠️ Để trống nếu bạn muốn giữ nguyên video cũ.
                </span>
            </div>

            <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; padding: 10px 0; margin-top: 8px;">
                💾 Lưu Thay Đổi
            </button>
        </form>
    </div>
</div>

<!-- ==========================================================================
     MODAL XEM TRỰC TIẾP VIDEO REVIEW
     ========================================================================== -->
<div id="watchVideoModal" class="admin-reels-overlay" style="display: none; background: rgba(8, 5, 18, 0.85); backdrop-filter: blur(15px); z-index: 11000;">
    <div class="admin-card" style="width: 100%; max-width: 420px; padding: 24px; position: relative; border-radius: 28px; background: rgba(20, 15, 38, 0.75); backdrop-filter: blur(25px); box-shadow: 0 30px 80px rgba(0,0,0,0.9), 0 0 40px rgba(240, 78, 35, 0.25), inset 0 1px 0 rgba(255,255,255,0.2); overflow: hidden; border: 1.5px solid rgba(255,255,255,0.15); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
        
        <!-- Glowing Close Button -->
        <button type="button" style="position: absolute; top: 18px; right: 18px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #ffffff; font-size: 1rem; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.background='rgba(239, 68, 68, 0.8)'; this.style.borderColor='#ef4444'; this.style.transform='rotate(90deg)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.transform='rotate(0deg)';" onclick="closeWatchVideoModal()">✕</button>
        
        <!-- Premium Pulsing Live Dot Header -->
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
            <span style="display: inline-block; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; box-shadow: 0 0 12px #ef4444; animation: blink 1.2s infinite alternate;"></span>
            <h4 id="watchVideoTitle" style="color: #ffffff; font-size: 1.05rem; font-weight: 800; margin: 0; padding-right: 32px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Outfit', sans-serif; text-shadow: 0 0 8px rgba(255,255,255,0.3);">🎥 Xem Video Review</h4>
        </div>
        
        <!-- Local HTML5 Video Player Container -->
        <div id="watchLocalContainer" style="display: none; width: 100%; height: 520px; background-color: #000000; border-radius: 20px; overflow: hidden; border: 1.5px solid rgba(255,255,255,0.12); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <video id="watchVideoPlayer" controls style="width: 100%; height: 100%; object-fit: cover;"></video>
        </div>

        <!-- YouTube Shorts Iframe Container -->
        <div id="watchYoutubeContainer" style="display: none; width: 100%; height: 520px; background-color: #000000; border-radius: 20px; overflow: hidden; border: 1.5px solid rgba(255,255,255,0.12); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <iframe id="watchYoutubePlayer" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width: 100%; height: 100%;"></iframe>
        </div>

        <!-- TikTok Embed Container -->
        <div id="watchTiktokContainer" style="display: none; width: 100%; height: 520px; background-color: #0c081c; border-radius: 20px; overflow: hidden; align-items: center; justify-content: center; padding: 24px; border: 1.5px solid rgba(255,255,255,0.12); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div style="text-align: center; color: rgba(255,255,255,0.9); padding: 10px;">
                <div style="font-size: 3rem; margin-bottom: 16px; filter: drop-shadow(0 0 10px rgba(255,255,255,0.3));">📱</div>
                <h5 style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 8px; font-family: 'Outfit', sans-serif;">Video Tiktok ngắn</h5>
                <p style="font-size: 0.8rem; color: rgba(255,255,255,0.6); line-height: 1.5; margin-bottom: 24px; padding: 0 10px;">Để có trải nghiệm mượt mà, tốc độ cao và đầy đủ tính năng tương tác của TikTok, hãy mở xem trực tiếp trên nền tảng nguồn!</p>
                <a id="watchTiktokLink" href="" target="_blank" class="btn-admin" style="background: var(--primary-grad); border: none; padding: 12px 28px; font-size: 0.85rem; font-weight: 700; border-radius: 12px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; color: #fff; box-shadow: 0 4px 15px rgba(240, 78, 35, 0.4); transition: all 0.25s;" onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 20px rgba(240, 78, 35, 0.6)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(240, 78, 35, 0.4)';">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    Mở Trên TikTok
                </a>
            </div>
        </div>
        
        <!-- Premium Meta Info Pill under player -->
        <div style="margin-top: 15px; display: flex; align-items: center; justify-content: space-between; font-size: 0.72rem; color: rgba(255,255,255,0.5); background: rgba(0,0,0,0.2); padding: 8px 14px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.06);">
            <span>🌐 Nguồn phát: Tự động nhận diện</span>
            <span style="color: #10b981; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%;"></span>
                Sẵn sàng phát
            </span>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Kiểm tra xem là Sửa (Edit) hay Thêm mới
    const hasEatery = <?php echo e($eatery ? 'true' : 'false'); ?>;
    const initLat = <?php echo e($eatery ? $eatery->latitude : 21.1182); ?>;
    const initLng = <?php echo e($eatery ? $eatery->longitude : 105.8394); ?>;
    let pickerMap;
    let marker;
    let isMapLocked = hasEatery;

    window.toggleMapLock = function() {
        const btn = document.getElementById('btnMapLockToggle');
        if (!btn) return;
        
        isMapLocked = !isMapLocked;
        
        if (isMapLocked) {
            btn.innerHTML = '🔒 Đã khóa click (Bấm để sửa vị trí)';
            btn.style.backgroundColor = 'var(--admin-danger-light)';
            btn.style.color = 'var(--admin-danger)';
            btn.style.borderColor = 'rgba(239, 68, 68, 0.15)';
        } else {
            btn.innerHTML = '🔓 Đang mở click (Bấm bản đồ để chỉnh)';
            btn.style.backgroundColor = 'var(--admin-success-light)';
            btn.style.color = 'var(--admin-success)';
            btn.style.borderColor = 'rgba(16, 185, 129, 0.15)';
        }
    };

    // 1. Chuyển đổi Tab làm việc chính
    window.switchSubTab = function(event, tabId) {
        // Store in localStorage to persist active tab across page reloads
        localStorage.setItem('activeAdminTab', tabId);

        // Toggle tab button active classes
        document.querySelectorAll('.admin-sub-tab-btn').forEach(btn => btn.classList.remove('active'));
        event.currentTarget.classList.add('active');
        
        // Toggle tab content visibility
        document.querySelectorAll('.admin-tab-section').forEach(section => section.style.display = 'none');
        const activeSection = document.getElementById(tabId);
        if (activeSection) {
            activeSection.style.display = 'block';
            
            // Critical Leaflet refresh:
            if (tabId === 'tab-info' && pickerMap) {
                setTimeout(() => {
                    pickerMap.invalidateSize();
                }, 100);
            }
        }
    };

    // 2. Chuyển đổi chế độ đăng video (Nhúng link vs Tải file)
    window.toggleUploadMode = function(mode) {
        const embedBtn = document.getElementById('uploadTabBtn-embed');
        const fileBtn = document.getElementById('uploadTabBtn-file');
        const embedContainer = document.getElementById('uploadContainer-embed');
        const fileContainer = document.getElementById('uploadContainer-file');
        
        const urlInput = document.getElementById('videoUrlInput');
        const fileInput = document.getElementById('videoFileInput');

        if (mode === 'embed') {
            embedBtn.classList.remove('btn-admin-secondary');
            embedBtn.classList.add('btn-admin-primary');
            embedBtn.style.backgroundColor = '';
            embedBtn.style.borderColor = '';
            
            fileBtn.classList.remove('btn-admin-primary');
            fileBtn.classList.add('btn-admin-secondary');
            fileBtn.style.backgroundColor = 'transparent';
            fileBtn.style.borderColor = 'transparent';
            
            embedContainer.style.display = 'block';
            fileContainer.style.display = 'none';
            
            urlInput.setAttribute('required', 'required');
            fileInput.removeAttribute('required');
        } else {
            fileBtn.classList.remove('btn-admin-secondary');
            fileBtn.classList.add('btn-admin-primary');
            fileBtn.style.backgroundColor = '';
            fileBtn.style.borderColor = '';
            
            embedBtn.classList.remove('btn-admin-primary');
            embedBtn.classList.add('btn-admin-secondary');
            embedBtn.style.backgroundColor = 'transparent';
            embedBtn.style.borderColor = 'transparent';
            
            fileContainer.style.display = 'block';
            embedContainer.style.display = 'none';
            
            fileInput.setAttribute('required', 'required');
            urlInput.removeAttribute('required');
        }
    };

    // 3. Edit Video Modal Logic
    window.openEditVideoModal = function(id, title, eateryId, videoUrl, videoType) {
        const form = document.getElementById('editVideoForm');
        form.setAttribute('action', '/admin/videos/' + id);
        
        document.getElementById('editVideoTitle').value = title;
        
        if (videoType === 'local') {
            toggleEditUploadMode('file');
            document.getElementById('editVideoUrlInput').value = '';
        } else {
            toggleEditUploadMode('embed');
            document.getElementById('editVideoUrlInput').value = videoUrl;
        }
        
        document.getElementById('editVideoModal').style.display = 'flex';
    };

    window.closeEditVideoModal = function() {
        document.getElementById('editVideoModal').style.display = 'none';
    };

    // 3.5. Xem trực tiếp Video Review Modal Logic
    window.openWatchVideoModal = function(title, url, type) {
        document.getElementById('watchVideoTitle').innerText = title;
        
        // Hide all containers by default
        document.getElementById('watchLocalContainer').style.display = 'none';
        document.getElementById('watchYoutubeContainer').style.display = 'none';
        document.getElementById('watchTiktokContainer').style.display = 'none';
        
        // Reset player sources
        document.getElementById('watchVideoPlayer').pause();
        document.getElementById('watchVideoPlayer').src = '';
        document.getElementById('watchYoutubePlayer').src = '';
        
        if (type === 'youtube_shorts') {
            const ytMatch = url.match(/(?:shorts\/|watch\?v=)([a-zA-Z0-9_-]+)/);
            if (ytMatch) {
                document.getElementById('watchYoutubeContainer').style.display = 'block';
                document.getElementById('watchYoutubePlayer').src = 'https://www.youtube.com/embed/' + ytMatch[1] + '?autoplay=1';
            } else {
                document.getElementById('watchLocalContainer').style.display = 'block';
                document.getElementById('watchVideoPlayer').src = url;
            }
        } else if (type === 'tiktok') {
            const ttMatch = url.match(/video\/(\d+)/);
            if (ttMatch) {
                document.getElementById('watchYoutubeContainer').style.display = 'block';
                document.getElementById('watchYoutubePlayer').src = 'https://www.tiktok.com/embed/v2/' + ttMatch[1];
            } else {
                document.getElementById('watchTiktokContainer').style.display = 'flex';
                document.getElementById('watchTiktokLink').href = url;
            }
        } else {
            // Local file or standard direct mp4 url
            document.getElementById('watchLocalContainer').style.display = 'block';
            document.getElementById('watchVideoPlayer').src = url;
            document.getElementById('watchVideoPlayer').load();
            document.getElementById('watchVideoPlayer').play().catch(e => console.log('Autoplay blocked'));
        }
        
        document.getElementById('watchVideoModal').style.display = 'flex';
    };

    window.closeWatchVideoModal = function() {
        document.getElementById('watchVideoPlayer').pause();
        document.getElementById('watchVideoPlayer').src = '';
        document.getElementById('watchYoutubePlayer').src = '';
        document.getElementById('watchVideoModal').style.display = 'none';
    };

    // 4. Xem chi tiết Món ăn Modal Logic
    window.openViewDishModal = function(name, price, description, imagePath, isSignature) {
        document.getElementById('viewDishName').innerText = name;
        document.getElementById('viewDishPrice').innerText = price;
        document.getElementById('viewDishDesc').innerText = description || "Không có mô tả chi tiết.";
        document.getElementById('viewDishImg').src = imagePath;
        
        document.getElementById('viewDishBadge').style.display = parseInt(isSignature) === 1 ? 'inline-flex' : 'none';
        document.getElementById('viewDishModal').style.display = 'flex';
    };

    window.closeViewDishModal = function() {
        document.getElementById('viewDishModal').style.display = 'none';
    };

    // 4.5. Xem chi tiết Hợp đồng Modal Logic
    window.openViewContractModal = function(supplier, items, signed, expired, img) {
        document.getElementById('viewContractSupplier').innerText = supplier;
        document.getElementById('viewContractItems').innerText = items;
        document.getElementById('viewContractSigned').innerText = signed;
        document.getElementById('viewContractExpired').innerText = expired;
        document.getElementById('viewContractImg').src = img;
        document.getElementById('viewContractModal').style.display = 'flex';
    };

    window.closeViewContractModal = function() {
        document.getElementById('viewContractModal').style.display = 'none';
    };

    // 4.6. Xem chi tiết Hóa đơn Modal Logic
    window.openViewInvoiceModal = function(supplier, summary, date, img) {
        document.getElementById('viewInvoiceSupplier').innerText = supplier;
        document.getElementById('viewInvoiceSummary').innerText = summary;
        document.getElementById('viewInvoiceDate').innerText = date;
        document.getElementById('viewInvoiceImg').src = img;
        document.getElementById('viewInvoiceModal').style.display = 'flex';
    };

    window.closeViewInvoiceModal = function() {
        document.getElementById('viewInvoiceModal').style.display = 'none';
    };

    // 4.7. Xem chi tiết Chứng nhận VSATTP Modal Logic
    window.openViewCertModal = function(number, issuer, issued, expired, img) {
        document.getElementById('viewCertNumber').innerText = "Số hiệu: " + number;
        document.getElementById('viewCertIssuedBy').innerText = issuer;
        document.getElementById('viewCertIssuedAt').innerText = issued;
        document.getElementById('viewCertExpiredAt').innerText = expired;
        document.getElementById('viewCertImg').src = img || "https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=800&q=80";
        document.getElementById('viewCertModal').style.display = 'flex';
    };

    window.closeViewCertModal = function() {
        document.getElementById('viewCertModal').style.display = 'none';
    };

    // 4.8. Xem chi tiết Nhật ký kiểm tra VSATTP Modal Logic
    window.openViewLogModal = function(date, origin, storage, checker) {
        document.getElementById('viewLogDate').innerText = "Ngày kiểm tra: " + date;
        document.getElementById('viewLogOrigin').innerText = origin;
        document.getElementById('viewLogStorage').innerText = storage;
        document.getElementById('viewLogChecker').innerText = checker;
        document.getElementById('viewLogModal').style.display = 'flex';
    };

    window.closeViewLogModal = function() {
        document.getElementById('viewLogModal').style.display = 'none';
    };

    // 5. Sửa Món ăn Modal Logic
    window.openEditDishModal = function(id, name, price, description, imagePath, isSignature) {
        const form = document.getElementById('editDishForm');
        form.setAttribute('action', '/admin/dishes/' + id);
        
        document.getElementById('editDishNameInput').value = name;
        document.getElementById('editDishPriceInput').value = price;
        document.getElementById('editDishDescInput').value = description;
        document.getElementById('editDishImageUrlInput').value = imagePath.startsWith('http') ? imagePath : '';
        
        const signatureCheckbox = document.getElementById('editDishSignatureInput');
        if (parseInt(isSignature) === 1) {
            signatureCheckbox.checked = true;
        } else {
            signatureCheckbox.checked = false;
        }
        
        document.getElementById('editDishModal').style.display = 'flex';
    };

    window.closeEditDishModal = function() {
        document.getElementById('editDishModal').style.display = 'none';
    };

    window.toggleEditUploadMode = function(mode) {
        const embedBtn = document.getElementById('editTabBtn-embed');
        const fileBtn = document.getElementById('editTabBtn-file');
        const embedContainer = document.getElementById('editContainer-embed');
        const fileContainer = document.getElementById('editContainer-file');
        
        const urlInput = document.getElementById('editVideoUrlInput');
        const fileInput = document.getElementById('editVideoFileInput');

        if (mode === 'embed') {
            embedBtn.classList.remove('btn-admin-secondary');
            embedBtn.classList.add('btn-admin-accent');
            embedBtn.style.backgroundColor = '';
            embedBtn.style.borderColor = '';
            
            fileBtn.classList.remove('btn-admin-accent');
            fileBtn.classList.add('btn-admin-secondary');
            fileBtn.style.backgroundColor = 'transparent';
            fileBtn.style.borderColor = 'transparent';
            
            embedContainer.style.display = 'block';
            fileContainer.style.display = 'none';
            
            urlInput.setAttribute('required', 'required');
            fileInput.removeAttribute('required');
        } else {
            fileBtn.classList.remove('btn-admin-secondary');
            fileBtn.classList.add('btn-admin-accent');
            fileBtn.style.backgroundColor = '';
            fileBtn.style.borderColor = '';
            
            embedBtn.classList.remove('btn-admin-accent');
            embedBtn.classList.add('btn-admin-secondary');
            embedBtn.style.backgroundColor = 'transparent';
            embedBtn.style.borderColor = 'transparent';
            
            fileContainer.style.display = 'block';
            embedContainer.style.display = 'none';
            
            fileInput.removeAttribute('required');
            urlInput.removeAttribute('required');
        }
    };

    document.addEventListener("DOMContentLoaded", function() {
        // Restore active sub tab if stored in localStorage
        const storedTabId = localStorage.getItem('activeAdminTab');
        if (storedTabId) {
            const tabBtn = Array.from(document.querySelectorAll('.admin-sub-tab-btn')).find(btn => {
                const clickAttr = btn.getAttribute('onclick');
                return clickAttr && clickAttr.includes(storedTabId);
            });
            if (tabBtn) {
                window.switchSubTab({ currentTarget: tabBtn }, storedTabId);
            }
        }

        // Khởi tạo bản đồ chọn tọa độ
        pickerMap = L.map('pickerMap', {
            zoomControl: true
        }).setView([initLat, initLng], hasEatery ? 15 : 13);

        // Lớp OpenStreetMap light mode sạch sẽ, tối giản cực đẹp
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(pickerMap);

        // Biểu tượng Marker
        const customIcon = L.divIcon({
            html: `<div style="background-color: var(--admin-primary); width: 22px; height: 22px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">📍</div>`,
            className: 'custom-leaflet-marker',
            iconSize: [22, 22],
            iconAnchor: [11, 11]
        });

        // Vẽ marker ban đầu nếu có dữ liệu
        if (hasEatery) {
            marker = L.marker([initLat, initLng], { icon: customIcon }).addTo(pickerMap);
        }

        // Sự kiện click bản đồ nhặt tọa độ điền tự động vào Form
        pickerMap.on('click', function(e) {
            if (isMapLocked) {
                alert("📍 Bản đồ định vị đang được KHÓA để tránh click nhầm vị trí.\n\nHãy nhấn nút '🔒 Đã khóa click' ở phía trên bản đồ để MỞ KHÓA nếu bạn muốn chỉnh sửa tọa độ của quán!");
                return;
            }

            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            
            // Cập nhật giá trị 2 ô input
            document.getElementById('latInput').value = lat;
            document.getElementById('lngInput').value = lng;
            
            // Di chuyển hoặc vẽ mới marker định vị
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, { icon: customIcon }).addTo(pickerMap);
            }
            
            pickerMap.panTo(e.latlng);
        });

        // Hỗ trợ cập nhật marker khi nhập tọa độ thủ công bằng tay
        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');

        function updateMarkerFromInput() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            
            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                const newLatLng = new L.LatLng(lat, lng);
                
                if (marker) {
                    marker.setLatLng(newLatLng);
                } else {
                    marker = L.marker(newLatLng, { icon: customIcon }).addTo(pickerMap);
                }
                
                pickerMap.setView(newLatLng, 15);
            }
        }

        latInput.addEventListener('change', updateMarkerFromInput);
        lngInput.addEventListener('change', updateMarkerFromInput);

        // Xử lý trích xuất tọa độ từ Google Maps Link tự động
        const btnExtract = document.getElementById('btnExtractCoords');
        const gmapsInput = document.getElementById('gmapsUrlInput');
        const helperText = document.getElementById('gmapsHelperText');

        btnExtract.addEventListener('click', function() {
            const url = gmapsInput.value.trim();
            if (!url) {
                helperText.innerText = "❌ Vui lòng dán đường dẫn Google Maps trước!";
                helperText.style.color = "var(--admin-danger)";
                return;
            }

            helperText.innerText = "⏳ Đang giải mã đường dẫn và trích xuất tọa độ...";
            helperText.style.color = "var(--admin-primary)";
            btnExtract.disabled = true;

            // Gửi request lên backend endpoint để giải mã link rút gọn và trích xuất tọa độ
            fetch('/admin/parse-google-maps', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ url: url })
            })
            .then(response => response.json())
            .then(data => {
                btnExtract.disabled = false;
                if (data.success) {
                    const lat = parseFloat(data.latitude).toFixed(6);
                    const lng = parseFloat(data.longitude).toFixed(6);

                    // Điền vào form
                    latInput.value = lat;
                    lngInput.value = lng;

                    // Di chuyển marker trên bản đồ
                    const newLatLng = new L.LatLng(lat, lng);
                    if (marker) {
                        marker.setLatLng(newLatLng);
                    } else {
                        marker = L.marker(newLatLng, { icon: customIcon }).addTo(pickerMap);
                    }
                    pickerMap.setView(newLatLng, 15);
                    helperText.innerText = "✅ Trích xuất tọa độ thành công!";
                    helperText.style.color = "var(--admin-success)";
                    
                    // Mở khóa map tự động khi trích xuất thành công để admin thấy marker di chuyển
                    isMapLocked = false;
                    const mapBtn = document.getElementById('btnMapLockToggle');
                    if (mapBtn) {
                        mapBtn.innerHTML = '🔓 Đang mở click (Bấm bản đồ để chỉnh)';
                        mapBtn.style.backgroundColor = 'var(--admin-success-light)';
                        mapBtn.style.color = 'var(--admin-success)';
                        mapBtn.style.borderColor = 'rgba(16, 185, 129, 0.15)';
                    }
                } else {
                    helperText.innerText = "❌ " + data.message;
                    helperText.style.color = "var(--admin-danger)";
                }
            })
            .catch(error => {
                btnExtract.disabled = false;
                console.error("Error:", error);
                helperText.innerText = "❌ Lỗi kết nối hệ thống. Vui lòng kiểm tra lại.";
                helperText.style.color = "var(--admin-danger)";
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FOODDA\FOOD_MAP\resources\views/admin/eatery-form.blade.php ENDPATH**/ ?>