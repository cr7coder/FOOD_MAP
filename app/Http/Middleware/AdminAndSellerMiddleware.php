<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAndSellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() && !session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        if (auth()->check() && !session()->has('user_id')) {
            $user = auth()->user();
            session([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
            ]);
        }

        $role = session('user_role');
        if ($role !== 'admin' && $role !== 'seller') {
            abort(403, 'Bạn không có quyền truy cập trang quản lý này!');
        }

        return $next($request);
    }
}
