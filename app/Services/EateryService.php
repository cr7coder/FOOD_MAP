<?php

namespace App\Services;

use App\Models\Eatery;
use App\Models\Review;
use App\DTOs\SearchDTO;
use App\DTOs\StoreReviewDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class EateryService
{
    /**
     * Thực hiện tìm kiếm địa điểm ẩm thực nâng cao
     */
    public function search(SearchDTO $dto): Collection
    {
        $query = Eatery::with(['category', 'commune', 'reviewVideos' => function($q) {
            $q->where('status', 'approved');
        }])->active();
        
        if ($dto->keyword) {
            $keyword = $dto->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%")
                  ->orWhereHas('dishes', function($dQuery) use ($keyword) {
                      $dQuery->where('name', 'like', "%{$keyword}%");
                  });
            });
        }
        
        if ($dto->categoryId) {
            $query->where('category_id', $dto->categoryId);
        }
        
        if ($dto->communeId) {
            $query->where('commune_id', $dto->communeId);
        }
        
        return $query->get();
    }

    /**
     * Lấy gợi ý tự động (Autocomplete)
     */
    public function getSuggestions(string $keyword): Collection
    {
        return Eatery::active()
            ->where('name', 'like', "%{$keyword}%")
            ->limit(6)
            ->get(['id', 'name', 'slug', 'address']);
    }

    /**
     * Lấy thông tin chi tiết và sinh dữ liệu có cấu trúc Schema.org chuẩn Google
     */
    public function getDetailAndSchema(string $slug): array
    {
        $eatery = Eatery::with(['category', 'commune', 'dishes', 'reviews' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->where('slug', $slug)->firstOrFail();
        
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
        
        $jsonLd = '<script type="application/ld+json">' . PHP_EOL . 
                  json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL . 
                  '</script>';
                  
        return [
            'eatery' => $eatery,
            'jsonLd' => $jsonLd
        ];
    }

    /**
     * Đăng bình luận & đánh giá mới
     */
    public function storeReview(int $eateryId, StoreReviewDTO $dto): Review
    {
        $eatery = Eatery::findOrFail($eateryId);
        
        $review = Review::create([
            'eatery_id' => $eatery->id,
            'user_name' => $dto->userName,
            'rating' => $dto->rating,
            'comment' => $dto->comment
        ]);

        foreach ($dto->mediaFiles as $file) {
            $path = $file->store('reviews', 'public');
            $type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
            $review->media()->create([
                'file_path' => '/storage/' . $path,
                'file_type' => $type
            ]);
        }
        
        // Cập nhật rating trung bình
        $avgRating = $eatery->reviews()->avg('rating');
        if ($avgRating !== null) {
            $eatery->update([
                'rating' => round($avgRating, 2)
            ]);
        }
        
        return $review;
    }
}
