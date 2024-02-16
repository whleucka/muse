<?php

namespace Nebula\Framework\Database\Interfaces;

interface Migration
{
	public function up();
	public function down();
}
