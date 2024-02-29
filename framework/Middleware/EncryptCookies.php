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
        $app_key = config("application.key");
        if (!$app_key) {
            return new Response("Application key is not set", 500);
        }

        // All cookies except PHPSESSID will be encrypted/decrypted
        $this->decrypt($request);

        $response = $next($request);

        $this->encrypt($request);

        return $response;
    }

    private function encrypt(Request $request): void
    {
        foreach ($request->cookies as $key => $value) {
            if (!$this->isEncrypted($value) && $key !== "PHPSESSID") {
                $encryptedValue = $this->encryptValue($value);
                setcookie(
                    $key,
                    $encryptedValue,
                    time() + 86400 * 30,
                    "/",
                    "",
                    false,
                    true
                );
            }
        }
    }

    private function decrypt(Request $request): void
    {
        foreach ($request->cookies as $key => $value) {
            if ($this->isEncrypted($value) && $key !== "PHPSESSID") {
                $decryptedValue = $this->decryptValue($value);
                $_COOKIE[$key] = $decryptedValue;
                $request->cookies->set($key, $decryptedValue);
            }
        }
    }

    function isEncrypted(mixed $value): bool
    {
        return strpos($value, "|crypt|") !== false;
    }

    function encryptValue(mixed $value)
    {
        $app_key = config("application.key");
        $encrypted = openssl_encrypt(
            $value,
            "AES-256-CBC",
            $app_key,
            0,
            substr($app_key, 0, 16)
        );
        // Add a marker to indicate that the cookie is encrypted
        $encrypted .= "|crypt|";
        return $encrypted;
    }

    function decryptValue(mixed $value)
    {
        $app_key = config("application.key");
        return openssl_decrypt(
            str_replace("|crypt|", "", $value),
            "AES-256-CBC",
            $app_key,
            0,
            substr($app_key, 0, 16)
        );
    }
}
