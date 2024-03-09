<?php

namespace App\Controllers\Muse;

use App\Models\Track;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group, Post};

#[Group(prefix: "/playlist")]
class PlaylistController extends Controller
{
	#[Get("/", "playlist.index", ["HX-Replace-Url=/playlist"])]
	public function index(): string
	{
		$content = template("muse/playlist/index.php");

		return $this->render("layout/base.php", ["main" => $content]);
	}

	// NOTE: formatting is important for hx-location header
	#[Get("/set", "playlist.set", ['HX-Location={"path": "/playlist", "target": "#main", "select": "#main", "swap": "outerHTML"}'])]
	public function playlist(): void
	{
		$term = session()->get("term");
		if ($term) {
			$tracks = Track::search($term);
			if ($tracks) {
				// This might have a size limitation
				session()->set("playlist_tracks", $tracks);
				// Forget the search term
				session()->delete("term");
			}
		}
	}

	#[Post("/index", "playlist.set-index", ["api"])]
	public function setIndex(): void
	{
		$data = $this->validateRequest([
			"index" => ["required"],
		]);
		if ($data) {
			session()->set("playlist_index", $data["index"]);
		}
	}

	#[Get("/load", "playlist.load")]
	public function load(): string
	{
		$playlist = session()->get("playlist_tracks");
		session()->set("playlist_index", 0);

		return template("muse/playlist/playlist.php", ["tracks" => $playlist ?? []]);
	}


	#[Get("/clear", "playlist.clear")]
	public function clear(): string
	{
		$playlist = session()->delete("playlist_tracks");

		$content = template("muse/playlist/index.php", [
			"playlist" => []
		]);

		return $this->render("layout/base.php", ["main" => $content]);
	}

	#[Get("/next-track", "playlist.next-track")]
	public function nextTrack(): ?string
	{
		$playlist = session()->get("playlist_tracks");
		$playlist_index = session()->get("playlist_index");
		if ($playlist) {
			$playlist_count = count($playlist);
			$next_index = ($playlist_index + 1) % $playlist_count;
			if (isset($playlist[$next_index])) {
				session()->set("playlist_index", $next_index);
				$track = $playlist[$next_index] ?? null;
				return @json($track->uuid);
			}
		}
		return null;
	}

	#[Get("/prev-track", "playlist.prev-track")]
	public function previousTrack(): ?string
	{
		$playlist = session()->get("playlist_tracks");
		$playlist_index = session()->get("playlist_index");
		if ($playlist) {
			$playlist_count = count($playlist);
			$prev_index = ($playlist_index - 1 + $playlist_count)  % $playlist_count;
			if (isset($playlist[$prev_index])) {
				session()->set("playlist_index", $prev_index);
				$track = $playlist[$prev_index] ?? null;
				return @json($track->uuid);
			}
		}
		return null;
	}
}

