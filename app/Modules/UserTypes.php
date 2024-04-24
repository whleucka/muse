<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class UserTypes extends Module
{
	public function __construct(object $config)
	{
		$this->sql_table = "user_types";
		$this->title = "User Types";

		$this->table_columns = [
			"ID" => "id",
			"Name" => "name",
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

