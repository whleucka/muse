<?php

namespace Nebula\Framework\Middleware\Interface;

use Symfony\Component\HttpFoundation\{Response, Request};

use Closure;

interface Middleware
{
    /**
     * @param Closure(): Response $next
     */
    public function handle(Request $request, Closure $next): Response;
}
