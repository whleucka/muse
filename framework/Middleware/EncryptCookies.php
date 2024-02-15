<?php

namespace Nebula\Framework\Middleware;

use Closure;
use Nebula\Framework\Middleware\Interface\Middleware;
use Symfony\Component\HttpFoundation\{Response, Request};

class EncryptCookies implements Middleware
{
	private string $key;

    public function handle(Request $request, Closure $next): Response
    {
		$this->key = config("security.app_key");
		if (!$this->key) return new Response("Application key is not set", 500);

		$this->decrypt($request);

        $response = $next($request);

		$this->encrypt($request);

        return $response;
    }

	private function encrypt(): void
	{
        foreach ($_COOKIE as $key => $value) {
			if (!$this->isEncrypted($value)) {
				$encryptedValue = openssl_encrypt($value, 'AES-256-CBC', $this->key, 0, substr($this->key, 0, 16));
				// Add a marker to indicate that the cookie is encrypted
                $encryptedValue .= '--2113';
				setcookie($key, $encryptedValue, time() + (86400 * 30), "/", "", false, true);
				$_COOKIE[$key] = $encryptedValue;
			}
        }
    }

	private function decrypt(): void
	{
        foreach ($_COOKIE as $key => $value) {
			if ($this->isEncrypted($value)) {
				$decryptedValue = openssl_decrypt(str_replace('--2113', '', $value), 'AES-256-CBC', $this->key, 0, substr($this->key, 0, 16));
				$_COOKIE[$key] = $decryptedValue;
			}
        }
    }

	private function isEncrypted(string $value): bool
	{
		return strpos($value, '--2113') !== false;
	}
}
