<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class UserTypes extends Module
{
    public function init(): void
    {
        $this->create = $this->edit = $this->delete = user()->type()->permission_level == 0;
        $this->table_columns = [
            "ID" => "id",
            "Name" => "name",
            "Permission Level" => "permission_level",
            "Updated" => "updated_at",
            "Created" => "created_at",
        ];
        $this->table_format = [
            "updated_at" => "ago",
            "created_at" => "ago",
        ];
        $this->form_columns = [
            "Name" => "name",
            "Permission Level" => "permission_level"
        ];
        $this->validation_rules = [
            "name" => ["required", "non_empty_string"],
            "permission_level" => ["min|0"],
        ];
    }
}
