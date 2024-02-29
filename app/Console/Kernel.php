<?php

namespace App\Console;

use Nebula\Framework\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    private Adapter $adapter;

    protected function bootstrap(): void
    {
        parent::bootstrap();
        $this->adapter = new Adapter();
        $this->adapter->run();
    }
}
