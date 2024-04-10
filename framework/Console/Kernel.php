<?php

namespace Nebula\Framework\Console;

use Nebula\Framework\System\Interface\Kernel as NebulaInterface;
use Nebula\Framework\Traits\Singleton;
use Nebula\Framework\System\Kernel as SystemKernel;

class Kernel extends SystemKernel implements NebulaInterface
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
}
