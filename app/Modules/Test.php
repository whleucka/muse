<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Test extends Module
{
	public function __construct(object $config)
	{
		$this->create = $this->delete = $this->edit = true;
		$this->table_columns = [
			"ID" => "id",
			"Name" => "name",
			"Number" => "number",
			"Updated" => "updated_at",
			"Created" => "created_at",
		];
		$this->table_format = [
			"updated_at" => "ago",
			"created_at" => "ago",
		];
		$this->filter_links = [
			"All" => "1=1",
			"Number" => "number IS NOT NULL"
		];
		$this->search_columns = [
			"name",
			"number",
		];
		$this->form_columns = [
			"Name" => "name",
			"Number" => "number",
		];
		$this->validation_rules = [
			"name" => ["required", "non_empty_string"],
			"number" => ["min|0"],
		];
		parent::__construct($config);
	}
}
