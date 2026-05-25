<?php $__env->startSection('title', 'Đăng nhập - Bản đồ số Ẩm thực Đông Anh'); ?>

<?php $__env->startSection('content'); ?>
<div class="container" style="padding: 80px 0; display: flex; justify-content: center; align-items: center; min-height: calc(100vh - var(--header-height) - 200px);">
    <div class="glass-panel" style="width: 100%; max-width: 440px; padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.5);">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <span style="font-size: 3rem; display: block; margin-bottom: 10px;">🍜</span>
            <h2 style="font-size: 1.8rem; font-family: var(--font-heading); background: var(--primary-grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; padding: 6px 0; line-height: 1.3;">
                Đăng nhập hệ thống
            </h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">
                Quản lý quán ăn và cập nhật bản đồ ẩm thực Đông Anh
            </p>
        </div>
        
        <!-- Hiển thị các lỗi validation -->
        <?php if($errors->any()): ?>
            <div class="glass-panel" style="background: rgba(240, 78, 35, 0.1); border-color: var(--primary-hover); padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; color: var(--primary); font-size: 0.85rem;">
                <ul style="list-style: none;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>⚠️ <?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="/auth/login" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="review-form-group">
                <label class="review-form-label" style="font-size: 0.85rem; font-weight: 600;">Địa chỉ Email</label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-input" required placeholder="name@example.com" style="padding: 12px 16px;">
            </div>
            
            <div class="review-form-group" style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label class="review-form-label" style="margin-bottom: 0; font-size: 0.85rem; font-weight: 600;">Mật khẩu</label>
                    <a href="#" style="font-size: 0.8rem; color: var(--primary);">Quên mật khẩu?</a>
                </div>
                <input type="password" name="password" class="form-input" required placeholder="••••••••" style="padding: 12px 16px;">
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px 0; font-size: 1rem; margin-bottom: 20px;">
                Đăng nhập ngay
            </button>
        </form>
        
        <div style="text-align: center; border-top: 1px solid var(--border-glow); padding-top: 20px; font-size: 0.85rem; color: var(--text-muted);">
            Chưa có tài khoản chủ quán? 
            <a href="/auth/register" style="color: var(--primary); font-weight: 600;">Đăng ký tại đây</a>
        </div>
        
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\FOODDA\FOOD_MAP\resources\views/auth/login.blade.php ENDPATH**/ ?>