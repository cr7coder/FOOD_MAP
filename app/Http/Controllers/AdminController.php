<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Commune;
use App\Models\Eatery;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Xác minh quyền truy cập Admin / Seller trước khi thực hiện bất cứ action nào
     */
    private function verifyAdmin()
    {
        $role = session('user_role');
        if ($role !== 'admin' && $role !== 'seller') {
            abort(403, 'Bạn không có quyền truy cập trang quản lý này!');
        }
    }

    /**
     * Hiển thị Dashboard Admin / Seller
     */
    public function dashboard()
    {
        $this->verifyAdmin();

        $isSeller = session('user_role') === 'seller';
        $sellerId = session('user_id');

        $stats = [
            'total_eateries' => $isSeller ? Eatery::where('user_id', $sellerId)->count() : Eatery::count(),
            'total_categories' => Category::count(),
            'total_communes' => Commune::count(),
            'total_reviews' => $isSeller 
                ? Review::whereIn('eatery_id', Eatery::where('user_id', $sellerId)->pluck('id'))->count() 
                : Review::count(),
        ];

        // Lấy danh sách quán ăn (nếu là Seller thì chỉ lấy quán thuộc quyền sở hữu của họ)
        $eateriesQuery = Eatery::with(['category', 'commune']);
        if ($isSeller) {
            $eateriesQuery->where('user_id', $sellerId);
        }
        $eateries = $eateriesQuery->orderBy('created_at', 'desc')->get();

        // Lấy danh sách Video Reviews (Admin xem hết, Seller xem các cơ sở của họ)
        $videosQuery = \App\Models\ReviewVideo::with(['eatery.category', 'user']);
        if ($isSeller) {
            $videosQuery->whereIn('eatery_id', Eatery::where('user_id', $sellerId)->pluck('id'));
        }
        $videos = $videosQuery->orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('stats', 'eateries', 'videos'));
    }

    /**
     * Mở form thêm quán mới
     */
    public function createEatery()
    {
        $this->verifyAdmin();

        $role = session('user_role');
        if ($role === 'seller') {
            $hasEatery = Eatery::where('user_id', session('user_id'))->exists();
            if ($hasEatery) {
                return redirect('/admin/dashboard')->with('error', 'Mỗi Chủ quán chỉ được đăng ký duy nhất 1 địa điểm kinh doanh!');
            }
        }

        $categories = Category::all();
        $communes = Commune::all();
        $eatery = null; // Phân biệt Form Thêm và Form Sửa

        return view('admin.eatery-form', compact('categories', 'communes', 'eatery'));
    }

    /**
     * Lưu trữ quán mới
     */
    public function storeEatery(Request $request)
    {
        $this->verifyAdmin();

        $role = session('user_role');
        if ($role === 'seller') {
            $hasEatery = Eatery::where('user_id', session('user_id'))->exists();
            if ($hasEatery) {
                return redirect('/admin/dashboard')->with('error', 'Mỗi Chủ quán chỉ được đăng ký duy nhất 1 địa điểm kinh doanh!');
            }
        }

        // Diagnostic log for file uploads
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            \Log::info('Image upload diagnostics:', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getClientMimeType(),
                'error_code' => $file->getError(),
                'error_msg' => $file->getErrorMessage(),
                'is_valid' => $file->isValid(),
            ]);
        } else {
            \Log::info('No file uploaded with key "image". Keys present: ' . implode(', ', array_keys($request->allFiles())));
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:eateries,name',
            'category_id' => 'required|exists:categories,id',
            'commune_id' => 'required|exists:communes,id',
            'address' => 'required|string|max:200',
            'phone' => 'nullable|string|max:20',
            'opening_hours' => 'nullable|string|max:50',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'price_range' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'image_url' => 'nullable|url',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        $slug = Str::slug($request->name);
        $imagePath = null;

        // Xử lý upload ảnh trực tiếp qua Google Drive (có fallback cục bộ)
        if ($request->hasFile('image')) {
            $imagePath = \App\Helpers\GoogleDriveHelper::upload($request->file('image'), 'eateries');
        } elseif ($request->image_url) {
            $url = $request->image_url;
            if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?id=))([a-zA-Z0-9_-]{25,50})/i', $url, $matches)) {
                $imagePath = 'https://drive.google.com/uc?export=download&id=' . $matches[1];
            } else {
                $imagePath = $url;
            }
        }

        Eatery::create([
            'user_id' => session('user_role') === 'seller' ? session('user_id') : null,
            'name' => $request->name,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'commune_id' => $request->commune_id,
            'address' => $request->address,
            'phone' => $request->phone,
            'opening_hours' => $request->opening_hours,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'price_range' => $request->price_range ?: '30.000đ - 100.000đ',
            'image_path' => $imagePath,
            'is_featured' => session('user_role') === 'admin' ? $request->has('is_featured') : false,
            'description' => $request->description,
            'rating' => 5.0, // Điểm mặc định ban đầu
            'status' => 'active',
        ]);

        return redirect('/admin/dashboard')->with('success', 'Thêm mới địa điểm ẩm thực thành công!');
    }

    /**
     * Mở form sửa thông tin quán
     */
    public function editEatery($id)
    {
        $this->verifyAdmin();

        $eatery = Eatery::with([
            'dishes',
            'reviewVideos',
            'foodSafetyCertificate',
            'foodSupplyContracts',
            'purchaseInvoices',
            'dailyFoodLogs'
        ])->findOrFail($id);
        
        // Ngăn chặn Seller chỉnh sửa quán ăn của người khác
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền sửa đổi cơ sở này!');
        }

        $categories = Category::all();
        $communes = Commune::all();

        return view('admin.eatery-form', compact('eatery', 'categories', 'communes'));
    }

    /**
     * Cập nhật thông tin quán
     */
    public function updateEatery(Request $request, $id)
    {
        $this->verifyAdmin();
        $eatery = Eatery::findOrFail($id);

        // Ngăn chặn Seller cập nhật quán ăn của người khác
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền sửa đổi cơ sở này!');
        }

        // Diagnostic log for file uploads
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            \Log::info('Image upload diagnostics (Update):', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getClientMimeType(),
                'error_code' => $file->getError(),
                'error_msg' => $file->getErrorMessage(),
                'is_valid' => $file->isValid(),
            ]);
        } else {
            \Log::info('No file uploaded with key "image" (Update). Keys present: ' . implode(', ', array_keys($request->allFiles())));
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:eateries,name,' . $id,
            'category_id' => 'required|exists:categories,id',
            'commune_id' => 'required|exists:communes,id',
            'address' => 'required|string|max:200',
            'phone' => 'nullable|string|max:20',
            'opening_hours' => 'nullable|string|max:50',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'price_range' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'image_url' => 'nullable|url',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        $slug = Str::slug($request->name);
        $imagePath = $eatery->image_path;

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu là file cục bộ
            if ($eatery->image_path && \Str::startsWith($eatery->image_path, '/uploads/eateries/')) {
                $oldPath = public_path($eatery->image_path);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $imagePath = \App\Helpers\GoogleDriveHelper::upload($request->file('image'), 'eateries');
        } elseif ($request->image_url) {
            $url = $request->image_url;
            if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?id=))([a-zA-Z0-9_-]{25,50})/i', $url, $matches)) {
                $imagePath = 'https://drive.google.com/uc?export=download&id=' . $matches[1];
            } else {
                $imagePath = $url;
            }
        }

        $eatery->update([
            'name' => $request->name,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'commune_id' => $request->commune_id,
            'address' => $request->address,
            'phone' => $request->phone,
            'opening_hours' => $request->opening_hours,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'price_range' => $request->price_range,
            'image_path' => $imagePath,
            'is_featured' => session('user_role') === 'admin' ? $request->has('is_featured') : $eatery->is_featured,
            'description' => $request->description,
        ]);

        return redirect('/admin/dashboard')->with('success', 'Cập nhật thông tin quán thành công!');
    }

    public function destroyEatery($id)
    {
        $this->verifyAdmin();
        
        $eatery = Eatery::findOrFail($id);
        
        // Ngăn chặn Seller tự ý xóa địa điểm kinh doanh của mình
        if (session('user_role') === 'seller') {
            abort(403, 'Chủ quán không được phép tự xóa địa điểm kinh doanh của mình! Vui lòng liên hệ Quản trị viên nếu bạn có nhu cầu thay đổi cơ sở.');
        }

        $eatery->delete();

        return redirect('/admin/dashboard')->with('success', 'Đã xóa địa điểm khỏi hệ thống bản đồ số!');
    }

    /**
     * Tự động giải mã đường dẫn Google Maps (kể cả link rút gọn) và rút trích Tọa độ Kinh/Vĩ
     */
    public function parseGoogleMapsUrl(Request $request)
    {
        $this->verifyAdmin();

        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->url;

        // Nếu là link rút gọn maps.app.goo.gl hoặc goo.gl, cần phân giải redirect
        if (Str::contains($url, ['maps.app.goo.gl', 'goo.gl'])) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_exec($ch);
                $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                curl_close($ch);
                if ($finalUrl) {
                    $url = $finalUrl;
                }
            } catch (\Exception $e) {
                // Tiếp tục xử lý URL gốc nếu xảy ra lỗi cURL
            }
        }

        $lat = null;
        $lng = null;

        // Định dạng 1: Chứa @vĩđộ,kinhđộ (ví dụ: @21.1118671,105.8698539)
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            $lat = $matches[1];
            $lng = $matches[2];
        }
        // Định dạng 2: Chứa q=vĩđộ,kinhđộ (ví dụ: q=21.1118671,105.8698539)
        elseif (preg_match('/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            $lat = $matches[1];
            $lng = $matches[2];
        }
        // Định dạng 3: Chứa mã nhúng 3d/4d nội bộ (!3d21.1118671!4d105.8698539)
        elseif (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $url, $matches)) {
            $lat = $matches[1];
            $lng = $matches[2];
        }

        if ($lat && $lng) {
            return response()->json([
                'success' => true,
                'latitude' => (double)$lat,
                'longitude' => (double)$lng,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy tọa độ trong đường dẫn này. Vui lòng nhập link chuẩn chứa tọa độ hoặc chọn trực tiếp trên Bản đồ.',
        ]);
    }

    /**
     * Thêm món ăn mới vào thực đơn của quán
     */
    public function storeDish(Request $request)
    {
        $this->verifyAdmin();

        $request->validate([
            'eatery_id' => 'required|exists:eateries,id',
            'dish_name' => 'required|string|max:100',
            'dish_price' => 'required|numeric|min:0',
            'dish_description' => 'nullable|string',
            'dish_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'dish_image_url' => 'nullable|url',
            'is_signature' => 'nullable|boolean',
        ]);

        $eatery = Eatery::findOrFail($request->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền quản trị thực đơn của cơ sở này!');
        }

        $imagePath = null;

        // Xử lý upload ảnh món ăn qua Google Drive (có fallback cục bộ)
        if ($request->hasFile('dish_image')) {
            $imagePath = \App\Helpers\GoogleDriveHelper::upload($request->file('dish_image'), 'dishes');
        } elseif ($request->dish_image_url) {
            $url = $request->dish_image_url;
            if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?id=))([a-zA-Z0-9_-]{25,50})/i', $url, $matches)) {
                $imagePath = 'https://drive.google.com/uc?export=download&id=' . $matches[1];
            } else {
                $imagePath = $url;
            }
        }

        \App\Models\Dish::create([
            'eatery_id' => $request->eatery_id,
            'name' => $request->dish_name,
            'price' => $request->dish_price,
            'description' => $request->dish_description,
            'image_path' => $imagePath,
            'is_signature' => $request->has('is_signature'),
        ]);

        return redirect()->back()->with('success', 'Thêm món ăn vào thực đơn thành công!');
    }

    /**
     * Cập nhật thông tin món ăn trong thực đơn
     */
    public function updateDish(Request $request, $id)
    {
        $this->verifyAdmin();

        $request->validate([
            'dish_name' => 'required|string|max:100',
            'dish_price' => 'required|numeric|min:0',
            'dish_description' => 'nullable|string',
            'dish_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'dish_image_url' => 'nullable|url',
            'is_signature' => 'nullable|boolean',
        ]);

        $dish = \App\Models\Dish::findOrFail($id);
        $eatery = Eatery::findOrFail($dish->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền quản trị thực đơn của cơ sở này!');
        }

        $imagePath = $dish->image_path;

        // Xử lý upload ảnh món ăn mới qua Google Drive (có fallback cục bộ)
        if ($request->hasFile('dish_image')) {
            $imagePath = \App\Helpers\GoogleDriveHelper::upload($request->file('dish_image'), 'dishes');
        } elseif ($request->has('dish_image_url')) {
            if ($request->dish_image_url) {
                $url = $request->dish_image_url;
                if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?id=))([a-zA-Z0-9_-]{25,50})/i', $url, $matches)) {
                    $imagePath = 'https://drive.google.com/uc?export=download&id=' . $matches[1];
                } else {
                    $imagePath = $url;
                }
            } else {
                $imagePath = null;
            }
        }

        $dish->update([
            'name' => $request->dish_name,
            'price' => $request->dish_price,
            'description' => $request->dish_description,
            'image_path' => $imagePath,
            'is_signature' => $request->has('is_signature'),
        ]);

        return redirect()->back()->with('success', 'Cập nhật món ăn thành công!');
    }

    /**
     * Bật/tắt trạng thái Món ăn đặc trưng
     */
    public function toggleSignatureDish($id)
    {
        $this->verifyAdmin();

        $dish = \App\Models\Dish::findOrFail($id);
        $eatery = Eatery::findOrFail($dish->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền quản trị thực đơn của cơ sở này!');
        }

        $dish->update([
            'is_signature' => !$dish->is_signature
        ]);

        return redirect()->back()->with('success', 'Cập nhật trạng thái món ăn thành công!');
    }

    /**
     * Xóa món ăn khỏi thực đơn
     */
    public function destroyDish($id)
    {
        $this->verifyAdmin();

        $dish = \App\Models\Dish::findOrFail($id);
        $eatery = Eatery::findOrFail($dish->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền quản trị thực đơn của cơ sở này!');
        }

        $dish->delete();

        return redirect()->back()->with('success', 'Xóa món ăn khỏi thực đơn thành công!');
    }

    /**
     * Đăng Video Review (Hỗ trợ nhúng TikTok/Shorts tối ưu dung lượng)
     */
    public function storeVideo(Request $request)
    {
        $this->verifyAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'eatery_id' => 'required|exists:eateries,id',
            'video_file' => 'nullable|file|mimes:mp4,mov,ogg,qt|max:20480', // tối đa 20MB
            'video_url' => 'nullable|url',
        ], [
            'video_file.max' => 'Dung lượng video đăng tải trực tiếp không được vượt quá 20MB để tối ưu dung lượng máy chủ!',
        ]);

        $eatery = Eatery::findOrFail($request->eatery_id);
        $role = session('user_role');
        $userId = session('user_id');

        // Phân quyền cho Seller
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không thể đăng video review cho cơ sở không thuộc sở hữu của bạn!');
        }

        $videoUrl = '';
        $videoType = 'local';
        $thumbnailPath = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80'; // mặc định

        if ($request->hasFile('video_file')) {
            // Đăng tải trực tiếp qua Google Drive (có fallback cục bộ)
            $videoUrl = \App\Helpers\GoogleDriveHelper::upload($request->file('video_file'), 'videos');
            $videoType = 'local';
        } elseif ($request->video_url) {
            // Hỗ trợ Nhúng thông minh từ link bên ngoài
            $url = $request->video_url;
            
            // Regex bóc tách Google Drive link thủ công
            if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?id=))([a-zA-Z0-9_-]{25,50})/i', $url, $matches)) {
                $videoType = 'local';
                $videoUrl = 'https://drive.google.com/uc?export=download&id=' . $matches[1];
            }
            // Regex bóc tách TikTok ID
            elseif (preg_match('/tiktok\.com\/(@[^\/]+\/video\/(\d+)|v\/(\d+))/i', $url, $matches) || preg_match('/vt\.tiktok\.com\/(\w+)/i', $url)) {
                $videoType = 'tiktok';
                $videoUrl = $url;
            } 
            // Regex bóc tách YouTube ID (bao gồm cả Shorts, watch?v=, share link youtu.be/...)
            elseif (preg_match('/(?:youtube\.com\/(?:shorts\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                $videoType = 'youtube_shorts';
                $videoUrl = $url;
            } else {
                $videoType = 'local';
                $videoUrl = $url;
            }
        } else {
            return redirect()->back()->withErrors(['video_url' => 'Vui lòng tải lên file video hoặc dán link TikTok/YouTube Shorts để nhúng!']);
        }

        $status = ($role === 'admin') ? 'approved' : 'pending';

        \App\Models\ReviewVideo::create([
            'eatery_id' => $eatery->id,
            'user_id' => $userId,
            'title' => $request->title,
            'video_url' => $videoUrl,
            'video_type' => $videoType,
            'thumbnail_path' => $thumbnailPath,
            'likes_count' => 0,
            'status' => $status
        ]);

        $message = ($status === 'approved') 
            ? '🎉 Đăng video review thành công và đã được công khai trên bản đồ!' 
            : '🎉 Đăng video thành công! Video đang chờ Ban quản trị phê duyệt trước khi công khai.';
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Cập nhật Video Review (Hỗ trợ cả Nhúng link và Tải file mới)
     */
    public function updateVideo(Request $request, $id)
    {
        $this->verifyAdmin();
        $video = \App\Models\ReviewVideo::findOrFail($id);
        $eatery = Eatery::findOrFail($video->eatery_id);
        $role = session('user_role');
        $userId = session('user_id');

        // Phân quyền cho Seller
        if ($role === 'seller' && $video->user_id !== $userId && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không thể sửa video review không thuộc sở hữu của bạn!');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'eatery_id' => 'required|exists:eateries,id',
            'video_file' => 'nullable|file|mimes:mp4,mov,ogg,qt|max:20480', // tối đa 20MB
            'video_url' => 'nullable|url',
        ], [
            'video_file.max' => 'Dung lượng video đăng tải trực tiếp không được vượt quá 20MB để tối ưu dung lượng máy chủ!',
        ]);

        $newEatery = Eatery::findOrFail($request->eatery_id);
        if ($role === 'seller' && $newEatery->user_id !== $userId) {
            abort(403, 'Bạn không thể liên kết video với cơ sở không thuộc sở hữu của bạn!');
        }

        $videoUrl = $video->video_url;
        $videoType = $video->video_type;

        // Có tải video file mới lên
        if ($request->hasFile('video_file')) {
            // Xóa video cũ nếu là file cục bộ
            if ($video->video_type === 'local' && \Str::startsWith($video->video_url, '/uploads/videos/')) {
                $oldFilePath = public_path($video->video_url);
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }

            $videoUrl = \App\Helpers\GoogleDriveHelper::upload($request->file('video_file'), 'videos');
            $videoType = 'local';
        } 
        // Có đổi link nhúng mới
        elseif ($request->video_url && $request->video_url !== $video->video_url) {
            // Xóa video cũ nếu là file cục bộ
            if ($video->video_type === 'local' && \Str::startsWith($video->video_url, '/uploads/videos/')) {
                $oldFilePath = public_path($video->video_url);
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }

            $url = $request->video_url;
            
            // Regex bóc tách Google Drive link thủ công
            if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?id=))([a-zA-Z0-9_-]{25,50})/i', $url, $matches)) {
                $videoType = 'local';
                $videoUrl = 'https://drive.google.com/uc?export=download&id=' . $matches[1];
            }
            // Regex bóc tách TikTok ID
            elseif (preg_match('/tiktok\.com\/(@[^\/]+\/video\/(\d+)|v\/(\d+))/i', $url, $matches) || preg_match('/vt\.tiktok\.com\/(\w+)/i', $url)) {
                $videoType = 'tiktok';
                $videoUrl = $url;
            } 
            // Regex bóc tách YouTube ID (bao gồm cả Shorts, watch?v=, share link youtu.be/...)
            elseif (preg_match('/(?:youtube\.com\/(?:shorts\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                $videoType = 'youtube_shorts';
                $videoUrl = $url;
            } else {
                $videoType = 'local';
                $videoUrl = $url;
            }
        }

        $status = ($role === 'admin') ? $video->status : 'pending';

        $video->update([
            'eatery_id' => $newEatery->id,
            'title' => $request->title,
            'video_url' => $videoUrl,
            'video_type' => $videoType,
            'status' => $status
        ]);

        $message = ($status === 'approved') 
            ? '🎉 Cập nhật video review thành công!' 
            : '🎉 Cập nhật video thành công! Video đã chuyển sang trạng thái chờ kiểm duyệt lại.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Phê duyệt Video Review (Admin only)
     */
    public function approveVideo($id)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Chỉ Quản trị viên hệ thống mới có quyền phê duyệt video!');
        }

        $video = \App\Models\ReviewVideo::findOrFail($id);
        $video->update(['status' => 'approved']);

        return redirect()->back()->with('success', '🎉 Phê duyệt video thành công! Video đã được công khai.');
    }

    /**
     * Từ chối Video Review (Admin only)
     */
    public function rejectVideo($id)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Chỉ Quản trị viên hệ thống mới có quyền từ chối video!');
        }

        $video = \App\Models\ReviewVideo::findOrFail($id);
        $video->update(['status' => 'rejected']);

        return redirect()->back()->with('success', '❌ Đã từ chối video review. Video này sẽ không hiển thị trên bản đồ.');
    }

    /**
     * Xóa Video Review
     */
    public function destroyVideo($id)
    {
        $this->verifyAdmin();
        $video = \App\Models\ReviewVideo::findOrFail($id);
        $eatery = Eatery::findOrFail($video->eatery_id);

        // Bảo vệ quyền của Seller: chỉ được xóa video do mình đăng hoặc thuộc eatery của mình
        if (session('user_role') === 'seller' && $video->user_id !== session('user_id') && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền xóa video review này!');
        }

        // Xóa file cục bộ nếu có
        if ($video->video_type === 'local' && Str::startsWith($video->video_url, '/uploads/videos/')) {
            $filePath = public_path($video->video_url);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $video->delete();

        return redirect()->back()->with('success', '🗑️ Xóa video review thành công!');
    }

    /**
     * Cập nhật Giấy Chứng Nhận An Toàn Thực Phẩm
     */
    public function storeFoodSafetyCertificate(Request $request)
    {
        $this->verifyAdmin();
        $request->validate([
            'eatery_id' => 'required|exists:eateries,id',
            'certificate_number' => 'required|string|max:100',
            'issued_by' => 'required|string|max:150',
            'issued_at' => 'required|date',
            'expired_at' => 'required|date|after:issued_at',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'image_url' => 'nullable|url',
        ]);

        $eatery = Eatery::findOrFail($request->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền cập nhật hồ sơ của cơ sở này!');
        }

        $imagePath = '/uploads/certificates/default-cert.jpg';
        if ($request->hasFile('image')) {
            $imagePath = \App\Helpers\GoogleDriveHelper::upload($request->file('image'), 'certificates');
        } elseif ($request->image_url) {
            $imagePath = $request->image_url;
        }

        \App\Models\FoodSafetyCertificate::updateOrCreate(
            ['eatery_id' => $request->eatery_id],
            [
                'certificate_number' => $request->certificate_number,
                'issued_by' => $request->issued_by,
                'issued_at' => $request->issued_at,
                'expired_at' => $request->expired_at,
                'image_path' => $imagePath,
            ]
        );

        return redirect()->back()->with('success', 'Cập nhật Giấy chứng nhận ATTP thành công!');
    }

    /**
     * Ghi nhật ký kiểm tra an toàn thực phẩm hàng ngày
     */
    public function storeDailyFoodLog(Request $request)
    {
        $this->verifyAdmin();
        $request->validate([
            'eatery_id' => 'required|exists:eateries,id',
            'log_date' => 'required|date',
            'ingredients_origin' => 'required|string|max:255',
            'storage_condition' => 'required|string|max:255',
            'checker_name' => 'required|string|max:100',
        ]);

        $eatery = Eatery::findOrFail($request->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền quản lý nhật ký của cơ sở này!');
        }

        \App\Models\DailyFoodLog::create([
            'eatery_id' => $request->eatery_id,
            'log_date' => $request->log_date,
            'ingredients_origin' => $request->ingredients_origin,
            'storage_condition' => $request->storage_condition,
            'checker_name' => $request->checker_name,
        ]);

        return redirect()->back()->with('success', 'Ghi nhật ký kiểm tra vệ sinh hàng ngày thành công!');
    }

    /**
     * Thêm hợp đồng cung cấp thực phẩm
     */
    public function storeFoodSupplyContract(Request $request)
    {
        $this->verifyAdmin();
        $request->validate([
            'eatery_id' => 'required|exists:eateries,id',
            'supplier_name' => 'required|string|max:150',
            'items_supplied' => 'required|string|max:255',
            'signed_at' => 'required|date',
            'expired_at' => 'required|date|after:signed_at',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'image_url' => 'nullable|url',
        ]);

        $eatery = Eatery::findOrFail($request->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền quản lý hợp đồng của cơ sở này!');
        }

        $imagePath = '/uploads/contracts/default-contract.jpg';
        if ($request->hasFile('image')) {
            $imagePath = \App\Helpers\GoogleDriveHelper::upload($request->file('image'), 'contracts');
        } elseif ($request->image_url) {
            $imagePath = $request->image_url;
        }

        \App\Models\FoodSupplyContract::create([
            'eatery_id' => $request->eatery_id,
            'supplier_name' => $request->supplier_name,
            'items_supplied' => $request->items_supplied,
            'signed_at' => $request->signed_at,
            'expired_at' => $request->expired_at,
            'image_path' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Thêm mới hợp đồng cung cấp thành công!');
    }

    /**
     * Thêm hóa đơn mua bán thực phẩm
     */
    public function storePurchaseInvoice(Request $request)
    {
        $this->verifyAdmin();
        $request->validate([
            'eatery_id' => 'required|exists:eateries,id',
            'supplier_name' => 'required|string|max:150',
            'items_summary' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'image_url' => 'nullable|url',
        ]);

        $eatery = Eatery::findOrFail($request->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền quản lý hóa đơn của cơ sở này!');
        }

        $imagePath = '/uploads/invoices/default-invoice.jpg';
        if ($request->hasFile('image')) {
            $imagePath = \App\Helpers\GoogleDriveHelper::upload($request->file('image'), 'invoices');
        } elseif ($request->image_url) {
            $imagePath = $request->image_url;
        }

        \App\Models\PurchaseInvoice::create([
            'eatery_id' => $request->eatery_id,
            'supplier_name' => $request->supplier_name,
            'items_summary' => $request->items_summary,
            'invoice_date' => $request->invoice_date,
            'image_path' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Thêm mới hóa đơn mua bán thành công!');
    }

    public function destroyFoodSupplyContract($id)
    {
        $this->verifyAdmin();
        $contract = \App\Models\FoodSupplyContract::findOrFail($id);
        $eatery = Eatery::findOrFail($contract->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền quản lý hợp đồng của cơ sở này!');
        }

        $contract->delete();
        return redirect()->back()->with('success', 'Xóa hợp đồng thành công!');
    }

    public function destroyPurchaseInvoice($id)
    {
        $this->verifyAdmin();
        $invoice = \App\Models\PurchaseInvoice::findOrFail($id);
        $eatery = Eatery::findOrFail($invoice->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền quản lý hóa đơn của cơ sở này!');
        }

        $invoice->delete();
        return redirect()->back()->with('success', 'Xóa hóa đơn thành công!');
    }

    public function destroyDailyFoodLog($id)
    {
        $this->verifyAdmin();
        $log = \App\Models\DailyFoodLog::findOrFail($id);
        $eatery = Eatery::findOrFail($log->eatery_id);
        if (session('user_role') === 'seller' && $eatery->user_id !== session('user_id')) {
            abort(403, 'Bạn không có quyền quản lý nhật ký của cơ sở này!');
        }

        $log->delete();
        return redirect()->back()->with('success', 'Xóa nhật ký kiểm tra thành công!');
    }
}

