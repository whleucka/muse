<?php

namespace App\Controllers\Home;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group};

#[Group(middleware: ["auth"])]
class HomeController extends Controller
{
	#[Get("/home", "home.index")]
	public function index(): string
	{
		$content = template("home/index.php");

		return template("layout/base.php", ["main" => $content]);
	}
}
