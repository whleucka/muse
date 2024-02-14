<?php

namespace Nebula\Framework\Middleware;

use Closure;
use Nebula\Framework\Middleware\Interface\Middleware;
use Symfony\Component\HttpFoundation\{Response, Request};
use Ramsey\Uuid\Uuid;

class RequestUuid implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $uuid4 = Uuid::uuid4()->toString();
        $request->attributes->add(["request_uuid" => $uuid4]);

        $response = $next($request);

        return $response;
    }
}
