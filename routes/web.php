<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\EateryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FoodTourController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Ở đây định nghĩa toàn bộ đường dẫn (routes) của Bản đồ số Ẩm thực Đông Anh.
|
*/

// --- USER SIDE ROUTES (Giao diện người dùng) ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tim-kiem', [SearchController::class, 'search'])->name('search');

// URL Thân thiện chuẩn SEO Google cho địa điểm ẩm thực & đặc sản
Route::get('/dia-diem/dac-san/{slug}', [EateryController::class, 'show'])->name('eatery.show');
Route::post('/dia-diem/reviews/{id}', [EateryController::class, 'storeReview'])->name('eatery.review.store');

// SEO Sitemap Route
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');

// API Video Reels đặc sản Đông Anh (Tóp Tóp Food Tour)
Route::get('/api/videos', [HomeController::class, 'getVideos'])->name('api.videos');
Route::post('/api/videos/{id}/like', [HomeController::class, 'likeVideo'])->name('api.videos.like');

// --- FOOD TOUR JOURNEY ROUTES (Trải nghiệm hành trình ẩm thực) ---
Route::get('/food-tours', [FoodTourController::class, 'index'])->name('food-tours.index');
Route::get('/cooking-tours', [FoodTourController::class, 'cookingIndex'])->name('cooking-tours.index');
Route::get('/food-tour/{slug}', [FoodTourController::class, 'show'])->name('food-tours.show');
Route::post('/api/food-tours/generate-ai', [FoodTourController::class, 'generateAI'])->name('api.food-tours.generate-ai');
Route::post('/api/food-tours/{id}/diary', [FoodTourController::class, 'storeDiary'])->name('api.food-tours.store-diary');


// --- AUTHENTICATION ROUTES (Đăng nhập / Đăng ký) ---
Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');


// --- ADMIN SIDE ROUTES (Giao diện quản trị viên) ---
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/eateries/create', [AdminController::class, 'createEatery'])->name('admin.eatery.create');
    Route::post('/eateries', [AdminController::class, 'storeEatery'])->name('admin.eatery.store');
    Route::get('/eateries/{id}/edit', [AdminController::class, 'editEatery'])->name('admin.eatery.edit');
    Route::put('/eateries/{id}', [AdminController::class, 'updateEatery'])->name('admin.eatery.update');
    Route::delete('/eateries/{id}', [AdminController::class, 'destroyEatery'])->name('admin.eatery.destroy');
    Route::post('/parse-google-maps', [AdminController::class, 'parseGoogleMapsUrl'])->name('admin.parse-google-maps');
    
    // Quản lý Thực đơn & Món ăn đặc trưng
    Route::post('/dishes', [AdminController::class, 'storeDish'])->name('admin.dish.store');
    Route::post('/dishes/{id}/toggle-signature', [AdminController::class, 'toggleSignatureDish'])->name('admin.dish.toggle-signature');
    Route::delete('/dishes/{id}', [AdminController::class, 'destroyDish'])->name('admin.dish.destroy');

    // Quản lý Video Reels đặc sản
    Route::post('/videos', [AdminController::class, 'storeVideo'])->name('admin.video.store');
    Route::put('/videos/{id}', [AdminController::class, 'updateVideo'])->name('admin.video.update');
    Route::delete('/videos/{id}', [AdminController::class, 'destroyVideo'])->name('admin.video.destroy');
    Route::post('/videos/{id}/approve', [AdminController::class, 'approveVideo'])->name('admin.video.approve');
    Route::post('/videos/{id}/reject', [AdminController::class, 'rejectVideo'])->name('admin.video.reject');
});
