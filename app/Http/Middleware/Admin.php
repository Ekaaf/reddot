<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Closure;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if (Auth::check()){
            if (Auth::user()->role_id == 1){
                return $next($request);
            }
            else{
                return response()->json(['error' => 'Unauthorized'], 401);

            }
        }
        else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
