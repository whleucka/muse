<?php

namespace Nebula\Framework\Middleware;

use Closure;
use Nebula\Framework\Middleware\Interface\Middleware;
use Symfony\Component\HttpFoundation\{Response, Request};

class EncryptCookies implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
		// Application key is required
		$app_key = config("security.app_key");
		if (!$app_key) return new Response("Application key is not set", 500);

		// All cookies except PHPSESSID will be encrypted/decrypted
		$this->decrypt($request);

        $response = $next($request);

		$this->encrypt($request);

        return $response;
    }

	private function encrypt(Request $request): void
	{
        foreach ($request->cookies as $key => $value) {
			if (!isEncrypted($value) && $key !== "PHPSESSID") {
				$encryptedValue = encrypt($value);
				setcookie($key, $encryptedValue, time() + (86400 * 30), "/", "", false, true);
				$request->cookies->set($key, $encryptedValue);
			}
        }
    }

	private function decrypt(Request $request): void
	{
        foreach ($request->cookies as $key => $value) {
			if (isEncrypted($value) && $key !== "PHPSESSID") {
				$decryptedValue = decrypt($value);
				$request->cookies->set($key, $decryptedValue);
			}
        }
    }
}
