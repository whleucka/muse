<?php

namespace Nebula\Framework\Middleware\Interface;

use Symfony\Component\HttpFoundation\{Response, Request};

use Closure;

interface Middleware
{
	public function handle(Request $request, Closure $next): Response;
}
