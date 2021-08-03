<?php

namespace Sofiakb\Lumen\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class AccessMiddleware
 * @package Ssf\LumenMiddleware
 * @author Sofiane Akbly <sofiane.akbly@gmail.com>
 */
class AccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        try {
            Log::channel('access')->info($this->message($request, $response));
        } catch (\Throwable $e) {
            Log::info($this->message($request, $response));
        }
        return $response;
    }

    /**
     * @param Request $request
     * @return string
     */
    private function message(Request $request, $response)
    {
        $ip = \Illuminate\Support\Facades\Request::ip();
        $browser = $request->header('user-agent') ?? $request->header('User-Agent');

        $message = "[{$response->status()}] $ip -- $browser" . PHP_EOL .
            "\t * Status code : {$response->status()}" . PHP_EOL .
            "\t * Method : {$request->getMethod()}" . PHP_EOL .
            "\t * IP : {$ip}" . PHP_EOL .
            "\t * Browser : {$browser}" . PHP_EOL;

        return $message;//. (($user = Auth::user()) ? " -- $user->email" : '');
    }
}
