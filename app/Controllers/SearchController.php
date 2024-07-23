<?php

namespace App\Controllers;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group, Post};
use App\Models\Track;

/**
 * Search controller
 * Provides track search functionality
 */
#[Group(prefix: "/search")]
class SearchController extends Controller
{
	/**
	 * Search index
	 */
	#[Get("/", "search.index", ["HX-Replace-Url=/search"])]
	public function index(): string
	{
		$content = template("muse/search/index.php", [
			"term" => session()->get("search_term"),
		]);

		return $this->render("layout/base.php", ["main" => $content]);
	}

	/**
	 * Load search results
	 */
	#[Get("/load", "search.load")]
	public function load(): ?string
	{
		$tracks = session()->get("search_tracks");
		if ($tracks) {
			return template("muse/search/results.php", ["tracks" => $tracks]);
		}
		return null;
	}

	/**
	 * Search tracks
	 */
	#[Post("/", "search.search")]
	public function search(): ?string
	{
		$term = $this->request()->get("term");
		if ($term) {
			$tracks = Track::search($term);
			if ($tracks) {
				session()->set("search_tracks", $tracks);
				session()->set("search_term", $term);
				return template("muse/search/results.php", ["tracks" => $tracks]);
			} else {
				return "<p class='mt-3'>No results found.</p>";
			}
		}
		session()->delete("search_term");
		return null;
	}

	/**
	 * Search by artist
	 */
	#[Get("/artist", "search.artist")]
	public function artist()
	{
		$artist = $this->request()->get("term");
		if ($artist) {
			$tracks = Track::search($artist, "artist");
			session()->set("search_tracks", $tracks);
		}
		hx_location("/search");
	}

	/**
	 * Set search results to playlist action
	 */
	#[Get("/playlist/all", "search.playlist-all")]
	public function play_all(): void
	{
		$tracks = session()->get("search_tracks");
		if ($tracks) {
			session()->set("playlist_tracks", $tracks);
			session()->delete("playlist_index");
			$this->clear();
		}
		hx_location("/playlist");
	}

	/**
	 * Reset the search, clear some session values
	 */
	#[Post("/reset", "search.reset")]
	public function reset()
	{
		$this->clear();
		return $this->index();
	}

	/**
	* Clear session values
	*/
	private function clear()
	{
		session()->delete("search_term");
		session()->delete("search_tracks");
	}
}
