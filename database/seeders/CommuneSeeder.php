<?php

namespace Database\Seeders;

use App\Models\Commune;
use Illuminate\Database\Seeder;

/**
 * CommuneSeeder — Tạo dữ liệu mẫu bảng communes (xã/thị trấn Đông Anh)
 *
 * Chạy: php artisan db:seed --class=CommuneSeeder
 * Lưu ý: Seeder này dùng firstOrCreate để không tạo trùng nếu commune đã tồn tại.
 */
class CommuneSeeder extends Seeder
{
    public function run(): void
    {
        $communes = [
            ['name' => 'Thị trấn Đông Anh', 'slug' => 'thi-tran-dong-anh'],
            ['name' => 'Cổ Loa',             'slug' => 'co-loa'],
            ['name' => 'Liên Hà',            'slug' => 'lien-ha'],
            ['name' => 'Vĩnh Ngọc',          'slug' => 'vinh-ngoc'],
            ['name' => 'Vân Nội',            'slug' => 'van-noi'],
            ['name' => 'Đông Hội',           'slug' => 'dong-hoi'],
            ['name' => 'Kim Nỗ',             'slug' => 'kim-no'],
            ['name' => 'Hải Bối',            'slug' => 'hai-boi'],
            ['name' => 'Nam Hồng',           'slug' => 'nam-hong'],
            ['name' => 'Tiên Dương',         'slug' => 'tien-duong'],
        ];

        foreach ($communes as $com) {
            Commune::firstOrCreate(
                ['slug' => $com['slug']],
                $com
            );
        }
    }
}
