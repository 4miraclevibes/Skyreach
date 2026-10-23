<?php

namespace App\Http\Middleware;

use App\Models\Contract;
use Closure;
use Illuminate\Support\Facades\View;

class ShareAppData
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        View::share('pulseLatest', Contract::query()->max('updated_at'));
        View::share('pulseCount', Contract::query()->count());

        return $next($request);
    }
}
