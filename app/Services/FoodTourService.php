<?php

namespace App\Services;

use App\Models\Eatery;
use App\Models\FoodTour;
use App\Models\FoodTourStop;
use App\Models\FoodTourDiary;
use App\Models\Review;
use App\DTOs\GenerateAITourDTO;
use App\DTOs\StoreDiaryDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FoodTourService
{
    /**
     * Sinh hành trình Food Tour bằng AI (Gemini API)
     */
    public function generateAITour(GenerateAITourDTO $dto): FoodTour
    {
        $budgetLimit = $dto->budgetLimit;
        $mood = $dto->mood;
        
        $moodText = $mood;
        $extraConstraint = "ĐẶC BIỆT ƯU TIÊN các sản phẩm/cơ sở OCOP Tinh hoa bản địa";
        if ($mood === 'specialty') {
            $moodText = 'Khám phá đặc sản, Tinh hoa bản địa';
            $extraConstraint = "BẮT BUỘC ÍT NHẤT 2 TRONG 3 ĐỊA ĐIỂM PHẢI CÓ CATEGORY LÀ 'Tinh hoa bản địa' HOẶC LÀ CƠ SỞ OCOP";
        }
        
        $eateries = Eatery::with('category')->active()->get()->map(function($e) {
            return [
                'id' => $e->id,
                'name' => $e->name,
                'category' => $e->category->name ?? 'Khác',
                'price_range' => $e->price_range,
                'description' => $e->description
            ];
        });

        $prompt = "Tôi đang ở Đông Anh, Hà Nội. Tôi có ngân sách khoảng {$budgetLimit} VND. Tâm trạng của tôi là '{$moodText}'. 
Hãy đóng vai một chuyên gia bản địa. Chọn chính xác 3 địa điểm phù hợp nhất từ danh sách sau để tạo thành 1 lộ trình Food Tour / Trải nghiệm liên hoàn mang đậm bản sắc văn hóa. YÊU CẦU QUAN TRỌNG: {$extraConstraint}.
Danh sách địa điểm:
" . json_encode($eateries, JSON_UNESCAPED_UNICODE) . "
YÊU CẦU TRẢ VỀ CHỈ LÀ CHUỖI JSON ĐÚNG ĐỊNH DẠNG SAU, KHÔNG CHỨA BẤT KỲ TEXT NÀO KHÁC BÊN NGOÀI (KHÔNG CÓ DẤU ```json):
{
    \"tour_name\": \"Tên tour sáng tạo (VD: Hành trình Khám phá Chợ & Ẩm thực Đông Anh)\",
    \"description\": \"Mô tả ngắn gọn 2 câu về tour.\",
    \"story\": \"Câu chuyện 3 câu dẫn dắt vì sao lại chọn 3 địa điểm này.\",
    \"difficulty\": \"✨ Lộ trình AI\",
    \"stops\": [
        {
            \"eatery_id\": id_1,
            \"recommendation\": \"Gợi ý cụ thể nên ăn món gì hoặc làm hoạt động gì tại đây (VD: Thử bát phở tái nạm hoặc Đi dạo mua sắm đặc sản nông sản sạch).\"
        },
        {
            \"eatery_id\": id_2,
            \"recommendation\": \"...\"
        },
        {
            \"eatery_id\": id_3,
            \"recommendation\": \"...\"
        }
    ]
}";

        $apiKey = config('services.gemini.key');
        
        if (!$apiKey) {
            throw new \Exception('Thiếu API Key Gemini.');
        }

        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ]);
        
        $result = $response->json();
        $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        
        if ($textResponse === null) {
            Log::error('Gemini API Full Response: ' . json_encode($result));
            $errMsg = $result['error']['message'] ?? 'Phản hồi rỗng từ Gemini';
            throw new \Exception('API Error: ' . $errMsg);
        }
        
        Log::info('Gemini Raw Response: ' . $textResponse);
        
        // Tách phần JSON ra khỏi phản hồi
        if (preg_match('/\{.*\}/s', $textResponse, $matches)) {
            $jsonString = $matches[0];
        } else {
            $jsonString = $textResponse;
        }
        
        $aiData = json_decode(trim($jsonString), true);
        
        if (isset($aiData['eatery_ids']) && !isset($aiData['stops'])) {
            $aiData['stops'] = array_map(function($id) {
                return ['eatery_id' => $id, 'recommendation' => 'Trải nghiệm ẩm thực địa phương hấp dẫn tại đây.'];
            }, $aiData['eatery_ids']);
        }

        if (!$aiData || !isset($aiData['stops']) || count($aiData['stops']) < 1) {
            Log::error('JSON Decode Error: ' . json_last_error_msg());
            throw new \Exception('Invalid JSON format returned from AI');
        }
        
        $slug = Str::slug($aiData['tour_name']) . '-' . substr(md5(uniqid()), 0, 5);
        
        $tour = FoodTour::create([
            'name' => $aiData['tour_name'],
            'slug' => $slug,
            'description' => $aiData['description'],
            'duration' => '2.5 giờ',
            'distance' => '5.0 km',
            'budget' => number_format($budgetLimit, 0, ',', '.') . 'đ',
            'difficulty' => $aiData['difficulty'],
            'best_time' => '17:00 - 21:00',
            'popularity' => 'Mới tạo',
            'mood' => $mood,
            'thumbnail' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
            'story' => $aiData['story'],
            'status' => 'draft',
            'is_ai_generated' => true,
        ]);
        
        foreach ($aiData['stops'] as $index => $stop) {
            FoodTourStop::create([
                'food_tour_id' => $tour->id,
                'eatery_id' => $stop['eatery_id'],
                'stop_order' => $index + 1,
                'stop_story' => $stop['recommendation'] ?? ("Điểm đến thứ " . ($index + 1) . " trong hành trình " . $aiData['tour_name'] . "."),
                'estimated_time' => '45 phút'
            ]);
        }
        
        return $tour;
    }

    /**
     * Lưu nhật ký hành trình Food Tour
     */
    public function storeDiary(int $tourId, int $userId, StoreDiaryDTO $dto): FoodTourDiary
    {
        $imagePath = null;
        if ($dto->image) {
            $base64 = $dto->image;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $type = strtolower($type[1]);

                if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    $base64 = base64_decode($base64);

                    if ($base64 !== false) {
                        $fileName = 'selfie_' . time() . '_' . uniqid() . '.' . $type;
                        $dir = public_path('uploads/diaries');
                        if (!file_exists($dir)) {
                            mkdir($dir, 0755, true);
                        }
                        file_put_contents($dir . '/' . $fileName, $base64);
                        $imagePath = '/uploads/diaries/' . $fileName;
                    }
                }
            }
        }

        $stopReviews = $dto->stopReviews;
        $user = \App\Models\User::find($userId);
        $userName = $user ? $user->name : 'Thực khách Food Tour';

        foreach ($stopReviews as $index => &$review) {
            if (!empty($review['image']) && preg_match('/^data:image\/(\w+);base64,/', $review['image'], $type)) {
                $imgBase64 = substr($review['image'], strpos($review['image'], ',') + 1);
                $type = strtolower($type[1]);

                if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    $imgBase64 = base64_decode($imgBase64);
                    if ($imgBase64 !== false) {
                        $fileName = 'stop_' . $index . '_' . time() . '_' . uniqid() . '.' . $type;
                        $dir = public_path('uploads/diaries');
                        if (!file_exists($dir)) {
                            mkdir($dir, 0755, true);
                        }
                        file_put_contents($dir . '/' . $fileName, $imgBase64);
                        $review['image_path'] = '/uploads/diaries/' . $fileName;
                        unset($review['image']);
                    }
                }
            }

            if (!empty($review['eatery_id'])) {
                $newReview = Review::create([
                    'eatery_id' => $review['eatery_id'],
                    'user_name' => $userName,
                    'rating' => $review['rating'] ?? null,
                    'comment' => $review['comment'] ?? ''
                ]);

                if (!empty($review['image_path'])) {
                    $newReview->media()->create([
                        'file_path' => $review['image_path'],
                        'file_type' => 'image'
                    ]);
                }

                $eatery = Eatery::find($review['eatery_id']);
                if ($eatery) {
                    $avgRating = Review::where('eatery_id', $review['eatery_id'])->avg('rating');
                    $eatery->update(['rating' => $avgRating ?: 5.00]);
                }
            }
        }

        $diary = FoodTourDiary::create([
            'food_tour_id' => $tourId,
            'user_id' => $userId,
            'rating' => $dto->rating,
            'comment' => $dto->comment,
            'image_path' => $imagePath,
            'completed_stops' => $dto->completedStops,
            'stop_reviews' => $stopReviews,
        ]);

        // Nếu đây là AI Tour đang ở trạng thái draft -> Nâng cấp lên 'saved'
        $tour = FoodTour::find($tourId);
        if ($tour && $tour->is_ai_generated && $tour->status === 'draft') {
            $updateData = ['status' => 'saved'];
            
            if ($dto->shareToCommunity) {
                $updateData['shared_at'] = now();
                $updateData['expires_at'] = now()->addHours(72);
            }
            
            $tour->update($updateData);
        }

        return $diary;
    }
}
