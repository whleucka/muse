<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Test extends Module
{
	public function __construct(object $config)
	{
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
		parent::__construct($config);
	}
}
