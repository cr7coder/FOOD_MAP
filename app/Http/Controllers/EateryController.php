<?php

namespace App\Http\Controllers;

use App\Models\Eatery;
use App\Models\Review;
use Illuminate\Http\Request;

class EateryController extends Controller
{
    public function show($slug)
    {
        $eatery = Eatery::with([
            'category', 
            'commune', 
            'dishes', 
            'foodSafetyCertificate', 
            'foodSupplyContracts', 
            'purchaseInvoices', 
            'dailyFoodLogs',
            'reviews' => function($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])->where('slug', $slug)->firstOrFail();
        
        // Tự động phân tích danh mục ẩm thực để chọn Schema Type thích hợp của Google
        $schemaType = 'LocalBusiness';
        $categorySlug = $eatery->category->slug;
        if ($categorySlug === 'bun-pho' || $categorySlug === 'lau-nuong' || $categorySlug === 'dac-san-dia-phuong') {
            $schemaType = 'Restaurant';
        } elseif ($categorySlug === 'khach-san-nha-nghi') {
            $schemaType = 'Hotel';
        } elseif ($categorySlug === 'quan-cafe') {
            $schemaType = 'CafeOrCoffeeShop';
        }
        
        $currentUrl = request()->url();
        
        // Khởi tạo mảng dữ liệu có cấu trúc Schema.org chuẩn Google
        $schemaData = [
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            'name' => $eatery->name,
            'image' => $eatery->image_path ?: 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=800&q=80',
            'telephone' => $eatery->phone ?: 'Chưa cập nhật',
            'priceRange' => $eatery->price_range ?: 'Đang cập nhật',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $eatery->address,
                'addressLocality' => $eatery->commune->name,
                'addressRegion' => 'Đông Anh, Hà Nội',
                'addressCountry' => 'VN'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $eatery->latitude,
                'longitude' => $eatery->longitude
            ],
            'url' => $currentUrl
        ];
        
        if ($schemaType === 'Restaurant' || $schemaType === 'CafeOrCoffeeShop') {
            $schemaData['servesCuisine'] = $eatery->category->name;
        }
        
        // Thêm đánh giá trung bình vào Schema nếu đã có bình luận
        if ($eatery->reviews->count() > 0) {
            $schemaData['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $eatery->average_rating,
                'reviewCount' => $eatery->reviews->count()
            ];
            
            $schemaData['review'] = [];
            foreach ($eatery->reviews->take(3) as $rev) {
                $schemaData['review'][] = [
                    '@type' => 'Review',
                    'author' => [
                        '@type' => 'Person',
                        'name' => $rev->user_name
                    ],
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => $rev->rating
                    ],
                    'reviewBody' => $rev->comment
                ];
            }
        }
        
        // Mã hóa Schema dữ liệu có cấu trúc sang định dạng thẻ script JSON-LD
        $jsonLd = '<script type="application/ld+json">' . PHP_EOL . 
                  json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL . 
                  '</script>';
        
        return view('detail', compact('eatery', 'jsonLd'));
    }

    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'user_name' => 'required|string|max:50',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480'
        ]);
        
        $eatery = Eatery::findOrFail($id);
        
        $review = Review::create([
            'eatery_id' => $eatery->id,
            'user_name' => $request->user_name,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('reviews', 'public');
                $type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
                $review->media()->create([
                    'file_path' => '/storage/' . $path,
                    'file_type' => $type
                ]);
            }
        }
        
        // Tính toán lại điểm số đánh giá trung bình thời gian thực và cập nhật lại bảng eateries
        $avgRating = $eatery->reviews()->avg('rating');
        if ($avgRating !== null) {
            $eatery->update([
                'rating' => round($avgRating, 2)
            ]);
        }
        
        return redirect()->back()->with('success', 'Cảm ơn bạn đã gửi đánh giá! Nhận xét của bạn giúp ích cho cộng đồng du lịch Đông Anh.');
    }
}
