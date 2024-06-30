<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $routePrefix = request()->route()->getPrefix();

        $routePrefix = str_replace('/','',$routePrefix);


        //        if ($routePrefix !== null) {
//
//        } else {
//        }

        // Now you have the route prefix, you can use it as needed
        dd($routePrefix);


        return $next($request);
    }
}
