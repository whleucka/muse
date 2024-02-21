<?php

namespace Nebula\Framework\Console;

use Nebula\Framework\System\Interface\Kernel as NebulaKernel;
use Nebula\Framework\Traits\Singleton;
use Dotenv\Dotenv;

class Kernel implements NebulaKernel
{
	use Singleton;

	public function main(): void
	{
		$this->bootstrap();
	}

	public function response(): void
	{
	}

	protected function bootstrap(): void
	{
		$this->environment();
	}

    /**
     * Load environment variables
     * @param string $path path to .env
     */
    protected function environment(): void
    {
        $path = config("path.root");
        if (!file_exists($path)) {
            error_log("warning: your .env path: '$path' doesn't exist");
        }
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->safeLoad();
    }
}
