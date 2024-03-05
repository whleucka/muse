<?php

namespace App\Controllers\Muse;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Post};

class PlaylistController extends Controller
{
	#[Get("/playlist", "playlist.index", ["HX-Replace-Url=/playlist"])]
	public function index(): string
	{
		$playlist = session()->get("playlist");

		$content = template("muse/playlist/index.php");

		return $this->render("layout/base.php", ["main" => $content]);
	}

	#[Get("/playlist/load", "playlist.load")]
	public function load(): string
	{
		$playlist = session()->get("playlist");

		return template("muse/playlist/playlist.php", ["tracks" => $playlist ?? []]);
	}


	#[Get("/playlist/clear", "playlist.clear")]
	public function clear(): string
	{
		$playlist = session()->delete("playlist");

		$content = template("muse/playlist/index.php", [
			"playlist" => []
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

