<?php

namespace App\Http\Middleware;

use Closure;

class CheckUiRequest
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
        abort_unless(isset($_SERVER['HTTP_ORIGIN']), 403);

        abort_unless($_SERVER['HTTP_ORIGIN'] === config('app.ui_url'), 403);

        return $next($request);
    }
}
