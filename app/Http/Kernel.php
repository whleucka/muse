<?php

namespace App\Http;

use Nebula\Framework\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Application middleware stack (order FILO)
     * @var array $middleware application middleware
     */
    protected array $middleware = [
        \Nebula\Framework\Middleware\Authentication::class,
        \Nebula\Framework\Middleware\CSRF::class,
        \Nebula\Framework\Middleware\EncryptCookies::class,
        \Nebula\Framework\Middleware\RequestUuid::class,
        \Nebula\Framework\Middleware\APIResponse::class,
    ];
}
