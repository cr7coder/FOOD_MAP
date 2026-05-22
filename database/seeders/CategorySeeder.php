<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * CategorySeeder — Tạo dữ liệu mẫu bảng categories
 *
 * Chạy: php artisan db:seed --class=CategorySeeder
 * Lưu ý: Seeder này dùng firstOrCreate để không tạo trùng nếu category đã tồn tại.
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Bún & Phở',
                'slug'        => 'bun-pho',
                'icon'        => '🍜',
                'description' => 'Các món bún phở gia truyền, hương vị thơm ngon hấp dẫn.',
            ],
            [
                'name'        => 'Lẩu & Nướng',
                'slug'        => 'lau-nuong',
                'icon'        => '🔥',
                'description' => 'Nhà hàng lẩu nướng, không gian sum họp gia đình, bạn bè lý tưởng.',
            ],
            [
                'name'        => 'Quán Cafe',
                'slug'        => 'quan-cafe',
                'icon'        => '☕',
                'description' => 'Không gian thưởng thức cà phê, trà bánh thư giãn và ngắm cảnh.',
            ],
            [
                'name'        => 'Khách sạn & Nhà nghỉ',
                'slug'        => 'khach-san-nha-nghi',
                'icon'        => '🏨',
                'description' => 'Điểm lưu trú tiện nghi, sạch sẽ phục vụ khách du lịch và công tác.',
            ],
            [
                'name'        => 'Tinh hoa bản địa',
                'slug'        => 'dac-san-dia-phuong',
                'icon'        => '🌾',
                'description' => 'Các món đặc sản mang đậm bản sắc và lịch sử truyền thống Đông Anh.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
