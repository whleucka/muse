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
			"Path" => "path",
			"Class" => "class_name",
			"SQL Table" => "sql_table",
			"Primary Key" => "primary_key",
			"Item Order" => "item_order",
			"Max Permission Level" => "max_permission_level",
			"Parent Module ID" => "parent_module_id",

		];
		$this->validation_rules = [
			"title" => ["required", "non_empty_string"],
			"path" => ["required", "non_empty_string"],
			"class_name" => ["required", "non_empty_string"],
			"sql_table" => ["required", "non_empty_string"],
			"primary_key" => ["required", "non_empty_string"],
			"item_order" => ["min|0"],
			"max_permission_level" => ["min|0"],
			"parent_module_id" => ["min|0"],
		];
		parent::__construct($config);
	}

	protected function hasRowDelete(object $row): bool
	{
		if ($row->title === "Modules") {
			return false;
		}

		return parent::hasRowDelete($row);
	}

	protected function hasRowEdit(object $row): bool
	{
		if ($row->title === "Modules") {
			return false;
		}

		return parent::hasRowEdit($row);
	}
}
