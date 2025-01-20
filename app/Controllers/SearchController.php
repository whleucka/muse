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
			"term" => session()->get("search_term") ?? $this->request()->get("term"),
		]);

		return $this->render("layout/base.php", ["main" => $content]);
	}

	/**
	 * Load search results
	 */
	#[Get("/load", "search.load")]
	public function load(): ?string
	{
		$term = $this->request()->get("term");
		if ($term) {
			$tracks = Track::search($term);
			if ($tracks) {
				session()->set("search_tracks", $tracks);
				session()->set("search_term", $term);
			}
        } else {
		    $tracks = session()->get("search_tracks") ?? [];
        }
		return template("muse/search/results.php", ["tracks" => $tracks]);
	}

	/**
	 * Search tracks
	 */
	#[Get("/query", "search.search")]
	public function search(): ?string
	{
		$term = $this->request()->get("term");
		if ($term) {
			$tracks = Track::search($term);
			if ($tracks) {
				session()->set("search_tracks", $tracks);
				session()->set("search_term", $term);
			}
        } else {
		    session()->delete("search_tracks");
        }
		session()->delete("search_term");
        return $this->index();
	}

	/**
	 * Search by artist
	 */
	#[Get("/artist/{uuid}", "search.artist")]
	public function artist(string $uuid)
	{
        $track = Track::findByAttribute("uuid", $uuid);
		if ($track) {
			$tracks = Track::search($track->meta()->artist, "artist");
			session()->set("search_tracks", $tracks);
		}
		hx_location("/search");
	}

	/**
	 * Search by album
	 */
	#[Get("/album/{uuid}", "search.album")]
	public function album(string $uuid)
	{
        $track = Track::findByAttribute("uuid", $uuid);
		if ($track) {
			$tracks = Track::search($track->meta()->album, "album");
			session()->set("search_tracks", $tracks);
		}
		hx_location("/search");
	}

	/**
	 * Search by directory
	 */
	#[Get("/directory/{uuid}", "search.directory")]
	public function directory(string $uuid)
	{
        $track = Track::findByAttribute("uuid", $uuid);
		if ($track) {
            $dir = dirname($track->name);
			$tracks = Track::search($dir, "directory");
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

		    $shuffle = session()->get("shuffle") === true;
            if (!$shuffle) {
                // Reset to top of playlist
			    session()->delete("playlist_index");
            }
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
