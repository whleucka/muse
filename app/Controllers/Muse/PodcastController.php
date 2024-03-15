<?php

namespace App\Controllers\Muse;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group};

#[Group(prefix: "/podcast")]
class PodcastController extends Controller
{
	#[Get("/", "podcast.index", ["HX-Replace-Url=/podcast"])]
	public function index(): string
	{
		$content = template("muse/podcast/index.php");

		return $this->render("layout/base.php", ["main" => $content]);
	}
}
