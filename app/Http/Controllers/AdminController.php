<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Commune;
use App\Models\Eatery;
use App\Models\Review;
use App\Models\User;
use App\DTOs\StoreEateryDTO;
use App\DTOs\StoreDishDTO;
use App\DTOs\StoreVideoDTO;
use App\DTOs\StoreUserDTO;
use App\DTOs\StoreCertificateDTO;
use App\DTOs\StoreDailyFoodLogDTO;
use App\DTOs\StoreSupplyContractDTO;
use App\DTOs\StorePurchaseInvoiceDTO;
use App\Services\AdminEateryService;
use App\Services\VideoService;
use App\Services\UserService;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function __construct(
        protected AdminEateryService $adminEateryService,
        protected VideoService $videoService,
        protected UserService $userService,
        protected GoogleMapsService $googleMapsService
    ) {}

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

        // Lấy danh sách quán ăn
        $eateriesQuery = Eatery::with(['category', 'commune']);
        if ($isSeller) {
            $eateriesQuery->where('user_id', $sellerId);
        }
        $eateries = $eateriesQuery->orderBy('created_at', 'desc')->paginate(10);

        // Lấy danh sách Video Reviews
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
        $eatery = null;

        return view('admin.eatery-form', compact('categories', 'communes', 'eatery'));
    }

    /**
     * Lưu trữ quán mới
     */
    public function storeEatery(Request $request)
    {
        $this->verifyAdmin();

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

        $dto = StoreEateryDTO::fromRequest($request);
        $this->adminEateryService->storeEatery($dto, session('user_id'), session('user_role'));

        return redirect('/admin/dashboard')->with('success', 'Thêm mới địa điểm ẩm thực thành công!');
    }

    /**
     * Mở form sửa thông tin quán
     */
    public function editEatery($id)
    {
        $this->verifyAdmin();

        $eatery = Eatery::with('dishes')->findOrFail($id);
        
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

        $dto = StoreEateryDTO::fromRequest($request);
        $this->adminEateryService->updateEatery((int)$id, $dto, session('user_id'), session('user_role'));

        return redirect('/admin/dashboard')->with('success', 'Cập nhật thông tin quán thành công!');
    }

    /**
     * Xóa địa điểm
     */
    public function destroyEatery($id)
    {
        $this->verifyAdmin();
        $this->adminEateryService->destroyEatery((int)$id, session('user_role'));

        return redirect('/admin/dashboard')->with('success', 'Đã xóa địa điểm khỏi hệ thống bản đồ số!');
    }

    /**
     * Tự động giải mã đường dẫn Google Maps và rút trích tọa độ
     */
    public function parseGoogleMapsUrl(Request $request)
    {
        $this->verifyAdmin();

        $request->validate([
            'url' => 'required|url',
        ]);

        $coords = $this->googleMapsService->parseUrl($request->url);

        if ($coords) {
            return response()->json([
                'success' => true,
                'latitude' => $coords['latitude'],
                'longitude' => $coords['longitude'],
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

        $dto = StoreDishDTO::fromRequest($request);
        $this->adminEateryService->storeDish($dto, session('user_id'), session('user_role'));

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

        $dto = StoreDishDTO::fromRequest($request);
        $this->adminEateryService->updateDish((int)$id, $dto, session('user_id'), session('user_role'));

        return redirect()->back()->with('success', 'Cập nhật món ăn thành công!');
    }

    /**
     * Bật/tắt trạng thái Món ăn đặc trưng
     */
    public function toggleSignatureDish($id)
    {
        $this->verifyAdmin();
        $this->adminEateryService->toggleSignatureDish((int)$id, session('user_id'), session('user_role'));

        return redirect()->back()->with('success', 'Cập nhật trạng thái món ăn thành công!');
    }

    /**
     * Xóa món ăn khỏi thực đơn
     */
    public function destroyDish($id)
    {
        $this->verifyAdmin();
        $this->adminEateryService->destroyDish((int)$id, session('user_id'), session('user_role'));

        return redirect()->back()->with('success', 'Xóa món ăn khỏi thực đơn thành công!');
    }

    /**
     * Đăng Video Review
     */
    public function storeVideo(Request $request)
    {
        $this->verifyAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'eatery_id' => 'required|exists:eateries,id',
            'video_file' => 'nullable|file|mimes:mp4,mov,ogg,qt|max:20480',
            'video_url' => 'nullable|url',
        ], [
            'video_file.max' => 'Dung lượng video đăng tải trực tiếp không được vượt quá 20MB để tối ưu dung lượng máy chủ!',
        ]);

        $dto = StoreVideoDTO::fromRequest($request);
        $video = $this->videoService->storeVideo($dto, session('user_id'), session('user_role'));

        $message = ($video->status === 'approved') 
            ? '🎉 Đăng video review thành công và đã được công khai trên bản đồ!' 
            : '🎉 Đăng video thành công! Video đang chờ Ban quản trị phê duyệt trước khi công khai.';
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Cập nhật Video Review
     */
    public function updateVideo(Request $request, $id)
    {
        $this->verifyAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'eatery_id' => 'required|exists:eateries,id',
            'video_file' => 'nullable|file|mimes:mp4,mov,ogg,qt|max:20480',
            'video_url' => 'nullable|url',
        ], [
            'video_file.max' => 'Dung lượng video đăng tải trực tiếp không được vượt quá 20MB để tối ưu dung lượng máy chủ!',
        ]);

        $dto = StoreVideoDTO::fromRequest($request);
        $video = $this->videoService->updateVideo((int)$id, $dto, session('user_id'), session('user_role'));

        $message = ($video->status === 'approved') 
            ? '🎉 Cập nhật video review thành công!' 
            : '🎉 Cập nhật video thành công! Video đã chuyển sang trạng thái chờ kiểm duyệt lại.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Phê duyệt Video Review
     */
    public function approveVideo($id)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Chỉ Quản trị viên hệ thống mới có quyền phê duyệt video!');
        }

        $this->videoService->approveVideo((int)$id);

        return redirect()->back()->with('success', '🎉 Phê duyệt video thành công! Video đã được công khai.');
    }

    /**
     * Từ chối Video Review
     */
    public function rejectVideo($id)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Chỉ Quản trị viên hệ thống mới có quyền từ chối video!');
        }

        $this->videoService->rejectVideo((int)$id);

        return redirect()->back()->with('success', '❌ Đã từ chối video review. Video này sẽ không hiển thị trên bản đồ.');
    }

    /**
     * Xóa Video Review
     */
    public function destroyVideo($id)
    {
        $this->verifyAdmin();
        $this->videoService->destroyVideo((int)$id, session('user_id'), session('user_role'));

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

        $dto = StoreCertificateDTO::fromRequest($request);
        $this->adminEateryService->storeFoodSafetyCertificate($dto, session('user_id'), session('user_role'));

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

        $dto = StoreDailyFoodLogDTO::fromRequest($request);
        $this->adminEateryService->storeDailyFoodLog($dto, session('user_id'), session('user_role'));

        return redirect()->back()->with('success', 'Ghi nhật ký kiểm tra vệ sinh hàng ngày thành công!');
    }

    /**
     * Xóa nhật ký kiểm tra
     */
    public function destroyDailyFoodLog($id)
    {
        $this->verifyAdmin();
        $this->adminEateryService->destroyDailyFoodLog((int)$id, session('user_id'), session('user_role'));

        return redirect()->back()->with('success', 'Xóa nhật ký kiểm tra thành công!');
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

        $dto = StoreSupplyContractDTO::fromRequest($request);
        $this->adminEateryService->storeFoodSupplyContract($dto, session('user_id'), session('user_role'));

        return redirect()->back()->with('success', 'Thêm mới hợp đồng cung cấp thành công!');
    }

    /**
     * Xóa hợp đồng cung cấp
     */
    public function destroyFoodSupplyContract($id)
    {
        $this->verifyAdmin();
        $this->adminEateryService->destroyFoodSupplyContract((int)$id, session('user_id'), session('user_role'));

        return redirect()->back()->with('success', 'Xóa hợp đồng thành công!');
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

        $dto = StorePurchaseInvoiceDTO::fromRequest($request);
        $this->adminEateryService->storePurchaseInvoice($dto, session('user_id'), session('user_role'));

        return redirect()->back()->with('success', 'Thêm mới hóa đơn mua bán thành công!');
    }

    /**
     * Xóa hóa đơn mua bán thực phẩm
     */
    public function destroyPurchaseInvoice($id)
    {
        $this->verifyAdmin();
        $this->adminEateryService->destroyPurchaseInvoice((int)$id, session('user_id'), session('user_role'));

        return redirect()->back()->with('success', 'Xóa hóa đơn thành công!');
    }

    /**
     * Xóa đánh giá spam hoặc phá hoại của khách hàng (Chỉ dành cho Admin tối cao)
     */
    public function destroyReview($id)
    {
        $this->verifyAdmin();
        
        if (session('user_role') !== 'admin') {
            abort(403, 'Bạn không có quyền xóa đánh giá của khách hàng!');
        }

        $review = \App\Models\Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Đã xóa đánh giá của khách hàng khỏi hệ thống!');
    }

    /**
     * Danh sách tài khoản User
     */
    public function indexUsers(Request $request)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Bạn không có quyền quản lý tài khoản người dùng!');
        }

        $users = $this->userService->searchAndPaginate($request);
        $stats = $this->userService->getRoleStats();
        
        $totalUsers = $stats['total'];
        $adminCount = $stats['admin'];
        $sellerCount = $stats['seller'];
        $userCount = $stats['user'];

        if ($request->ajax()) {
            return view('admin.users.partial-table', compact('users'))->render();
        }

        return view('admin.users.index', compact('users', 'totalUsers', 'adminCount', 'sellerCount', 'userCount'));
    }

    /**
     * Mở form tạo User mới
     */
    public function createUser()
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Bạn không có quyền thêm người dùng mới!');
        }

        return view('admin.users.create');
    }

    /**
     * Lưu trữ User mới
     */
    public function storeUser(Request $request)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Bạn không có quyền thêm người dùng mới!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,seller,user',
            'phone' => 'nullable|string|max:15',
            'avatar' => 'nullable|string|max:10',
        ], [
            'email.unique' => 'Email này đã tồn tại trên hệ thống!',
            'password.min' => 'Mật khẩu tối thiểu phải từ 6 ký tự!',
        ]);

        $dto = StoreUserDTO::fromRequest($request);
        $this->userService->storeUser($dto);

        return redirect('/admin/users')->with('success', 'Thêm mới tài khoản người dùng thành công!');
    }

    /**
     * Xem thông tin chi tiết User
     */
    public function showUser($id)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Bạn không có quyền xem thông tin chi tiết người dùng!');
        }

        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Mở form sửa thông tin User
     */
    public function editUser($id)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Bạn không có quyền chỉnh sửa tài khoản người dùng!');
        }

        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin User
     */
    public function updateUser(Request $request, $id)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Bạn không có quyền cập nhật tài khoản người dùng!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|string|in:admin,seller,user',
            'phone' => 'nullable|string|max:15',
            'avatar' => 'nullable|string|max:10',
            'status' => 'required|string|in:active,disabled',
            'password' => 'nullable|string|min:6',
        ], [
            'email.unique' => 'Email này đã tồn tại trên hệ thống!',
            'password.min' => 'Mật khẩu thay đổi phải từ 6 ký tự!',
        ]);

        $dto = StoreUserDTO::fromRequest($request);
        $this->userService->updateUser((int)$id, $dto);

        return redirect('/admin/users')->with('success', 'Cập nhật tài khoản người dùng thành công!');
    }

    /**
     * Xóa tài khoản User
     */
    public function destroyUser($id)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Bạn không có quyền xóa tài khoản người dùng!');
        }

        $res = $this->userService->destroyUser((int)$id, (int)session('user_id'));

        if ($res['status'] === 'error') {
            return redirect()->back()->with('error', $res['message']);
        }

        return redirect('/admin/users')->with('success', $res['message']);
    }

    /**
     * Bật/Tắt tài khoản người dùng
     */
    public function toggleUserStatus($id)
    {
        $this->verifyAdmin();
        if (session('user_role') !== 'admin') {
            abort(403, 'Bạn không có quyền thay đổi trạng thái tài khoản!');
        }

        $res = $this->userService->toggleUserStatus((int)$id, (int)session('user_id'));

        if ($res['status'] === 'error') {
            return redirect()->back()->with('error', $res['message']);
        }

        return redirect()->back()->with('success', $res['message']);
    }
}
