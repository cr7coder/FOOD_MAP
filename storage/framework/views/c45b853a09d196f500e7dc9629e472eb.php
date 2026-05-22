<?php $__env->startSection('title', 'Bảng Điều Khiển Quản Trị - Dong Anh Food Map'); ?>

<?php $__env->startSection('content'); ?>
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <div class="admin-layout">
        
        <!-- Sidebar Navigation (Toggles Tabs) -->
        <aside class="admin-sidebar glass-panel">
            <h3 style="font-size: 1.1rem; color: var(--primary); margin-bottom: 20px; border-bottom: 1px solid var(--border-glow); padding-bottom: 8px; font-family: var(--font-heading); font-weight: 700;">
                ⚙️ Menu Quản Trị
            </h3>
            
            <button type="button" class="admin-menu-item active" id="adminTabBtn-overview" onclick="switchAdminTab('overview')" style="width: 100%; text-align: left; background: transparent; border: none; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; gap: 10px; color: var(--text-muted); font-family: var(--font-heading); font-weight: 600; padding: 12px 16px; border-radius: 8px; transition: all 0.25s;">
                <span>📊</span> Tổng quan & Địa điểm
            </button>
            
            <button type="button" class="admin-menu-item" id="adminTabBtn-videos" onclick="switchAdminTab('videos')" style="width: 100%; text-align: left; background: transparent; border: none; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; gap: 10px; color: var(--text-muted); font-family: var(--font-heading); font-weight: 600; padding: 12px 16px; border-radius: 8px; transition: all 0.25s;">
                <span>🎥</span> Quản lý Video Review
            </button>
            

            
            <div style="border-top: 1px solid var(--border-glow); margin: 15px 0;"></div>
            
            <?php if(session('user_role') === 'admin' || (session('user_role') === 'seller' && $eateries->count() === 0)): ?>
            <a href="/admin/eateries/create" class="admin-menu-item">
                <span>➕</span> Đăng ký địa điểm
            </a>
            <?php endif; ?>
            <a href="/" class="admin-menu-item">
                <span>🗺️</span> Bản đồ Khách hàng
            </a>
        </aside>
        
        <!-- Main Content Area -->
        <div class="admin-content">
            
            <!-- GOOGLE DRIVE STORAGE STATUS BANNER -->
            <?php if(empty(env('GOOGLE_DRIVE_CLIENT_ID')) || empty(env('GOOGLE_DRIVE_CLIENT_SECRET')) || empty(env('GOOGLE_DRIVE_REFRESH_TOKEN'))): ?>
                <div class="glass-panel" style="background: rgba(255, 165, 0, 0.08); border-color: rgba(255, 165, 0, 0.3); padding: 14px 20px; border-radius: 12px; color: rgba(255, 200, 100, 0.95); margin-bottom: 25px; font-size: 0.88rem; line-height: 1.5; display: flex; align-items: flex-start; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                    <span style="font-size: 1.3rem; filter: drop-shadow(0 0 4px rgba(255, 165, 0, 0.4));">☁️</span>
                    <div>
                        <strong style="color: #ffb74d;">Lưu trữ Google Drive đang hoạt động ở chế độ Giả Lập!</strong><br>
                        Tất cả ảnh/video đăng tải sẽ được lưu tạm tại thư mục cục bộ (`public/uploads`) để đảm bảo hệ thống không bị lỗi. Hãy cấu hình các biến môi trường sau trong file `.env` của bạn để kích hoạt đồng bộ hóa Google Drive thực tế (0MB server space):<br>
                        <code style="background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 4px; color: #ffb74d; font-family: monospace; display: inline-block; margin-top: 4px; font-size: 0.82rem;">GOOGLE_DRIVE_CLIENT_ID</code>
                        <code style="background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 4px; color: #ffb74d; font-family: monospace; display: inline-block; margin-top: 4px; font-size: 0.82rem;">GOOGLE_DRIVE_CLIENT_SECRET</code>
                        <code style="background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 4px; color: #ffb74d; font-family: monospace; display: inline-block; margin-top: 4px; font-size: 0.82rem;">GOOGLE_DRIVE_REFRESH_TOKEN</code>
                        <code style="background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 4px; color: #ffb74d; font-family: monospace; display: inline-block; margin-top: 4px; font-size: 0.82rem;">GOOGLE_DRIVE_FOLDER_ID</code>
                    </div>
                </div>
            <?php else: ?>
                <div class="glass-panel" style="background: rgba(46, 204, 113, 0.08); border-color: rgba(46, 204, 113, 0.3); padding: 14px 20px; border-radius: 12px; color: #2ecc71; margin-bottom: 25px; font-size: 0.88rem; line-height: 1.5; display: flex; align-items: flex-start; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                    <span style="font-size: 1.3rem;">⚡</span>
                    <div>
                        <strong style="color: #2ecc71;">Đã kết nối lưu trữ Google Drive thành công!</strong><br>
                        Tất cả tệp ảnh cơ sở, ảnh món ăn và video review khi đăng tải sẽ tự động tải lên lưu trữ đám mây Google Drive của bạn và tự động sinh link phát trực tuyến tốc độ cao (tiết kiệm 100% tài nguyên ổ cứng máy chủ!).
                    </div>
                </div>
            <?php endif; ?>

            <!-- SUCCESS ALERT BANNER -->
            <?php if(session('success')): ?>
                <div class="glass-panel" style="background: rgba(32, 178, 170, 0.1); border-color: var(--accent); padding: 14px 20px; border-radius: 8px; color: var(--accent); margin-bottom: 30px; font-size: 0.95rem;">
                    🎉 <strong>Thành công!</strong> <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <!-- ==========================================================================
                 TAB 1: SYSTEM OVERVIEW & EATERIES CRUD
                 ========================================================================== -->
            <div id="adminTab-overview" class="admin-tab-content">
                <!-- Welcome Header -->
                <div style="margin-bottom: 30px;">
                    <h1 style="font-size: 2rem; font-family: var(--font-heading); background: var(--primary-grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block; padding: 8px 0; line-height: 1.3;">
                        Báo cáo Hệ thống Bản đồ số
                    </h1>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 4px;">
                        Tổng quan dữ liệu ẩm thực, khách sạn, cafe và đặc sản địa phương trên địa bàn Đông Anh
                    </p>
                </div>
                
                <!-- Stats Grid Widget -->
                <div class="stats-grid" style="margin-bottom: 30px;">
                    <div class="stat-card glass-panel">
                        <div>
                            <div class="stat-value"><?php echo e($stats['total_eateries']); ?></div>
                            <div class="stat-label">Tổng số địa điểm</div>
                        </div>
                        <span class="stat-icon">📍</span>
                    </div>
                    <div class="stat-card glass-panel">
                        <div>
                            <div class="stat-value"><?php echo e($stats['total_categories']); ?></div>
                            <div class="stat-label">Danh mục dịch vụ</div>
                        </div>
                        <span class="stat-icon">🍔</span>
                    </div>
                    <div class="stat-card glass-panel">
                        <div>
                            <div class="stat-value"><?php echo e($stats['total_communes']); ?></div>
                            <div class="stat-label">Xã / Thị trấn số hóa</div>
                        </div>
                        <span class="stat-icon">🌾</span>
                    </div>
                    <div class="stat-card glass-panel">
                        <div>
                            <div class="stat-value"><?php echo e($stats['total_reviews']); ?></div>
                            <div class="stat-label">Tổng số đánh giá</div>
                        </div>
                        <span class="stat-icon">💬</span>
                    </div>
                </div>
                
                <!-- Eateries CRUD Management Area -->
                <div class="glass-panel" style="padding: 28px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-glow); padding-bottom: 14px;">
                        <h2 style="font-size: 1.3rem; font-family: var(--font-heading); display: flex; align-items: center; gap: 8px;">
                            <span>📋</span> Danh sách các Cơ sở / Địa điểm
                        </h2>
                        <?php if(session('user_role') === 'admin' || (session('user_role') === 'seller' && $eateries->count() === 0)): ?>
                        <a href="/admin/eateries/create" class="btn-primary" style="font-size: 0.85rem; padding: 8px 16px;">
                            <span>➕</span> Đăng ký địa điểm
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($eateries->count() > 0): ?>
                        <div style="overflow-x: auto;">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Ảnh</th>
                                        <th>Tên địa điểm</th>
                                        <th>Phân loại</th>
                                        <th>Xã / Khu vực</th>
                                        <th>Số điện thoại</th>
                                        <th>Chỉ số</th>
                                        <th style="text-align: center;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $eateries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo e($eat->image_path ?: 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=300&q=80'); ?>" style="width: 54px; height: 54px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-glow);" alt="<?php echo e($eat->name); ?>">
                                            </td>
                                            <td>
                                                <strong style="color: var(--text-main); display: block;"><?php echo e($eat->name); ?></strong>
                                                <span style="font-size: 0.75rem; color: var(--text-muted); display: block; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">📍 <?php echo e($eat->address); ?></span>
                                            </td>
                                            <td>
                                                <span class="tag-badge-accent" style="font-size: 0.7rem; padding: 2px 8px;"><?php echo e($eat->category->icon); ?> <?php echo e($eat->category->name); ?></span>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.9rem;">Xã <?php echo e($eat->commune->name); ?></span>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.9rem; font-family: var(--font-heading); color: var(--primary);"><?php echo e($eat->phone ?: 'Chưa có'); ?></span>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 2px;">
                                                    <span style="color: #ffc107; font-weight: 700;">★ <?php echo e(number_format($eat->average_rating, 1)); ?></span>
                                                    <span style="font-size: 0.7rem; color: var(--text-muted);">(<?php echo e($eat->reviews->count()); ?> reviews)</span>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div style="display: inline-flex; gap: 8px;">
                                                    <a href="/dia-diem/dac-san/<?php echo e($eat->slug); ?>" target="_blank" class="btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 6px;">
                                                        Xem
                                                    </a>
                                                    <a href="/admin/eateries/<?php echo e($eat->id); ?>/edit" class="btn-accent" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 6px;">
                                                        Sửa
                                                    </a>
                                                    <?php if(session('user_role') === 'admin'): ?>
                                                    <form action="/admin/eateries/<?php echo e($eat->id); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa điểm này khỏi Bản đồ số Đông Anh không?')" style="display: inline;">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 6px; background: var(--primary-hover);">
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
                        <div style="text-align: center; padding: 40px 0; color: var(--text-muted); display: flex; flex-direction: column; align-items: center; gap: 14px;">
                            <p style="font-size: 1.1rem; margin-bottom: 0;">📭 Chưa có địa điểm nào được nhập lên hệ thống.</p>
                            <?php if(session('user_role') === 'admin'): ?>
                            <a href="/admin/eateries/create" class="btn-primary" style="font-size: 0.9rem;">Thêm địa điểm đầu tiên</a>
                            <?php elseif(session('user_role') === 'seller'): ?>
                            <p style="font-size: 0.9rem; color: var(--text-muted); max-width: 450px; line-height: 1.6;">Chào mừng Chủ quán! Tài khoản của bạn chưa liên kết với địa điểm kinh doanh nào trên Bản đồ số. Hãy đăng ký quán của bạn ngay để khách hàng có thể khám phá và đặt hàng!</p>
                            <a href="/admin/eateries/create" class="btn-primary" style="font-size: 0.9rem; padding: 10px 24px; border-radius: 10px;">➕ Đăng ký địa điểm kinh doanh của tôi</a>
                            <?php else: ?>
                            <p style="font-size: 0.9rem; color: var(--text-muted);">Vui lòng liên hệ Ban quản trị hệ thống để đăng ký và được gán quyền quản lý địa điểm của bạn.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ==========================================================================
                 TAB 2: VIDEO REELS REVIEWS MANAGEMENT
                 ========================================================================== -->
            <div id="adminTab-videos" class="admin-tab-content" style="display: none;">
                <!-- Section Header -->
                <div style="margin-bottom: 25px;">
                    <h1 style="font-size: 2rem; font-family: var(--font-heading); background: var(--primary-grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block; padding: 8px 0; line-height: 1.3;">
                        Quản lý Video Review Đặc sản 🎥
                    </h1>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 4px;">
                        Tạo trải nghiệm Tóp Tóp Food Tour sinh động. Hỗ trợ nhúng link TikTok & YouTube Shorts để tối ưu hóa bộ nhớ 0MB!
                    </p>
                </div>

                <!-- Video Stats Cards -->
                <div class="stats-grid" style="margin-bottom: 25px;">
                    <div class="stat-card glass-panel">
                        <span class="stat-icon">🎬</span>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo e($videos->count()); ?></span>
                            <span class="stat-label">Tổng video review</span>
                        </div>
                    </div>
                    <div class="stat-card glass-panel" style="border-left: 3px solid #28a745;">
                        <span class="stat-icon">✅</span>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo e($videos->where('status', 'approved')->count()); ?></span>
                            <span class="stat-label">Đã phê duyệt công khai</span>
                        </div>
                    </div>
                    <div class="stat-card glass-panel" style="border-left: 3px solid #ffc107;">
                        <span class="stat-icon" style="animation: pulse 1.5s infinite;">⏳</span>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo e($videos->where('status', 'pending')->count()); ?></span>
                            <span class="stat-label">Đang chờ kiểm duyệt</span>
                        </div>
                    </div>
                </div>

                <!-- Main Work Area (Split-Screen Form & List) -->
                <div class="split-screen" style="display: flex; gap: 24px; flex-wrap: wrap;">
                    
                    <!-- Left Sidebar: Add New Video Form -->
                    <div class="glass-panel" style="flex: 1; min-width: 320px; padding: 22px; display: flex; flex-direction: column; gap: 20px; max-width: 380px; height: fit-content;">
                        <h3 style="font-size: 1.1rem; color: var(--primary); border-bottom: 1px solid var(--border-glow); padding-bottom: 10px; font-family: var(--font-heading); font-weight: 700; display: flex; align-items: center; gap: 8px;">
                            <span>✨</span> Đăng Video Review Mới
                        </h3>

                        <form action="<?php echo e(route('admin.video.store')); ?>" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 16px;">
                            <?php echo csrf_field(); ?>
                            
                            <div class="review-form-group">
                                <label class="review-form-label">Tiêu đề video *</label>
                                <input type="text" name="title" required placeholder="Ví dụ: Ăn sập chợ Đông Anh ngon đỉnh..." class="form-input" style="font-size: 0.85rem;">
                            </div>

                            <div class="review-form-group">
                                <label class="review-form-label">Chọn cơ sở kinh doanh *</label>
                                <select name="eatery_id" required class="form-input" style="font-size: 0.85rem;">
                                    <option value="" disabled selected>-- Chọn cơ sở liên kết --</option>
                                    <?php $__currentLoopData = $eateries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($eat->id); ?>"><?php echo e($eat->name); ?> (<?php echo e($eat->commune->name); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Upload Type Custom Tabs -->
                            <div style="background: rgba(255,255,255,0.05); padding: 4px; border-radius: 8px; display: flex; gap: 4px; border: 1px solid var(--border-glow);">
                                <button type="button" id="uploadTabBtn-embed" onclick="toggleUploadMode('embed')" class="btn-accent" style="flex: 1; font-size: 0.75rem; padding: 6px 0; border-radius: 6px; justify-content: center; background: var(--primary-grad); border: none;">
                                    🔗 Nhúng Link (0MB)
                                </button>
                                <button type="button" id="uploadTabBtn-file" onclick="toggleUploadMode('file')" class="btn-secondary" style="flex: 1; font-size: 0.75rem; padding: 6px 0; border-radius: 6px; justify-content: center; background: transparent; border: none; color: var(--text-muted);">
                                    📤 Tải Video lên
                                </button>
                            </div>

                            <!-- Embed Section (Default) -->
                            <div id="uploadContainer-embed" class="review-form-group" style="display: block;">
                                <label class="review-form-label">Đường dẫn Video (TikTok / YouTube Shorts) *</label>
                                <input type="url" id="videoUrlInput" name="video_url" placeholder="https://www.tiktok.com/@.../video/..." class="form-input" style="font-size: 0.82rem;">
                                <span style="font-size: 0.72rem; color: var(--text-muted); display: block; margin-top: 4px; line-height: 1.3;">
                                    💡 Khuyên dùng: Dán link TikTok Reels hoặc YouTube Shorts. Hệ thống tự động nhúng, không tiêu thụ ổ cứng của bạn!
                                </span>
                            </div>

                            <!-- File Upload Section (Hidden by default) -->
                            <div id="uploadContainer-file" class="review-form-group" style="display: none;">
                                <label class="review-form-label">Chọn File Video từ thiết bị *</label>
                                <input type="file" id="videoFileInput" name="video_file" accept="video/mp4,video/quicktime,video/x-matroska" class="form-input" style="font-size: 0.82rem; padding: 8px 12px;">
                                <span style="font-size: 0.72rem; color: var(--text-muted); display: block; margin-top: 4px;">
                                    ⚠️ Chấp nhận file MP4 dung lượng &lt; 20MB để tối ưu dung lượng lưu trữ.
                                </span>
                            </div>

                            <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px 0; font-size: 0.9rem; font-weight: 700; border-radius: 8px; margin-top: 10px;">
                                🚀 Đăng Video Review
                            </button>
                        </form>
                    </div>

                    <!-- Right Column: Video Review Management Table -->
                    <div class="glass-panel" style="flex: 2; min-width: 480px; padding: 22px;">
                        <h3 style="font-size: 1.1rem; color: var(--primary); border-bottom: 1px solid var(--border-glow); padding-bottom: 10px; margin-bottom: 15px; font-family: var(--font-heading); font-weight: 700; display: flex; align-items: center; gap: 8px;">
                            <span>🎬</span> Danh Sách Video Review
                        </h3>

                        <?php if($videos->count() > 0): ?>
                            <div class="table-container" style="overflow-x: auto;">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Video & Tiêu đề</th>
                                            <th>Quán ăn liên kết</th>
                                            <th>Kiểu</th>
                                            <th>Tương tác</th>
                                            <th>Trạng thái</th>
                                            <th style="text-align: center;">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <div style="display: flex; align-items: center; gap: 10px;">
                                                        <div style="position: relative; width: 45px; height: 60px; border-radius: 6px; overflow: hidden; background: #000; flex-shrink: 0; border: 1px solid var(--border-glow);">
                                                            <img src="<?php echo e($vid->thumbnail_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=100&q=80'); ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8;">
                                                            <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 12px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.8));">▶️</span>
                                                        </div>
                                                        <div style="display: flex; flex-direction: column;">
                                                            <span style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                                <?php echo e($vid->title); ?>

                                                            </span>
                                                            <span style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">
                                                                Đăng bởi: <?php echo e($vid->user->name); ?>

                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div style="display: flex; flex-direction: column;">
                                                        <span style="font-weight: 600; font-size: 0.82rem; color: var(--text-main);"><?php echo e($vid->eatery->name); ?></span>
                                                        <span style="font-size: 0.72rem; color: var(--primary);"><?php echo e($vid->eatery->category->name); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if($vid->video_type === 'tiktok'): ?>
                                                        <span style="background: #000; color: #00f2fe; border: 1px solid #fe0979; font-size: 0.65rem; padding: 3px 8px; border-radius: 20px; font-weight: 800; text-shadow: 0 0 5px #fe0979;">TikTok</span>
                                                    <?php elseif($vid->video_type === 'youtube_shorts'): ?>
                                                        <span style="background: #ff0000; color: #fff; font-size: 0.65rem; padding: 3px 8px; border-radius: 20px; font-weight: 800; box-shadow: 0 0 8px rgba(255,0,0,0.4);">Shorts</span>
                                                    <?php else: ?>
                                                        <span style="background: var(--primary-hover); color: #fff; font-size: 0.65rem; padding: 3px 8px; border-radius: 20px; font-weight: 700;">MP4 File</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span style="font-size: 0.82rem; font-weight: 700; color: #e83e8c;">❤️ <?php echo e(number_format($vid->likes_count)); ?></span>
                                                </td>
                                                <td>
                                                    <?php if($vid->status === 'approved'): ?>
                                                        <span style="color: #28a745; background: rgba(40,167,69,0.1); border: 1px solid rgba(40,167,69,0.3); font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                                            <span style="width: 6px; height: 6px; background: #28a745; border-radius: 50%; display: inline-block;"></span> Đã Duyệt
                                                        </span>
                                                    <?php elseif($vid->status === 'pending'): ?>
                                                        <span style="color: #ffc107; background: rgba(255,193,7,0.1); border: 1px solid rgba(255,193,7,0.3); font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; animation: pulse 2s infinite;">
                                                            <span style="width: 6px; height: 6px; background: #ffc107; border-radius: 50%; display: inline-block;"></span> Chờ Duyệt
                                                        </span>
                                                    <?php else: ?>
                                                        <span style="color: #dc3545; background: rgba(220,53,69,0.1); border: 1px solid rgba(220,53,69,0.3); font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                                            <span style="width: 6px; height: 6px; background: #dc3545; border-radius: 50%; display: inline-block;"></span> Từ Chối
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                    <div style="display: inline-flex; gap: 8px;">
                                                        <?php if(session('user_role') === 'admin' && $vid->status === 'pending'): ?>
                                                            <form action="<?php echo e(route('admin.video.approve', $vid->id)); ?>" method="POST" style="display: inline;">
                                                                <?php echo csrf_field(); ?>
                                                                <button type="submit" class="btn-accent" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 6px; background: #28a745; border: none; color: #fff; font-weight: 700;">
                                                                    Duyệt
                                                                </button>
                                                            </form>
                                                            <form action="<?php echo e(route('admin.video.reject', $vid->id)); ?>" method="POST" style="display: inline;">
                                                                <?php echo csrf_field(); ?>
                                                                <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 6px; background: #ffc107; border: none; color: #000; font-weight: 700;">
                                                                    Từ Chối
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                        
                                                        <?php if(session('user_role') === 'admin' || $vid->user_id === session('user_id') || $vid->eatery->user_id === session('user_id')): ?>
                                                            <button type="button" class="btn-accent" onclick="openEditVideoModal('<?php echo e($vid->id); ?>', '<?php echo e(addslashes($vid->title)); ?>', '<?php echo e($vid->eatery_id); ?>', '<?php echo e($vid->video_url); ?>', '<?php echo e($vid->video_type); ?>')" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 6px; background: linear-gradient(135deg, #20b2aa, #17a2b8); border: none; color: #fff; font-weight: 700;">
                                                                Sửa
                                                            </button>
                                                        <?php endif; ?>
                                                        
                                                        <form action="<?php echo e(route('admin.video.destroy', $vid->id)); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa video review này không?')" style="display: inline;">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 6px; background: var(--primary-hover);">
                                                                Xóa
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
                            <div style="text-align: center; padding: 40px 0; color: var(--text-muted);">
                                <p style="font-size: 1.1rem; margin-bottom: 6px;">🎬 Chưa có video review nào.</p>
                                <p style="font-size: 0.85rem; color: var(--text-muted);">Hãy thêm link nhúng TikTok hoặc tải video lên để khách hàng dễ dàng tìm kiếm nhé!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>



        </div>
        
    </div>
</div>
<?php $__env->stopSection(); ?>

<!-- ==========================================================================
     MODAL SỬA VIDEO REVIEW DÀNH CHO SELLER & ADMIN
     ========================================================================== -->
<div id="editVideoModal" class="reels-overlay" style="display: none; z-index: 100005; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(9, 9, 11, 0.85); backdrop-filter: blur(8px);">
    <div class="glass-panel" style="width: 100%; max-width: 450px; padding: 28px; position: relative; border-radius: 16px; border: 1px solid var(--border-glow); box-shadow: 0 20px 50px rgba(0,0,0,0.5); background: #18181b;">
        <button type="button" style="position: absolute; top: 16px; right: 16px; background: transparent; border: none; color: var(--text-muted); font-size: 1.2rem; cursor: pointer; transition: color 0.2s;" onclick="closeEditVideoModal()" onmouseover="this.style.color='#ff3366'" onmouseout="this.style.color='var(--text-muted)'">✕</button>
        
        <h3 style="font-size: 1.2rem; color: var(--primary); margin-bottom: 20px; border-bottom: 1px solid var(--border-glow); padding-bottom: 10px; font-family: var(--font-heading); font-weight: 700; display: flex; align-items: center; gap: 8px;">
            <span>✏️</span> Cập Nhật Video Review
        </h3>
        
        <form id="editVideoForm" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 16px;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="review-form-group">
                <label class="review-form-label" style="font-size: 0.85rem; color: var(--text-main); font-weight: 600; display: block; margin-bottom: 6px;">Tiêu đề video *</label>
                <input type="text" id="editVideoTitle" name="title" required placeholder="Ví dụ: Ăn sập chợ Đông Anh..." class="form-input" style="font-size: 0.85rem; width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-glow); color: #fff; padding: 10px; border-radius: 8px;">
            </div>

            <div class="review-form-group">
                <label class="review-form-label" style="font-size: 0.85rem; color: var(--text-main); font-weight: 600; display: block; margin-bottom: 6px;">Chọn cơ sở kinh doanh *</label>
                <select id="editVideoEateryId" name="eatery_id" required class="form-input" style="font-size: 0.85rem; width: 100%; background: #1f1f23; border: 1px solid var(--border-glow); color: #fff; padding: 10px; border-radius: 8px;">
                    <?php $__currentLoopData = $eateries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($eat->id); ?>"><?php echo e($eat->name); ?> (<?php echo e($eat->commune->name); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Edit Upload Type Tabs -->
            <div style="background: rgba(255,255,255,0.05); padding: 4px; border-radius: 8px; display: flex; gap: 4px; border: 1px solid var(--border-glow); margin-bottom: 4px;">
                <button type="button" id="editTabBtn-embed" onclick="toggleEditUploadMode('embed')" class="btn-accent" style="flex: 1; font-size: 0.75rem; padding: 8px 0; border-radius: 6px; justify-content: center; background: var(--primary-grad); border: none; color: #fff; cursor: pointer; display: flex; align-items: center; font-weight: 600;">
                    🔗 Nhúng Link (0MB)
                </button>
                <button type="button" id="editTabBtn-file" onclick="toggleEditUploadMode('file')" class="btn-secondary" style="flex: 1; font-size: 0.75rem; padding: 8px 0; border-radius: 6px; justify-content: center; background: transparent; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; font-weight: 600;">
                    📤 Tải Video mới
                </button>
            </div>

            <!-- Edit Embed Section -->
            <div id="editContainer-embed" class="review-form-group" style="display: block;">
                <label class="review-form-label" style="font-size: 0.85rem; color: var(--text-main); font-weight: 600; display: block; margin-bottom: 6px;">Đường dẫn Video (TikTok / YouTube Shorts) *</label>
                <input type="url" id="editVideoUrlInput" name="video_url" placeholder="https://www.tiktok.com/..." class="form-input" style="font-size: 0.82rem; width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-glow); color: #fff; padding: 10px; border-radius: 8px;">
            </div>

            <!-- Edit File Upload Section -->
            <div id="editContainer-file" class="review-form-group" style="display: none;">
                <label class="review-form-label" style="font-size: 0.85rem; color: var(--text-main); font-weight: 600; display: block; margin-bottom: 6px;">Chọn File Video mới từ thiết bị</label>
                <input type="file" id="editVideoFileInput" name="video_file" accept="video/mp4,video/quicktime,video/x-matroska" class="form-input" style="font-size: 0.82rem; width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-glow); color: #fff; padding: 8px 12px; border-radius: 8px;">
                <span style="font-size: 0.72rem; color: var(--text-muted); display: block; margin-top: 6px; line-height: 1.3;">
                    ⚠️ Để trống nếu bạn muốn giữ nguyên file video đã đăng tải trước đó.
                </span>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px 0; font-size: 0.9rem; font-weight: 700; border-radius: 8px; margin-top: 10px; cursor: pointer;">
                💾 Lưu Thay Đổi
            </button>
        </form>
    </div>
</div>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Mặc định ở tab overview
        document.getElementById('adminTab-overview').style.display = 'block';
    });

    // 1. Sidebar Tab Switcher
    window.switchAdminTab = function(tabName) {
        document.querySelectorAll('.admin-menu-item').forEach(item => item.classList.remove('active'));
        document.querySelectorAll('.admin-tab-content').forEach(content => content.style.display = 'none');
        
        const btn = document.getElementById('adminTabBtn-' + tabName);
        if (btn) btn.classList.add('active');
        
        const tab = document.getElementById('adminTab-' + tabName);
        if (tab) tab.style.display = 'block';
    };

    // 2. Chuyển đổi chế độ đăng video (Nhúng link vs Tải file trực tiếp)
    window.toggleUploadMode = function(mode) {
        const embedBtn = document.getElementById('uploadTabBtn-embed');
        const fileBtn = document.getElementById('uploadTabBtn-file');
        const embedContainer = document.getElementById('uploadContainer-embed');
        const fileContainer = document.getElementById('uploadContainer-file');
        
        const urlInput = document.getElementById('videoUrlInput');
        const fileInput = document.getElementById('videoFileInput');

        if (mode === 'embed') {
            embedBtn.style.background = 'var(--primary-grad)';
            embedBtn.style.color = '#fff';
            
            fileBtn.style.background = 'transparent';
            fileBtn.style.color = 'var(--text-muted)';
            
            embedContainer.style.display = 'block';
            fileContainer.style.display = 'none';
            
            urlInput.setAttribute('required', 'required');
            fileInput.removeAttribute('required');
        } else {
            fileBtn.style.background = 'var(--primary-grad)';
            fileBtn.style.color = '#fff';
            
            embedBtn.style.background = 'transparent';
            embedBtn.style.color = 'var(--text-muted)';
            
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
        document.getElementById('editVideoEateryId').value = eateryId;
        
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

    window.toggleEditUploadMode = function(mode) {
        const embedBtn = document.getElementById('editTabBtn-embed');
        const fileBtn = document.getElementById('editTabBtn-file');
        const embedContainer = document.getElementById('editContainer-embed');
        const fileContainer = document.getElementById('editContainer-file');
        
        const urlInput = document.getElementById('editVideoUrlInput');
        const fileInput = document.getElementById('editVideoFileInput');

        if (mode === 'embed') {
            embedBtn.style.background = 'var(--primary-grad)';
            embedBtn.style.color = '#fff';
            
            fileBtn.style.background = 'transparent';
            fileBtn.style.color = 'var(--text-muted)';
            
            embedContainer.style.display = 'block';
            fileContainer.style.display = 'none';
            
            urlInput.setAttribute('required', 'required');
            fileInput.removeAttribute('required');
        } else {
            fileBtn.style.background = 'var(--primary-grad)';
            fileBtn.style.color = '#fff';
            
            embedBtn.style.background = 'transparent';
            embedBtn.style.color = 'var(--text-muted)';
            
            fileContainer.style.display = 'block';
            embedContainer.style.display = 'none';
            
            fileInput.removeAttribute('required');
            urlInput.removeAttribute('required');
        }
    };
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FOODDA\FOOD_MAP\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>