<?php

namespace App\Http\Controllers;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function showLogin(Request $request)
    {
        if (Auth::check() || session()->has('user_id')) {
            return redirect('/');
        }
        if ($request->has('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $dto = LoginDTO::fromRequest($request);

        if ($this->authService->login($dto)) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không khớp với cơ sở dữ liệu của chúng tôi.',
        ])->onlyInput('email');
    }

    public function showRegister(Request $request)
    {
        if (Auth::check() || session()->has('user_id')) {
            return redirect('/');
        }
        if ($request->has('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|string|in:user,seller',
        ], [
            'email.unique' => 'Email này đã tồn tại trên hệ thống!',
            'phone.required' => 'Vui lòng cung cấp số điện thoại liên hệ!',
        ]);

        $dto = RegisterDTO::fromRequest($request);
        $this->authService->register($dto);

        return redirect('/')->with('success', 'Đăng ký tài khoản thành công!');
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        return redirect('/');
    }
}
