<?php

namespace App\Controllers\Muse;

use Nebula\Framework\Controller\Controller;
use StellarRouter\Get;

class MuseController extends Controller
{
	#[Get("/colours", "muse.colors")]
	public function colours_red(): string
	{
		return template("components/colours.php", [
			"colour" => sprintf("rgb(%s,%s,%s)", rand(0,255), rand(0,255), rand(0,255))
		]);
	}
}

