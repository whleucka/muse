<?php

namespace App\Modules;

use App\Models\User;
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
			"request_uri"
		];
		$this->table_order_by = "id";
		$this->table_sort = "DESC";
		parent::__construct('sessions');
	}

	protected function tableValueOverride(&$row): void
	{
		// Convert INT to IP
		$row->ip = long2ip($row->ip);
		// Lookup user
		$row->user_id = User::find($row->user_id)->name;
	}
}
