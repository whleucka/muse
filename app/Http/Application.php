<?php

namespace App\Http;

class Application
{
    public function __construct(private Kernel $kernel)
    {
    }

    /**
     * Invoke Kernel main method
     */
    public function run(): void
    {
        $response = $this->kernel->main();
        $response->send();
    }
}
