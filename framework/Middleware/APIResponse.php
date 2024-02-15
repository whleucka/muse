<?php

namespace Nebula\Framework\Middleware;

use Closure;
use Nebula\Framework\Middleware\Interface\Middleware;
use Symfony\Component\HttpFoundation\{JsonResponse, Response, Request};

class APIResponse implements Middleware
{
	public function handle(Request $request, Closure $next): Response
	{
		$middleware = $request->get("route")->getMiddleware();

		$response = $next($request);

		$code = $response->getStatusCode();
		$headers = [
			...$response->headers,
			"Content-Type" => "application/json; charset=utf-8",
		];
		$data = [
			"success" => $code === 200,
			"id" => $request->get("request_uuid"),
			"status" => $code,
			"ts" => time(),
		];

		if ($code === 200) {
			$data["data"] = $response->getContent();
		} else {
			$data["error"] = $response->getContent();
		}

		arsort($data);
		return in_array("api", $middleware)
			? new JsonResponse($data, $code, $headers)
			: $response;
	}
}
