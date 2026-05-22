<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder — Tạo dữ liệu mẫu bảng users
 *
 * Chạy: php artisan db:seed --class=UserSeeder
 * Lưu ý: Seeder này dùng firstOrCreate để không tạo trùng nếu user đã tồn tại.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Quản trị viên',
                'email'    => 'admin@foodmap.vn',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'avatar'   => null,
            ],
            [
                'name'     => 'Chủ Quán Mạch Tràng',
                'email'    => 'seller@foodmap.vn',
                'password' => Hash::make('seller123'),
                'role'     => 'seller',
                'avatar'   => null,
            ],
            [
                'name'     => 'Thực Thần Đông Anh',
                'email'    => 'user@foodmap.vn',
                'password' => Hash::make('user123'),
                'role'     => 'user',
                'avatar'   => null,
            ],
            [
                'name'     => 'Thành viên Đông Anh',
                'email'    => 'member@foodmap.vn',
                'password' => Hash::make('member123'),
                'role'     => 'user',
                'avatar'   => null,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
