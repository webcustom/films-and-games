<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Пользователь аутентифицирован
            if($request->user()->usertype === 'admin'){
                // Пользователь является администратором
                return $next($request);
            }else {
                // Пользователь не является администратором
                return redirect()->route('home');
            }

        } else {
            // Пользователь не аутентифицирован
            return redirect()->route('login.index');
        }
    }
}
