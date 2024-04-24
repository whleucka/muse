<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Modules extends Module
{
	public function __construct(object $config)
	{
		$this->table_columns = [
			"ID" => "id",
			"Title" => "title",
			"Updated" => "updated_at",
			"Created" => "created_at",
		];
		$this->table_format = [
			"updated_at" => "ago",
			"created_at" => "ago",
		];
		parent::__construct($config);
	}
}
