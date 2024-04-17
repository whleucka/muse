<?php

declare(strict_types=1);

use Nebula\Framework\Admin\Module;
use PHPUnit\Framework\TestCase;

final class ModuleTest extends TestCase
{
	public function testModulePath(): void
	{
		$module = new Module("foo");
		$this->assertSame("foo", $module->getPath());
	}
}
