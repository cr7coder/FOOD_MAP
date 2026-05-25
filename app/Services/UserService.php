<?php

namespace App\Services;

use App\Models\User;
use App\DTOs\StoreUserDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Tìm kiếm và phân trang tài khoản
     */
    public function searchAndPaginate(Request $request): LengthAwarePaginator
    {
        $query = User::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
    }

    /**
     * Lấy thống kê số lượng User theo vai trò
     */
    public function getRoleStats(): array
    {
        return [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'seller' => User::where('role', 'seller')->count(),
            'user' => User::where('role', 'user')->count(),
        ];
    }

    /**
     * Tạo tài khoản người dùng mới
     */
    public function storeUser(StoreUserDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'role' => $dto->role,
            'avatar' => $dto->avatar ?: '🧑',
            'phone' => $dto->phone,
            'status' => 'active',
        ]);
    }

    /**
     * Cập nhật tài khoản người dùng
     */
    public function updateUser(int $id, StoreUserDTO $dto): User
    {
        $user = User::findOrFail($id);

        $data = [
            'name' => $dto->name,
            'email' => $dto->email,
            'role' => $dto->role,
            'avatar' => $dto->avatar ?: '🧑',
            'phone' => $dto->phone,
            'status' => $dto->status ?: $user->status,
        ];

        if (!empty($dto->password)) {
            $data['password'] = Hash::make($dto->password);
        }

        $user->update($data);

        return $user;
    }

    /**
     * Xóa tài khoản người dùng
     */
    public function destroyUser(int $id, int $currentUserId): array
    {
        $user = User::findOrFail($id);

        if ($user->id === $currentUserId) {
            return [
                'status' => 'error',
                'message' => 'Bạn không được phép tự xóa tài khoản của chính mình!'
            ];
        }

        $user->delete();

        return [
            'status' => 'success',
            'message' => 'Đã xóa tài khoản người dùng khỏi hệ thống!'
        ];
    }

    /**
     * Kích hoạt hoặc vô hiệu hóa tài khoản người dùng
     */
    public function toggleUserStatus(int $id, int $currentUserId): array
    {
        $user = User::findOrFail($id);

        if ($user->id === $currentUserId) {
            return [
                'status' => 'error',
                'message' => 'Bạn không thể tự vô hiệu hóa tài khoản của chính mình!'
            ];
        }

        $user->status = $user->status === 'active' ? 'disabled' : 'active';
        $user->save();

        $message = $user->status === 'active' ? 'Kích hoạt tài khoản thành công!' : 'Đã vô hiệu hóa tài khoản thành công!';

        return [
            'status' => 'success',
            'message' => $message
        ];
    }
}
