<?php

namespace Nebula\Migrations\Users;

use Nebula\Framework\Database\{SQL, Schema};
use Nebula\Framework\Database\Interfaces\Migration;

return new class implements Migration
{
		public function up()
		{
			$migration_path = config("path.migrations");
			return Schema::run(fn(SQL $sql) => $sql->file($migration_path . "/users/up.sql"));
		}

		public function down()
		{
			$migration_path = config("path.migrations");
			return Schema::run(fn(SQL $sql) => $sql->file($migration_path . "/users/down.sql"));
		}
};
