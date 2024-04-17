<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Test extends Module
{
	public function __construct()
	{
		$this->sql_table = "test";
		$this->title = "Test";

		$this->table_columns = [
			"ID" => "id",
			"Name" => "name",
			"Updated At" => "updated_at",
			"Created At" => "created_at",
		];
		parent::__construct('test');
	}
}
