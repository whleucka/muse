<?php

namespace Nebula\Framework\Middleware;

use Closure;
use Nebula\Framework\Middleware\Interface\Middleware;
use Symfony\Component\HttpFoundation\{Response, Request};

class HTMX implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $middlewares = $request->get("route")?->getMiddleware();
        $response = $next($request);

        if ($middlewares) {
            foreach ($middlewares as $middleware) {
                if (strtolower(substr($middleware, 0, 2)) === "hx") {
                    $opts = explode("=", $middleware);
                    $response->headers->set($opts[0], $opts[1]);
                }
            }
        }

        return $response;
    }
}
