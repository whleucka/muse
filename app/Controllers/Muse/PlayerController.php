<?php

namespace App\Controllers\Muse;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group};

#[Group(prefix: "/player")]
class PlayerController extends Controller
{
	private function nextIndex(array $playlist, int $current_index): int
	{
		// Defaults
		if (!session()->has("shuffle")) session()->set("shuffle", true);
		if (!session()->has("repeat")) session()->set("repeat", true);

		$shuffle = session()->get("shuffle") === true;
		$repeat = session()->get("repeat") === true; // wip

		$playlist_count = count($playlist);

		if ($shuffle) $current_index = rand(0, 1000);
		return (intval($current_index) + 1) % $playlist_count;
	}

	private function prevIndex(array $playlist, int $current_index): int
	{
		// Defaults
		if (!session()->has("shuffle")) session()->set("shuffle", true);
		if (!session()->has("repeat")) session()->set("repeat", true);

		$shuffle = session()->get("shuffle") === true;
		$repeat = session()->get("repeat") === true; // wip

		$playlist_count = count($playlist);

		if ($shuffle) $current_index = rand(0, 1000);
		return intval($current_index - 1 + $playlist_count) % $playlist_count;
	}

	#[Get("/shuffle", "player.shuffle", ["api"])]
	public function shuffle(): int
	{
		$shuffle = session()->get("shuffle") === true;
		return $shuffle ? 1 : 0;
	}

	#[Get("/repeat", "player.repeat", ["api"])]
	public function repeat(): int
	{
		$repeat = session()->get("repeat") === true;
		return $repeat ? 1 : 0;
	}

	#[Get("/shuffle/toggle", "player.shuffle-toggle", ["api"])]
	public function shuffleToggle(): int
	{
		if (!session()->has("shuffle")) session()->set("shuffle", true);
		$shuffle = session()->get("shuffle") === true;
		session()->set("shuffle", !$shuffle);
		return !$shuffle ? 1 : 0;
	}

	#[Get("/repeat/toggle", "player.repeat-toggle", ["api"])]
	public function repeatToggle(): int
	{
		if (!session()->has("repeat")) session()->set("repeat", true);
		$shuffle = session()->get("repeat") === true;
		session()->set("repeat", !$shuffle);
		return !$shuffle ? 1 : 0;
	}

	#[Get("/next-track", "player.next-track", ["api"])]
	public function nextTrack(): ?array
	{
		$playlist = session()->get("playlist_tracks");
		$playlist_index = session()->get("playlist_index");
		if ($playlist && count($playlist) > 0) {
			$next_index = $this->nextIndex($playlist, $playlist_index);
			if (isset($playlist[$next_index])) {
				session()->set("playlist_index", $next_index);
				$track = $playlist[$next_index] ?? null;
				return [
					"track" => $track,
					"index" => $playlist_index
				];
			}
		}
		return null;
	}

	#[Get("/prev-track", "player.prev-track", ["api"])]
	public function previousTrack(): ?array
	{
		$playlist = session()->get("playlist_tracks");
		$playlist_index = session()->get("playlist_index");
		if ($playlist && count($playlist) > 0) {
			$prev_index = $this->prevIndex($playlist, $playlist_index);
			if (isset($playlist[$prev_index])) {
				session()->set("playlist_index", $prev_index);
				$track = $playlist[$prev_index] ?? null;
				return [
					"track" => $track,
					"index" => $playlist_index
				];
			}
		}
		return null;
	}
}
