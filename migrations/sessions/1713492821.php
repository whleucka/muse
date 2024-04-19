<?php

namespace Nebula\Migrations\sessions;

use Nebula\Framework\Database\{SQL, Schema};
use Nebula\Framework\Database\Interface\Migration;

return new class implements Migration
{
		public function up()
		{
			return Schema::run(fn(SQL $sql) => $sql->migrationFile("/sessions/up.sql"));
		}

		public function down()
		{
			return Schema::run(fn(SQL $sql) => $sql->migrationFile("/sessions/down.sql"));
		}
};
