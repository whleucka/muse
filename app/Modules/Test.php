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
			"Number" => "number",
			"Updated At" => "updated_at",
			"Created At" => "created_at",
		];
		$this->filter_links = [
			"All" => "1=1",
			"Number" => "number IS NOT NULL"
		];
		$this->search_columns = [
			"name",
			"number",
		];
		parent::__construct('test');
	}
}
