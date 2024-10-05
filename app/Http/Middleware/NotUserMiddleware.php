<?php

namespace App\Http\Middleware;

use App\Services\User\GetUserService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(GetUserService::find()->isAdmin() || GetUserService::find()->isOwner())
            return $next($request);

        return response(['status' => 'fail' , 'message' => 'you are not allowed to access this page']);
    }
}
