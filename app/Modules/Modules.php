<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Modules extends Module
{
	public function __construct()
	{
		$this->sql_table = "modules";
		$this->title = "Modules";

		$this->table_columns = [
			"ID" => "id",
			"Title" => "title",
			"Path" => "path",
			"Parent Module" => "(SELECT m.title FROM modules m WHERE m.id = modules.parent_module_id) as parent_module",
			"Max Permission Level" => "(SELECT name FROM user_types WHERE permission_level = max_permission_level) as max_permission_level",
			"Update" => "updated_at",
			"Created" => "created_at",
		];
		$this->table_format = [
			"updated_at" => "ago",
			"created_at" => "ago",
		];
		parent::__construct('modules');
	}
}
