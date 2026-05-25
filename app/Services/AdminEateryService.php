<?php

namespace App\Services;

use App\Models\Eatery;
use App\Models\Dish;
use App\Models\FoodSafetyCertificate;
use App\Models\DailyFoodLog;
use App\Models\FoodSupplyContract;
use App\Models\PurchaseInvoice;
use App\DTOs\StoreEateryDTO;
use App\DTOs\StoreDishDTO;
use App\DTOs\StoreCertificateDTO;
use App\DTOs\StoreDailyFoodLogDTO;
use App\DTOs\StoreSupplyContractDTO;
use App\DTOs\StorePurchaseInvoiceDTO;
use App\Helpers\GoogleDriveHelper;
use Illuminate\Support\Str;

class AdminEateryService
{
    /**
     * Lưu trữ quán mới
     */
    public function storeEatery(StoreEateryDTO $dto, ?int $userId, string $role): Eatery
    {
        if ($role === 'seller') {
            $hasEatery = Eatery::where('user_id', $userId)->exists();
            if ($hasEatery) {
                abort(400, 'Mỗi Chủ quán chỉ được đăng ký duy nhất 1 địa điểm kinh doanh!');
            }
        }

        $imagePath = null;
        if ($dto->imageFile) {
            $imagePath = GoogleDriveHelper::upload($dto->imageFile, 'eateries');
        } elseif ($dto->imageUrl) {
            $imagePath = $this->parseDriveUrl($dto->imageUrl);
        }

        return Eatery::create([
            'user_id' => $role === 'seller' ? $userId : null,
            'name' => $dto->name,
            'slug' => Str::slug($dto->name),
            'category_id' => $dto->categoryId,
            'commune_id' => $dto->communeId,
            'address' => $dto->address,
            'phone' => $dto->phone,
            'opening_hours' => $dto->openingHours,
            'latitude' => $dto->latitude,
            'longitude' => $dto->longitude,
            'price_range' => $dto->priceRange ?: '30.000đ - 100.000đ',
            'image_path' => $imagePath,
            'is_featured' => $role === 'admin' ? $dto->isFeatured : false,
            'description' => $dto->description,
            'rating' => 5.0,
            'status' => 'active',
        ]);
    }

    /**
     * Cập nhật quán ăn
     */
    public function updateEatery(int $id, StoreEateryDTO $dto, ?int $userId, string $role): Eatery
    {
        $eatery = Eatery::findOrFail($id);

        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền sửa đổi cơ sở này!');
        }

        $imagePath = $eatery->image_path;

        if ($dto->imageFile) {
            // Xóa ảnh cũ nếu là file cục bộ
            if ($eatery->image_path && Str::startsWith($eatery->image_path, '/uploads/eateries/')) {
                $oldPath = public_path($eatery->image_path);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $imagePath = GoogleDriveHelper::upload($dto->imageFile, 'eateries');
        } elseif ($dto->imageUrl) {
            $imagePath = $this->parseDriveUrl($dto->imageUrl);
        }

        $eatery->update([
            'name' => $dto->name,
            'slug' => Str::slug($dto->name),
            'category_id' => $dto->categoryId,
            'commune_id' => $dto->communeId,
            'address' => $dto->address,
            'phone' => $dto->phone,
            'opening_hours' => $dto->openingHours,
            'latitude' => $dto->latitude,
            'longitude' => $dto->longitude,
            'price_range' => $dto->priceRange,
            'image_path' => $imagePath,
            'is_featured' => $role === 'admin' ? $dto->isFeatured : $eatery->is_featured,
            'description' => $dto->description,
        ]);

        return $eatery;
    }

    /**
     * Xóa quán ăn
     */
    public function destroyEatery(int $id, string $role): void
    {
        if ($role === 'seller') {
            abort(403, 'Chủ quán không được phép tự xóa địa điểm kinh doanh của mình! Vui lòng liên hệ Quản trị viên.');
        }

        $eatery = Eatery::findOrFail($id);
        $eatery->delete();
    }

    /**
     * Thêm món ăn mới vào thực đơn
     */
    public function storeDish(StoreDishDTO $dto, int $userId, string $role): Dish
    {
        $eatery = Eatery::findOrFail($dto->eateryId);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền quản trị thực đơn của cơ sở này!');
        }

        $imagePath = null;
        if ($dto->imageFile) {
            $imagePath = GoogleDriveHelper::upload($dto->imageFile, 'dishes');
        } elseif ($dto->imageUrl) {
            $imagePath = $this->parseDriveUrl($dto->imageUrl);
        }

        return Dish::create([
            'eatery_id' => $dto->eateryId,
            'name' => $dto->name,
            'price' => $dto->price,
            'description' => $dto->description,
            'image_path' => $imagePath,
            'is_signature' => $dto->isSignature,
        ]);
    }

    /**
     * Cập nhật món ăn trong thực đơn
     */
    public function updateDish(int $id, StoreDishDTO $dto, int $userId, string $role): Dish
    {
        $dish = Dish::findOrFail($id);
        $eatery = Eatery::findOrFail($dish->eatery_id);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền quản trị thực đơn của cơ sở này!');
        }

        $imagePath = $dish->image_path;
        if ($dto->imageFile) {
            $imagePath = GoogleDriveHelper::upload($dto->imageFile, 'dishes');
        } elseif ($dto->imageUrl !== null) {
            $imagePath = $dto->imageUrl ? $this->parseDriveUrl($dto->imageUrl) : null;
        }

        $dish->update([
            'name' => $dto->name,
            'price' => $dto->price,
            'description' => $dto->description,
            'image_path' => $imagePath,
            'is_signature' => $dto->isSignature,
        ]);

        return $dish;
    }

    /**
     * Bật/tắt món ăn đặc trưng
     */
    public function toggleSignatureDish(int $id, int $userId, string $role): Dish
    {
        $dish = Dish::findOrFail($id);
        $eatery = Eatery::findOrFail($dish->eatery_id);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền quản trị thực đơn của cơ sở này!');
        }

        $dish->update([
            'is_signature' => !$dish->is_signature
        ]);

        return $dish;
    }

    /**
     * Xóa món ăn khỏi thực đơn
     */
    public function destroyDish(int $id, int $userId, string $role): void
    {
        $dish = Dish::findOrFail($id);
        $eatery = Eatery::findOrFail($dish->eatery_id);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền quản trị thực đơn của cơ sở này!');
        }

        $dish->delete();
    }

    /**
     * Lưu/Cập nhật Giấy phép VSATTP
     */
    public function storeFoodSafetyCertificate(StoreCertificateDTO $dto, int $userId, string $role): FoodSafetyCertificate
    {
        $eatery = Eatery::findOrFail($dto->eateryId);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền cập nhật hồ sơ của cơ sở này!');
        }

        $imagePath = '/uploads/certificates/default-cert.jpg';
        if ($dto->imageFile) {
            $imagePath = GoogleDriveHelper::upload($dto->imageFile, 'certificates');
        } elseif ($dto->imageUrl) {
            $imagePath = $dto->imageUrl;
        }

        return FoodSafetyCertificate::updateOrCreate(
            ['eatery_id' => $dto->eateryId],
            [
                'certificate_number' => $dto->certificateNumber,
                'issued_by' => $dto->issuedBy,
                'issued_at' => $dto->issuedAt,
                'expired_at' => $dto->expiredAt,
                'image_path' => $imagePath,
            ]
        );
    }

    /**
     * Lưu nhật ký kiểm tra hàng ngày
     */
    public function storeDailyFoodLog(StoreDailyFoodLogDTO $dto, int $userId, string $role): DailyFoodLog
    {
        $eatery = Eatery::findOrFail($dto->eateryId);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền quản lý nhật ký của cơ sở này!');
        }

        return DailyFoodLog::create([
            'eatery_id' => $dto->eateryId,
            'log_date' => $dto->logDate,
            'ingredients_origin' => $dto->ingredientsOrigin,
            'storage_condition' => $dto->storageCondition,
            'checker_name' => $dto->checkerName,
        ]);
    }

    /**
     * Xóa nhật ký kiểm tra
     */
    public function destroyDailyFoodLog(int $id, int $userId, string $role): void
    {
        $log = DailyFoodLog::findOrFail($id);
        $eatery = Eatery::findOrFail($log->eatery_id);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền quản lý nhật ký của cơ sở này!');
        }

        $log->delete();
    }

    /**
     * Lưu hợp đồng cung ứng thực phẩm
     */
    public function storeFoodSupplyContract(StoreSupplyContractDTO $dto, int $userId, string $role): FoodSupplyContract
    {
        $eatery = Eatery::findOrFail($dto->eateryId);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền quản lý hợp đồng của cơ sở này!');
        }

        $imagePath = '/uploads/contracts/default-contract.jpg';
        if ($dto->imageFile) {
            $imagePath = GoogleDriveHelper::upload($dto->imageFile, 'contracts');
        } elseif ($dto->imageUrl) {
            $imagePath = $dto->imageUrl;
        }

        return FoodSupplyContract::create([
            'eatery_id' => $dto->eateryId,
            'supplier_name' => $dto->supplierName,
            'items_supplied' => $dto->itemsSupplied,
            'signed_at' => $dto->signedAt,
            'expired_at' => $dto->expiredAt,
            'image_path' => $imagePath,
        ]);
    }

    /**
     * Xóa hợp đồng cung ứng
     */
    public function destroyFoodSupplyContract(int $id, int $userId, string $role): void
    {
        $contract = FoodSupplyContract::findOrFail($id);
        $eatery = Eatery::findOrFail($contract->eatery_id);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền quản lý hợp đồng của cơ sở này!');
        }

        $contract->delete();
    }

    /**
     * Lưu hóa đơn thu mua thực phẩm
     */
    public function storePurchaseInvoice(StorePurchaseInvoiceDTO $dto, int $userId, string $role): PurchaseInvoice
    {
        $eatery = Eatery::findOrFail($dto->eateryId);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền quản lý hóa đơn của cơ sở này!');
        }

        $imagePath = '/uploads/invoices/default-invoice.jpg';
        if ($dto->imageFile) {
            $imagePath = GoogleDriveHelper::upload($dto->imageFile, 'invoices');
        } elseif ($dto->imageUrl) {
            $imagePath = $dto->imageUrl;
        }

        return PurchaseInvoice::create([
            'eatery_id' => $dto->eateryId,
            'supplier_name' => $dto->supplierName,
            'items_summary' => $dto->itemsSummary,
            'invoice_date' => $dto->invoiceDate,
            'image_path' => $imagePath,
        ]);
    }

    /**
     * Xóa hóa đơn thu mua
     */
    public function destroyPurchaseInvoice(int $id, int $userId, string $role): void
    {
        $invoice = PurchaseInvoice::findOrFail($id);
        $eatery = Eatery::findOrFail($invoice->eatery_id);
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền quản lý hóa đơn của cơ sở này!');
        }

        $invoice->delete();
    }

    /**
     * Giải mã và chuẩn hóa URL Google Drive
     */
    private function parseDriveUrl(string $url): string
    {
        if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?id=))([a-zA-Z0-9_-]{25,50})/i', $url, $matches)) {
            return 'https://drive.google.com/uc?export=download&id=' . $matches[1];
        }
        return $url;
    }
}
