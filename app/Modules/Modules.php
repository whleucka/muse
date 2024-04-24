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
			"Parent" => "(SELECT m.title FROM modules m WHERE m.id = modules.parent_module_id) as parent",
			"Updated" => "updated_at",
			"Created" => "created_at",
		];
		$this->table_format = [
			"updated_at" => "ago",
			"created_at" => "ago",
		];
		$this->filter_links = [
			"All" => "1=1",
			"Parent" => "parent IS NULL",
			"Children" => "parent IS NOT NULL",
		];
		$this->table_order_by = "parent_module_id";
		$this->table_sort = "ASC";
		parent::__construct($config);
	}
}
