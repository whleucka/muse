<?php

namespace Nebula\Migrations\track_meta;

use Nebula\Framework\Database\{SQL, Schema};
use Nebula\Framework\Database\Interface\Migration;

return new class implements Migration
{
		public function up()
		{
			return Schema::run(fn(SQL $sql) => $sql->migrationFile("/track_meta/table/up.sql"));
		}

		public function down()
		{
			return Schema::run(fn(SQL $sql) => $sql->migrationFile("/track_meta/table/down.sql"));
		}
};
