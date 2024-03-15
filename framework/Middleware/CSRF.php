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
        $middleware = $request->get("route")?->getMiddleware();
        $this->token();

        if ($middleware && !in_array("api", $middleware)) {
            if (!$this->validate($request)) {
                return new Response("Invalid request", 403);
            }
        }

        $response = $next($request);

        return $response;
    }

    private function token(): void
    {
        $token = session()->get("csrf_token");
        $token_ts = session()->get("csrf_token_ts");

        if (
            is_null($token) ||
            is_null($token_ts) ||
            $token_ts + 3600 < time()
        ) {
            $token = $this->generateToken();
            session()->set("csrf_token", $token);
            session()->set("csrf_token_ts", time());
        }
    }

    /**
     * Get a token string
     */
    function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function validate(Request $request): bool
    {
        $request_method = $request->getMethod();
        if (in_array($request_method, ["GET", "HEAD", "OPTIONS"])) {
            return true;
        }

        $session_token = session()->get("csrf_token");
        $token = $request->get("csrf_token");

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
