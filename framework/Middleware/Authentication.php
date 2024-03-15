<?php

namespace Nebula\Framework\Middleware;

use App\Models\User;
use Closure;
use Nebula\Framework\Auth\Auth;
use Nebula\Framework\Middleware\Interface\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Authentication implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $middleware = $request->get("route")?->getMiddleware();

        if ($middleware && in_array("auth", $middleware)) {
            if (!$this->userAuth($request)) {
                Auth::redirectSignIn();
            }
        }

        $response = $next($request);

        return $response;
    }

    private function userAuth(Request $request): bool
    {
        $id = session()->get("user_id");
        $uuid = $request->cookies->get("user_uuid");

        // Cookie
        if ($uuid) {
            $user = User::findByAttribute("uuid", $uuid);
            if ($user) {
                return true;
            }
        }
        // Session
        if ($id) {
            $user = User::find($id);
            if ($user) {
                return true;
            }
        }

        return false;
    }
}
