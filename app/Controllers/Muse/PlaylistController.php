<?php

namespace App\Controllers\Muse;

use Nebula\Framework\Controller\Controller;
use StellarRouter\Get;

class PlaylistController extends Controller
{
	#[Get("/playlist", "playlist.index", ["HX-Replace-Url=/playlist"])]
	public function index(): string
	{
		$playlist = session()->get("playlist");

		$content = template("muse/playlist/index.php", [
			"playlist" => $playlist ?? []
		]);

		return $this->render("layout/base.php", ["main" => $content]);
	}

	public function nextTrack()
	{
		 die("wip");
	}

	public function previousTrack()
	{
		 die("wip");
	}
}

