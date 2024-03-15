<?php

namespace Nebula\Framework\System\Interface;

interface Kernel
{
    public function main(): void;
    public function response(): void;
}
