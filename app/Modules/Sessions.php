<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Sessions extends Module
{
	public function __construct()
	{
		$this->sql_table = "sessions";
		$this->title = "Sessions";

		$this->table_columns = [
			"ID" => "id",
			"Request URI" => "request_uri",
			"IP" => "ip",
			"User" => "user_id",
			"Created" => "created_at",
		];
		$this->search_columns = [
			"name",
			"number",
		];
		$this->table_order_by = "id";
		$this->table_sort = "DESC";
		parent::__construct('sessions');
	}

	protected function tableValueOverride(&$row): void
	{
		// Convert INT to IP
		$row->ip = long2ip($row->ip);
	}
}
