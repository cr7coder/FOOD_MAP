<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Eatery;
use App\Models\Category;
use App\Models\Commune;
use Illuminate\Support\Str;

class OcopHeritageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = Category::where('slug', 'dac-san-dia-phuong')->first();
        if (!$category) {
            return;
        }

        $defaultCommune = Commune::first();

        $ocopData = [
            [
                'name' => 'HTX nông nghiệp dược liệu công nghệ cao KOVI',
                'address' => 'Thôn Lộc Hà, xã Đông Anh, thành phố Hà Nội',
                'description' => 'Sản phẩm OCOP 4 sao: Đông trùng hạ thảo tươi, Đông trùng hạ thảo khô, Đông trùng hạ thảo ký chủ nhộng tằm (Chứng nhận năm 2022).',
                'latitude' => 21.0945,
                'longitude' => 105.8672,
                'commune_name' => 'Mai Lâm',
                'image_path' => 'https://dongtrunghathaokovi.com/wp-content/uploads/2021/04/dong-trung-ha-thao-kovi.jpg'
            ],
            [
                'name' => 'Công ty TNHH Hoàng Chiến Thắng',
                'address' => 'Thôn Đông Ngàn, xã Đông Anh, thành phố Hà Nội',
                'description' => 'Sản phẩm OCOP đa dạng: Bánh gạo lứt, Bánh vừng vòng, Bánh sampa, Bánh nhện vừng, Bánh Vòng Dừa, Bánh vừng Cookies, Bánh gạo thơm (Đạt chứng nhận liên tục 2022-2025).',
                'latitude' => 21.1098,
                'longitude' => 105.8612,
                'commune_name' => 'Đông Hội',
                'image_path' => 'https://bizweb.dktcdn.net/100/356/102/files/banh-dong-ngan-dong-anh.jpg?v=1626071477148'
            ],
            [
                'name' => 'Tương Việt Hùng - HTX dịch vụ nông nghiệp thôn Đoài',
                'address' => 'Thôn Đoài, xã Đông Anh, thành phố Hà Nội',
                'description' => 'Sản phẩm OCOP đặc trưng truyền thống: Tương Việt Hùng (Chứng nhận năm 2022). Tương nếp thơm ngon, đậm đà bản sắc cố đô.',
                'latitude' => 21.1444,
                'longitude' => 105.8752,
                'commune_name' => 'Việt Hùng',
                'image_path' => 'https://file1.dangcongsan.vn/data/0/images/2021/11/04/phuongthuy/tuong-viet-hung-1.jpg'
            ],
            [
                'name' => 'Bánh ngọt Thuý Quyên',
                'address' => 'Thôn Đông Ngàn, xã Đông Anh, thành phố Hà Nội',
                'description' => 'Sản phẩm OCOP: Bánh xốp vừng, Bánh sampa, Bánh trứng nhện (Chứng nhận năm 2023). Đậm đà hương vị bánh kẹo Đông Ngàn.',
                'latitude' => 21.1090,
                'longitude' => 105.8610,
                'commune_name' => 'Đông Hội',
                'image_path' => 'https://hanoimoi.com.vn/Uploads/Images/tuandiep/2020/09/20/banh.jpg'
            ],
            [
                'name' => 'Rượu Long Tửu (HKD Thạo Loan)',
                'address' => 'Thôn Xuân Canh, xã Đông Anh, thành phố Hà Nội',
                'description' => 'Sản phẩm OCOP lừng danh: Rượu gạo nếp Long Tửu, Rượu dâu, Rượu mơ, Rượu Bạch cúc (Chứng nhận liên tục 2023-2025). Tinh túy men lá.',
                'latitude' => 21.1030,
                'longitude' => 105.8450,
                'commune_name' => 'Xuân Canh',
                'image_path' => 'https://file3.qdnd.vn/data/images/0/2021/12/30/vuhuyen/longtuu.jpg'
            ],
            [
                'name' => 'HTX Cổ Loa',
                'address' => 'Trung tâm Cổ Loa, xã Đông Anh, thành phố Hà Nội',
                'description' => 'Sản phẩm OCOP nông sản sạch: Hành lá, khoai tây, bí đỏ, lạc nhân (Chứng nhận 2024-2025).',
                'latitude' => 21.1167,
                'longitude' => 105.8667,
                'commune_name' => 'Cổ Loa',
                'image_path' => 'https://media.thanglong.chinhphu.vn/Images/2021/04/23/nongsan.jpg'
            ],
            [
                'name' => 'Gạo nếp cái hoa vàng Dục Tú',
                'address' => 'Xã Đông Anh, thành phố Hà Nội',
                'description' => 'Sản phẩm OCOP: Gạo nếp cái hoa vàng (HTX dịch vụ nông nghiệp Dục Tú - Chứng nhận 2024). Hạt tròn, dẻo thơm.',
                'latitude' => 21.1345,
                'longitude' => 105.9082,
                'commune_name' => 'Dục Tú',
                'image_path' => 'https://media.songkhoe.vn/2019/11/04/nep-cai-hoa-vang-1.jpg'
            ],
            [
                'name' => 'Cơ sở sản xuất thực phẩm Liêm Hiệp',
                'address' => 'Thôn Thượng, xã Đông Anh, thành phố Hà Nội',
                'description' => 'Sản phẩm OCOP: Giò lụa, Chả lụa (Chứng nhận 2025). Thơm ngon, an toàn vệ sinh thực phẩm.',
                'latitude' => 21.1440,
                'longitude' => 105.8475,
                'commune_name' => 'Uy Nỗ',
                'image_path' => 'https://cdn.tgdd.vn/Files/2019/08/16/1188358/cach-lam-gio-lua-thom-ngon-tai-nha-202112311456100155.jpg'
            ]
        ];

        foreach ($ocopData as $data) {
            $commune = Commune::where('name', 'like', '%'.$data['commune_name'].'%')->first() ?? $defaultCommune;

            Eatery::updateOrCreate(
                ['name' => $data['name']],
                [
                    'category_id' => $category->id,
                    'commune_id' => $commune->id,
                    'slug' => Str::slug($data['name']),
                    'address' => $data['address'],
                    'latitude' => $data['latitude'] + (rand(-10,10)/10000), 
                    'longitude' => $data['longitude'] + (rand(-10,10)/10000),
                    'rating' => 4.9,
                    'price_range' => '50.000đ - 500.000đ',
                    'opening_hours' => '07:00 - 18:00',
                    'description' => $data['description'],
                    'image_path' => $data['image_path'],
                    'status' => 'active'
                ]
            );
        }
    }
}
