<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Commune;
use App\Models\Eatery;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $categories = Category::all();
        $communes = Commune::all();
        
        $keyword = $request->query('q');
        $catId = $request->query('category_id');
        $comId = $request->query('commune_id');
        
        $query = Eatery::with(['category', 'commune', 'reviewVideos' => function($q) {
            $q->where('status', 'approved');
        }])->active();
        
        // Tìm theo từ khóa (tên quán, mô tả, địa chỉ, hoặc món ăn trong thực đơn)
        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%")
                  ->orWhereHas('dishes', function($dQuery) use ($keyword) {
                      $dQuery->where('name', 'like', "%{$keyword}%");
                  });
            });
        }
        
        // Lọc theo danh mục ẩm thực
        if ($catId) {
            $query->where('category_id', $catId);
        }
        
        // Lọc theo xã/thị trấn
        if ($comId) {
            $query->where('commune_id', $comId);
        }
        
        $eateries = $query->get();
        
        // API phục vụ tính năng tự động gợi ý (Autocomplete Suggestions) khi gõ ô tìm kiếm
        if ($request->query('ajax') === 'suggest' && $keyword) {
            $suggestions = Eatery::active()
                ->where('name', 'like', "%{$keyword}%")
                ->limit(6)
                ->get(['id', 'name', 'slug', 'address']);
            return response()->json($suggestions);
        }
        
        // Trả về JSON nếu yêu cầu API (cập nhật marker bản đồ thời gian thực)
        if ($request->expectsJson() || $request->query('json') === '1') {
            return response()->json($eateries);
        }
        
        return view('search', compact('categories', 'communes', 'eateries', 'keyword', 'catId', 'comId'));
    }
}
