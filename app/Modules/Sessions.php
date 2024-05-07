<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Sessions extends Module
{
    public function init(): void
    {
        $user = user();
        $this->delete = false;
        $this->table_columns = [
            "ID" => "id",
            "Request URI" => "request_uri",
            "User" => "(SELECT name
				FROM users
				WHERE users.id = sessions.user_id) as session_user",
            "IP" => "INET_NTOA(ip) as ip",
            "Created" => "created_at",
        ];
        $this->table_format = [
            "created_at" => "ago",
        ];
        $this->search_columns = ["request_uri", "session_user", "ip"];
        $this->filter_links = [
            "All" => "1=1",
            "Me" => "session_user = '" . $user->name . "'",
            "Others" => "session_user != '" . $user->name . "'",
        ];
        $this->table_order_by = "id";
        $this->table_sort = "DESC";
    }
}
