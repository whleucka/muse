<?php

namespace Nebula\Framework\Console;

use Nebula\Framework\System\Interface\Kernel as NebulaKernel;
use Nebula\Framework\Traits\Singleton;

class Kernel implements NebulaKernel
{
	use Singleton;

	public function main(): void
	{
		$this->bootstrap();
	}

	public function response(): void
	{
		printf("wip!!!!\n");
	}

	protected function bootstrap(): void
	{
	}
}
