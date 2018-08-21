<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;

class CheckInstall
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
        # Comment start
        if(!file_exists(base_path('/.env')) && !Request::is('install')) {
            return redirect(url('/install'));
        }
        # Comment end

        return $next($request);
    }
}
