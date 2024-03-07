<?php

namespace App\Controllers\Muse;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Post};

class PlaylistController extends Controller
{
	#[Get("/playlist", "playlist.index", ["HX-Replace-Url=/playlist"])]
	public function index(): string
	{
		$playlist = session()->get("playlist_tracks");

		$content = template("muse/playlist/index.php");

		return $this->render("layout/base.php", ["main" => $content]);
	}

	#[Get("/playlist/load", "playlist.load")]
	public function load(): string
	{
		$playlist = session()->get("playlist_tracks");
		session()->set("playlist_index", 0);

		return template("muse/playlist/playlist.php", ["tracks" => $playlist ?? []]);
	}


	#[Get("/playlist/clear", "playlist.clear")]
	public function clear(): string
	{
		$playlist = session()->delete("playlist_tracks");

		$content = template("muse/playlist/index.php", [
			"playlist" => []
		]);

		return $this->render("layout/base.php", ["main" => $content]);
	}

	#[Get("/playlist/next-track", "playlist.next-track")]
	public function nextTrack(): ?string
	{
		$playlist = session()->get("playlist_tracks");
		$playlist_index = session()->get("playlist_index");
		$playlist_count = count($playlist);
		$next_index = ($playlist_index + 1) % $playlist_count;
		error_log($next_index);

		if (isset($playlist[$next_index])) {
			session()->set("playlist_index", $next_index);
			$track = $playlist[$next_index] ?? null;
			return @json($track->uuid);
		}
		return json('end');
	}

	#[Get("/playlist/prev-track", "playlist.prev-track")]
	public function previousTrack(): ?string
	{
		$playlist = session()->get("playlist_tracks");
		$playlist_index = session()->get("playlist_index");
		$playlist_count = count($playlist);
		$prev_index = ($playlist_index - 1 + $playlist_count)  % $playlist_count;
		error_log($prev_index);

		if (isset($playlist[$prev_index])) {
			session()->set("playlist_index", $prev_index);
			$track = $playlist[$prev_index] ?? null;
			return @json($track->uuid);
		}
		return json('end');
	}
}

