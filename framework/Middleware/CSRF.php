<?php

namespace Nebula\Framework\Middleware;

use Closure;
use Nebula\Framework\Middleware\Interface\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CSRF implements Middleware
{
	public function handle(Request $request, Closure $next): Response
	{
		$token = $this->token();

		if (!$this->validate($request, $token)) {
			return new Response("Invalid request", 403);
		}

		$response = $next($request);

		return $response;
	}

	private function token(): string
	{
		$token = session()->get("csrf_token");
		$token_ts = session()->get("csrf_token_ts");

		if (is_null($token) || is_null($token_ts) || $token_ts + 3600 < time()) {
			$token = generateToken();
			session()->set("csrf_token", $token);
			session()->set("csrf_token_ts", time());
		}

		return $token;
	}


	private function validate(Request $request, string $token): bool
	{
		$request_method = $request->getMethod();
		if (in_array($request_method, ["GET", "HEAD", "OPTIONS"])) {
			return true;
		}

		$session_token = session()->get("csrf_token");

		if (
			!is_null($session_token) &&
			!is_null($token) &&
			hash_equals($session_token, $token)
		) {
			return true;
		}

		return false;
	}
}
