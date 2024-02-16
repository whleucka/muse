<?php

namespace Nebula\Framework\Database;

use Closure;

class Schema
{
	/**
	* Run schema query
 	*/
	public static function run(Closure $callback): string
	{
		$sql = new SQL;
		$callback($sql);
		return $sql->query();
	}
}
