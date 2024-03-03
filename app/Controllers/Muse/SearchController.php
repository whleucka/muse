<?php

namespace App\Controllers\Muse;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Post};

class SearchController extends Controller
{
	private function search(string $term): array|bool
	{
		// TODO use models here ?
		return db()->fetchAll("SELECT tracks.uuid, track_meta.*, '/img/no-album.png' as cover
			FROM tracks
			INNER JOIN track_meta ON track_meta.track_id = tracks.id
			WHERE title LIKE ? OR
			artist LIKE ? OR
			album LIKE ? OR
			genre LIKE ?
			ORDER BY artist,album,track_number", ...array_fill(0, 4, "$term%"));
	}

	#[Get("/search", "search.index", ["HX-Replace-Url=/search"])]
	public function index(): string
	{
		$content = template("muse/search/index.php", [
			"term" => session()->get("term")
		]);

        return $this->render("layout/base.php", ["main" => $content]);
	}

	#[Post("/search", "search.post")]
	public function post(): ?string
	{
		$data = $this->validateRequest([
			"term" => ["required"],
		]);
		if (isset($data["term"])) {
			// Set the search term
			session()->set("term", $data["term"]);
			$tracks = $this->search($data["term"]);
			return template("muse/search/results.php", ["tracks" => $tracks]);
		}
		// Clear search term
		session()->delete("term");
		return null;
	}

	#[Get("/search/playlist", "search.playlist", ["HX-Redirect=/playlist"])]
	public function playlist(): void
	{
		$term = session()->get("term");
		if ($term) {
			$tracks = $this->search($term);
			// This might have a limitation
			session()->set("playlist", $tracks);
		}
	}
}
