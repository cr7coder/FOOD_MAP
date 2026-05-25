<?php

namespace App\Http\Controllers;

use App\DTOs\StoreReviewDTO;
use App\Services\EateryService;
use Illuminate\Http\Request;

class EateryController extends Controller
{
    public function __construct(
        protected EateryService $eateryService
    ) {}

    public function show($slug)
    {
        $data = $this->eateryService->getDetailAndSchema($slug);
        
        $eatery = $data['eatery'];
        $jsonLd = $data['jsonLd'];
        
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
        
        $dto = StoreReviewDTO::fromRequest($request);
        $this->eateryService->storeReview((int)$id, $dto);
        
        return redirect()->back()->with('success', 'Cảm ơn bạn đã gửi đánh giá! Nhận xét của bạn giúp ích cho cộng đồng du lịch Đông Anh.');
    }
}
