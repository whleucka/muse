<?php

namespace Nebula\Framework\System\Interfaces;

interface Kernel
{
	public function main(): void;
	public function response(): void;
}
