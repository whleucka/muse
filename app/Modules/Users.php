<?php

namespace App\Modules;

use App\Models\User;
use Nebula\Framework\Admin\Module;

class Users extends Module
{
    private User $user;

    public function init(): void
    {
        $this->user = user();
        $this->create = $this->delete = $this->edit = false;
        $this->table_columns = [
            "ID" => "id",
            "UUID" => "uuid",
            "Name" => "name",
            "Email" => "email",
            "Created" => "created_at",
        ];
        $this->table_format = [
            "created_at" => "ago",
        ];
        $this->search_columns = ["uuid", "name", "email"];
        $this->filter_links = [
            "All" => "1=1",
            "Me" => "id = {$this->user->id}",
            "Others" => "id != {$this->user->id}",
        ];
    }
}
