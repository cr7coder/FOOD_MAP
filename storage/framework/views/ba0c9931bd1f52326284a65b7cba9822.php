<div class="admin-table-container">
    <table class="admin-data-table">
        <thead>
            <tr>
                <th style="width: 70px; text-align: center;">Avatar</th>
                <th>Họ và Tên</th>
                <th>Email</th>
                <th>Số Điện Thoại</th>
                <th>Vai Trò</th>
                <th style="text-align: center;">Trạng Thái</th>
                <th style="text-align: center; width: 160px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if($users->count() > 0): ?>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="text-align: center;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; border: 1.5px solid var(--admin-border);">
                                <?php echo e($user->avatar ?: '🧑'); ?>

                            </div>
                        </td>
                        <td>
                            <strong style="color: var(--admin-text-main); font-size: 0.9rem; display: block;"><?php echo e($user->name); ?></strong>
                            <span style="font-size: 0.72rem; color: var(--admin-text-muted);">ID: #<?php echo e($user->id); ?></span>
                        </td>
                        <td>
                            <span style="font-size: 0.84rem; color: var(--admin-text-main);"><?php echo e($user->email); ?></span>
                        </td>
                        <td>
                            <span style="font-size: 0.84rem; color: var(--admin-text-main);"><?php echo e($user->phone ?: 'Chưa cập nhật'); ?></span>
                        </td>
                        <td>
                            <?php if($user->role === 'admin'): ?>
                                <span class="admin-badge admin-badge-primary" style="font-size: 0.72rem; font-weight: 700; background-color: #f3e8ff; color: #7e22ce; border-color: rgba(126, 34, 206, 0.15);">Admin</span>
                            <?php elseif($user->role === 'seller'): ?>
                                <span class="admin-badge admin-badge-primary" style="font-size: 0.72rem; font-weight: 700; background-color: #ecfdf5; color: #047857; border-color: rgba(4, 120, 87, 0.15);">Seller</span>
                            <?php else: ?>
                                <span class="admin-badge admin-badge-primary" style="font-size: 0.72rem; font-weight: 700; background-color: #eff6ff; color: #1d4ed8; border-color: rgba(29, 78, 216, 0.15);">Customer</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if($user->status === 'active'): ?>
                                <span class="admin-badge admin-badge-success" style="font-size: 0.72rem; font-weight: 700;">Hoạt động</span>
                            <?php else: ?>
                                <span class="admin-badge" style="font-size: 0.72rem; font-weight: 700; background-color: #f1f5f9; color: #64748b; border: 1px solid rgba(100, 116, 139, 0.15);">Vô hiệu hóa</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                <!-- View details button -->
                                <a href="/admin/users/<?php echo e($user->id); ?>" class="btn-admin btn-admin-accent" style="padding: 6px 10px; font-size: 0.72rem; font-weight: 700; border-radius: 6px;" title="Xem chi tiết">
                                    👁️
                                </a>

                                <!-- Edit button -->
                                <a href="/admin/users/<?php echo e($user->id); ?>/edit" class="btn-admin btn-admin-primary" style="padding: 6px 10px; font-size: 0.72rem; font-weight: 700; border-radius: 6px;" title="Chỉnh sửa">
                                    ✏️
                                </a>

                                <!-- Delete button -->
                                <?php if($user->id !== session('user_id')): ?>
                                    <form action="/admin/users/<?php echo e($user->id); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn người dùng này khỏi hệ thống?')" style="display: inline; margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-admin btn-admin-danger" style="padding: 6px 10px; font-size: 0.72rem; font-weight: 700; border-radius: 6px;" title="Xóa tài khoản">
                                            🗑️
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px 0; color: var(--admin-text-muted); font-style: italic;">
                        🔍 Không tìm thấy tài khoản người dùng nào khớp với bộ lọc.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Premium Pagination links -->
<?php if($users->hasPages()): ?>
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        <div class="pagination">
            <?php echo e($users->links('pagination::bootstrap-4')); ?>

        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\FOODDA\FOOD_MAP\resources\views/admin/users/partial-table.blade.php ENDPATH**/ ?>