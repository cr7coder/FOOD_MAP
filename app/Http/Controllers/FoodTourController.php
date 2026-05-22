<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Commune;
use App\Models\Eatery;
use App\Models\FoodTour;
use App\Models\FoodTourStop;
use Illuminate\Http\Request;

class FoodTourController extends Controller
{
    /**
     * Display a listing of the pre-designed food tours.
     */
    public function index(Request $request)
    {
        $mood = $request->query('mood');
        
        // Chỉ hiển thị các Tour chính thức do Admin tạo, loại trừ các tour trải nghiệm nấu ăn (cooking)
        $query = FoodTour::public()->where('mood', '!=', 'cooking')->with(['stops.eatery', 'diaries.user'])->withCount('diaries');
        
        if ($mood) {
            $query->where('mood', $mood);
        }
        
        $tours = $query->get();

        // Lấy các AI Tour đang được chia sẻ công khai bởi cộng đồng (chưa hết 72h)
        $communityTours = FoodTour::community()
            ->with(['stops.eatery', 'diaries.user'])
            ->withCount('diaries')
            ->orderBy('shared_at', 'desc')
            ->get();
        
        return view('food-tours.index', compact('tours', 'mood', 'communityTours'));
    }

    /**
     * Display a listing of cooking and experience tours.
     */
    public function cookingIndex(Request $request)
    {
        $tours = FoodTour::public()
            ->where('mood', 'cooking')
            ->with(['stops.eatery', 'diaries.user'])
            ->withCount('diaries')
            ->get();
            
        return view('food-tours.cooking', compact('tours'));
    }

    /**
     * Display the specified food tour details.
     */
    public function show(Request $request, $slug)
    {
        $tour = FoodTour::where('slug', $slug)
            ->with(['stops' => function($q) {
                $q->orderBy('stop_order');
            }, 'stops.eatery.category', 'stops.eatery.commune'])
            ->firstOrFail();

        $diaries = \App\Models\FoodTourDiary::where('food_tour_id', $tour->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('food-tours.show', compact('tour', 'diaries'));
    }

    /**
     * Advanced Simulated AI Tour Generator.
     * Takes budget, mood, and optional starting region, filters eateries,
     * arranges them geographically using a Nearest Neighbor traveling salesman heuristic,
     * and returns a custom generated cinematic food tour.
     */
    public function generateAI(Request $request)
    {
        $budgetLimit = (int) $request->input('budget', 300000);
        $mood = $request->input('mood', 'chill');
        
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
            return response()->json(['success' => false, 'message' => 'Thiếu API Key Gemini.']);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);
            
            $result = $response->json();
            $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if ($textResponse === null) {
                \Illuminate\Support\Facades\Log::error('Gemini API Full Response: ' . json_encode($result));
                $errMsg = $result['error']['message'] ?? 'Phản hồi rỗng từ Gemini';
                throw new \Exception('API Error: ' . $errMsg);
            }
            
            \Illuminate\Support\Facades\Log::info('Gemini Raw Response: ' . $textResponse);
            
            // Tách phần JSON ra khỏi phản hồi (nếu AI có chèn thêm chữ ở đầu hoặc cuối)
            if (preg_match('/\{.*\}/s', $textResponse, $matches)) {
                $jsonString = $matches[0];
            } else {
                $jsonString = $textResponse;
            }
            
            $aiData = json_decode(trim($jsonString), true);
            
            // Fallback for older prompt format just in case AI hallucinates
            if (isset($aiData['eatery_ids']) && !isset($aiData['stops'])) {
                $aiData['stops'] = array_map(function($id) {
                    return ['eatery_id' => $id, 'recommendation' => 'Trải nghiệm ẩm thực địa phương hấp dẫn tại đây.'];
                }, $aiData['eatery_ids']);
            }

            if (!$aiData || !isset($aiData['stops']) || count($aiData['stops']) < 1) {
                \Illuminate\Support\Facades\Log::error('JSON Decode Error: ' . json_last_error_msg());
                throw new \Exception('Invalid JSON format returned from AI');
            }
            
            $slug = \Illuminate\Support\Str::slug($aiData['tour_name']) . '-' . substr(md5(uniqid()), 0, 5);
            
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
                'status' => 'draft',           // Trạng thái ban đầu: bản nháp
                'is_ai_generated' => true,      // Đánh dấu đây là AI Tour
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
            
            return response()->json(['success' => true, 'slug' => $slug]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gemini API Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi kết nối AI: ' . $e->getMessage()]);
        }
    }

    /**
     * Store completed food tour diary.
     */
    public function storeDiary(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để lưu trữ nhật ký hành trình và chia sẻ ảnh kỷ niệm!'
            ], 401);
        }

        $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'image' => 'nullable|string', // base64 string
            'completed_stops' => 'nullable|array',
            'stop_reviews' => 'nullable|array',
        ]);

        // If base64 image is uploaded, decode and save to public storage
        $imagePath = null;
        if ($request->input('image')) {
            $base64 = $request->input('image');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $type = strtolower($type[1]); // e.g. png, jpeg

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

        // Also if stop reviews have base64 images, decode and save them
        $stopReviews = $request->input('stop_reviews', []);
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
                        unset($review['image']); // don't store heavy base64 inside db JSON!
                    }
                }
            }

            // Save to global eatery Review table
            if (!empty($review['eatery_id'])) {
                $user = auth()->user();
                $userName = $user ? $user->name : 'Thực khách Food Tour';
                
                $newReview = \App\Models\Review::create([
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

                // Recalculate and update the average rating of this eatery
                $eatery = \App\Models\Eatery::find($review['eatery_id']);
                if ($eatery) {
                    $avgRating = \App\Models\Review::where('eatery_id', $review['eatery_id'])->avg('rating');
                    $eatery->update(['rating' => $avgRating ?: 5.00]);
                }
            }
        }

        // Create new FoodTourDiary entry using Eloquent
        \App\Models\FoodTourDiary::create([
            'food_tour_id' => $id,
            'user_id' => auth()->id(),
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
            'image_path' => $imagePath,
            'completed_stops' => $request->input('completed_stops', []),
            'stop_reviews' => $stopReviews,
        ]);

        // Nếu đây là AI Tour đang ở trạng thái draft -> Nâng cấp lên 'saved'
        $tour = \App\Models\FoodTour::find($id);
        if ($tour && $tour->is_ai_generated && $tour->status === 'draft') {
            $updateData = ['status' => 'saved'];
            
            // Nếu user chọn chia sẻ lên cộng đồng
            if ($request->boolean('share_to_community')) {
                $updateData['shared_at'] = now();
                $updateData['expires_at'] = now()->addHours(72);
            }
            
            $tour->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lưu nhật ký hành trình thành công!',
            'image_url' => $imagePath
        ]);
    }

    /**
     * Haversine Distance Formula.
     */
    private function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = 
            sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}
