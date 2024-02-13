<?php

namespace App\Controllers;

use Nebula\Framework\Controller\Controller;
use StellarRouter\Get;

class BasicController extends Controller
{
	#[Get("/", "basic.index")]
	public function index(): string
	{
		return template("basic/index.php", [
			"message" => "Hello, world! " . time()
		]);
	}
}
