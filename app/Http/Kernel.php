<?php

namespace App\Http;

use Nebula\Framework\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
	protected array $middleware = [
		\Nebula\Framework\Middleware\RequestUuid::class
	];
}
