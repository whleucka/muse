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

	// We redirect to the playlist here
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
				// Alwyays start on index 0,
				// the first track in the playlist
				session()->set("playlist_index", 0);
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

	#[Get("/uuid", "playlist.uuid", ["api"])]
	public function getUuid(): ?string
	{
		$playlist = session()->get("playlist_tracks");
		$playlist_index = session()->get("playlist_index");
		return isset($playlist[$playlist_index]) ? $playlist[$playlist_index]->uuid : null;
	}

	#[Get("/load", "playlist.load")]
	public function load(): string
	{
		$playlist = session()->get("playlist_tracks");
		$playlist_index = session()->get("playlist_index");

		return template("muse/playlist/playlist.php", ["tracks" => $playlist ?? []]);
	}


	#[Get("/clear", "playlist.clear")]
	public function clear(): string
	{
		session()->delete("playlist_tracks");
		$content = template("muse/playlist/index.php");

		return $this->render("layout/base.php", ["main" => $content]);
	}

	private function nextIndex(array $playlist, int $current_index): int
	{
		$shuffle = session()->get("shuffle") === true;
		$repeat = session()->get("repeat") === true; // wip
		$playlist_count = count($playlist);

		if ($shuffle) {
			// wip
		} else {
			return (intval($current_index) + 1) % $playlist_count;
		}
	}

	private function prevIndex(array $playlist, int $current_index): int
	{
		$shuffle = session()->get("shuffle") === true;
		$repeat = session()->get("repeat") === true; // wip
		$playlist_count = count($playlist);

		if ($shuffle) {
			// wip
		} else {
			return intval($current_index - 1 + $playlist_count) % $playlist_count;
		}
	}

	#[Get("/next-track", "playlist.next-track")]
	public function nextTrack(): ?string
	{
		$playlist = session()->get("playlist_tracks");
		$playlist_index = session()->get("playlist_index");
		if ($playlist && count($playlist) > 0) {
			$next_index = $this->nextIndex($playlist, $playlist_index);
			if (isset($playlist[$next_index])) {
				session()->set("playlist_index", $next_index);
				$track = $playlist[$next_index] ?? null;
				return @json($track->uuid);
			}
		}
		return json(false);
	}

	#[Get("/prev-track", "playlist.prev-track")]
	public function previousTrack(): ?string
	{
		$playlist = session()->get("playlist_tracks");
		$playlist_index = session()->get("playlist_index");
		if ($playlist && count($playlist) > 0) {
			$prev_index = $this->prevIndex($playlist, $playlist_index);
			if (isset($playlist[$prev_index])) {
				session()->set("playlist_index", $prev_index);
				$track = $playlist[$prev_index] ?? null;
				return @json($track->uuid);
			}
		}
		return json(false);
	}
}

