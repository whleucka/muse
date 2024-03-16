<?php

namespace Nebula\Framework\System\Interface;

use StellarRouter\Router;

interface Kernel
{
    public function main(): void;
    public function response(): void;
    public function router(): Router;
}
