<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Sessions extends Module
{
	public function __construct()
	{
		$user = user();
		$this->sql_table = "sessions";
		$this->title = "Sessions";

		$this->table_columns = [
			"ID" => "id",
			"Request URI" => "request_uri",
			"IP" => "ip",
			"User" => "(SELECT name
				FROM users
				WHERE users.id = sessions.user_id) as session_user",
			"Created" => "created_at",
		];
		$this->table_format = [
			"ip" => "ip",
			"created_at" => "ago"
		];
		$this->search_columns = [
			"request_uri"
		];
		$this->filter_links = [
			"All" => "1=1",
			"Me" => "session_user = '" . $user->name . "'",
			"Others" => "session_user != '" . $user->name . "'",
		];
		$this->table_order_by = "id";
		$this->table_sort = "DESC";
		parent::__construct('sessions');
	}
}
