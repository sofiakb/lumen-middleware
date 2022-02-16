<?php

namespace Sofiakb\Lumen\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sofiakb\Utils\Response;
use Sofiakb\Utils\Result\Result;

class CorsMiddleware
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
        if (($origin = $this->authorized($request)) === false)
            return Response::unknown(Result::forbidden());

        $headers = $this->headers($origin);

        if ($request->isMethod('OPTIONS')) {
            return response()->json(['method' => "OPTIONS"], 200, $headers);
        }

        $response = $next($request);
        if ($response)
            foreach ($headers as $key => $value) {
                if (method_exists($response, 'header'))
                    $response->header($key, $value);
                elseif ($response->headers)
                    $response->headers->set($key, $value);
            }

        return $response;
    }

    private function authorized(Request $request)
    {
        return ($urls = config('cors.urls'))
            ? (collect($urls)->contains(($origin = $request->headers->get('origin'))) ? $origin : false)
            : '*';
    }

    private function headers($origin)
    {
        return [
            'Access-Control-Allow-Origin'      => $origin,
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With, X-API-KEY, Content-Type, Origin'
        ];
    }
}
