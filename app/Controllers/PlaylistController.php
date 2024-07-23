<?php

namespace App\Controllers;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group, Post};
use App\Models\Track;

/**
 * Playlist controller
 * Current playlist of tracks
 */
#[Group(prefix: "/playlist")]
class PlaylistController extends Controller
{
	/**
	 * Playlist index
	 */
    #[Get("/", "playlist.index", ["HX-Replace-Url=/playlist"])]
    public function index(): string
    {
        $content = template("muse/playlist/index.php");

        return $this->render("layout/base.php", ["main" => $content]);
    }

	/**
	 * Load playlist tracks
	 */
    #[Get("/load", "playlist.load")]
    public function load(): string
    {
        $playlist = session()->get("playlist_tracks");
        $has_tracks = db()->fetch("SELECT * FROM tracks LIMIT 1");

        return template(
            "muse/playlist/results.php",
            [
                "tracks" => $playlist ?? [],
                "has_tracks" => $has_tracks
            ]
        );
    }

	/**
	 * Generate random playlist
	 */
    #[Get("/random", "playlist.random")]
    public function random(): string
    {
        $tracks = Track::random();
        if ($tracks) {
            session()->set("playlist_tracks", $tracks);
            session()->set("playlist_index", 0);
        }
        return $this->index();
    }

	/**
	 * Set playlist index
	 */
    #[Post("/index", "playlist.set-index", ["api"])]
    public function setIndex(): void
    {
        $index = $this->request()->get("index");
        session()->set("playlist_index", $index);
    }

	/**
	 * Get playlist track
	 */
    #[Get("/track", "playlist.track", ["api"])]
    public function track(): ?array
    {
        $playlist = session()->get("playlist_tracks");
        $playlist_index = session()->get("playlist_index");
        return isset($playlist[$playlist_index])
            ? ["track" => $playlist[$playlist_index], "index" => $playlist_index]
            : null;
    }

	/**
	 * Reset playlist, clear session value
	 */
    #[Get("/reset", "playlist.reset")]
    public function reset(): string
    {
        session()->delete("playlist_tracks");
        return $this->index();
    }
}
