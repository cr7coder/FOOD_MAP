<?php

namespace App\Http\Controllers;

use App\DTOs\GenerateAITourDTO;
use App\DTOs\StoreDiaryDTO;
use App\Models\FoodTour;
use App\Services\FoodTourService;
use Illuminate\Http\Request;

class FoodTourController extends Controller
{
    public function __construct(
        protected FoodTourService $foodTourService
    ) {}

    /**
     * Display a listing of the pre-designed food tours.
     */
    public function index(Request $request)
    {
        $mood = $request->query('mood');
        
        $query = FoodTour::public()->where('mood', '!=', 'cooking')->with(['stops.eatery', 'diaries.user'])->withCount('diaries');
        
        if ($mood) {
            $query->where('mood', $mood);
        }
        
        $tours = $query->get();

        // Lấy các AI Tour đang được chia sẻ công khai bởi cộng đồng
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
     */
    public function generateAI(Request $request)
    {
        $dto = GenerateAITourDTO::fromRequest($request);

        try {
            $tour = $this->foodTourService->generateAITour($dto);
            return response()->json(['success' => true, 'slug' => $tour->slug]);
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
            'image' => 'nullable|string',
            'completed_stops' => 'nullable|array',
            'stop_reviews' => 'nullable|array',
        ]);

        $dto = StoreDiaryDTO::fromRequest($request);
        $diary = $this->foodTourService->storeDiary((int)$id, auth()->id(), $dto);

        return response()->json([
            'success' => true,
            'message' => 'Lưu nhật ký hành trình thành công!',
            'image_url' => $diary->image_path
        ]);
    }
}
