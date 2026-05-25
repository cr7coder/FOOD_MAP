<?php

namespace App\Services;

use App\Models\User;
use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Thực hiện đăng nhập
     */
    public function login(LoginDTO $dto): bool
    {
        if (Auth::attempt($dto->toArray())) {
            request()->session()->regenerate();
            
            $user = Auth::user();
            
            // Lưu thông tin người dùng vào Session
            session([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Thực hiện đăng ký tài khoản
     */
    public function register(RegisterDTO $dto): User
    {
        $role = $dto->role ?: 'user';
        if ($role === 'admin') {
            $role = 'user'; // Bảo mật: Không cho phép tự ý đăng ký làm Admin
        }

        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'role' => $role,
            'phone' => $dto->phone,
            'status' => 'active',
            'avatar' => '🧑',
        ]);

        Auth::login($user);

        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
        ]);

        return $user;
    }

    /**
     * Thực hiện đăng xuất
     */
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        session()->forget(['user_id', 'user_name', 'user_role']);
    }
}
