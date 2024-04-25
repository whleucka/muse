<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Modules extends Module
{
	public function __construct(object $config)
	{
		$this->create = $this->delete = $this->edit = true;
		$this->table_columns = [
			"ID" => "id",
			"Title" => "title",
			"Parent" => "(SELECT m.title
				FROM modules m
				WHERE m.id = modules.parent_module_id) as parent",
			"Updated" => "updated_at",
			"Created" => "created_at",
		];
		$this->table_format = [
			"updated_at" => "ago",
			"created_at" => "ago",
		];
		$this->filter_links = [
			"Root" => "parent IS NULL",
			"Children" => "parent IS NOT NULL",
		];
		$this->table_order_by = "parent_module_id";
		$this->table_sort = "ASC";
		$this->form_columns = [
			"Title" => "title",
			"Parent Module ID" => "parent_module_id",
		];
		$this->validation_rules = [
			"title" => ["required", "non_empty_string"],
			"parent_module_id" => ["required", "min|0"]
		];
		parent::__construct($config);
	}
}
