<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Lưu thông tin người dùng vào Session tiện dụng
            session([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
            ]);

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
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|string|in:user,seller',
        ]);

        $role = $request->input('role', 'user');
        if ($role === 'admin') {
            $role = 'user'; // Bảo mật: Không cho phép tự ý đăng ký làm Admin
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        Auth::login($user);

        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
        ]);

        return redirect('/')->with('success', 'Đăng ký tài khoản thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        session()->forget(['user_id', 'user_name', 'user_role']);

        return redirect('/');
    }
}
