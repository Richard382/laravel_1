<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ... $roles)
    {
        if ( ! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

//        if ($user->isAdmin())
//            return $next($request);

        foreach ($roles as $role)
        {
            if ($user->hasRole($role))
            {
                return $next($request);
            }
        }

        return abort(404);
    }
}
